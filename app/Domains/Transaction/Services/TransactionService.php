<?php

namespace App\Domains\Transaction\Services;

use App\Domains\Auth\Models\User;
use App\Domains\Escrow\Services\EscrowService;
use App\Domains\Job\Models\Job;
use App\Domains\JobApplication\Models\JobApplication;
use App\Domains\Notification\Services\NotificationService;
use App\Domains\Transaction\Events\StatusUpdated;
use App\Domains\Transaction\Models\Transaction;
use App\Services\BaseService;
use DB;
use Exception;
use Illuminate\Http\Response;
use Log;

/**
 * Class TransactionService.
 */
class TransactionService extends BaseService
{
    /** @var EscrowService */
    protected $escrowService;

    /**
     * @var NotificationService
     */
    protected NotificationService $notificationService;

    /**
     * @param Transaction $transaction
     * @param EscrowService $escrowService
     */
    public function __construct(
        Transaction         $transaction,
        EscrowService       $escrowService,
        NotificationService $notificationService
    ) {
        $this->model = $transaction;
        $this->escrowService = $escrowService;
        $this->notificationService = $notificationService;
    }

    /**
     * @param Job $job
     * @param User $freelancer
     * @return mixed
     * @throws \Throwable
     */
    public function createWhenApprovedFreelancer(Job $job, User $freelancer)
    {
        try {
            $response = $this->escrowService->createTransaction([
                'freelancer_id' => $freelancer->id,
                'freelancer_email' => $freelancer->escrow_email,
                'employer_email' => $job->user->escrow_email,
                'employer_id' => $job->user->id,
                'currency' => config('escrow.default_default_currency'),
                'job_id' => $job->id,
                'job_name' => $job->name,
                'freelancer_name' => $freelancer->name,
                'amount' => $job->wage,
            ]);

            $transactionDetail = $this->escrowService->getTransactionDetail($response->get('transaction_id'), $job->user->escrow_email);
            $amounts = $this->calculateTheAmounts($transactionDetail['items']);
            $item_escrow_id = $transactionDetail['items'][0]['id'];
            throw_if(
                !$response ||
                    !$this->model->create([
                        'sender_id' => $job->user_id,
                        'receiver_id' => $freelancer->id,
                        'job_id' => $job->id,
                        'escrow_transaction_id' => $response->get('transaction_id'),
                        'item_escrow_id' => $item_escrow_id,
                        'reference' => $response->get('reference'),
                        'status' => Transaction::CREATE,
                        'amount_sender' => $amounts['amount_sender'],
                        'amount_receiver' => $amounts['amount_receiver'],
                        'currency' => $transactionDetail['currency'],
                    ]),
                Exception::class,
                'Create transaction failed.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );

            return $response->get('landing_page');
        } catch (Exception $e) {
            if ($response) {
                $this->escrowService->cancelTransaction($response->get('transaction_id'));
            }
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param array $data
     * @return bool
     */
    public function updateTransactionStatus(array $data)
    {
        if (array_key_exists('transaction_id', $data)) {
            $transaction = $this->model
                ->where('escrow_transaction_id', $data['transaction_id'])
                ->where('status', '!=', $data['event'])
                ->first();

            throw_if(!$transaction, Exception::class, 'Transaction not found.', Response::HTTP_NOT_FOUND);

            $updateAttributes = [
                'status' => $data['event'] ?? $transaction->status
            ];
            if (!$transaction->isVisibleRecipient() && $data['event'] == Transaction::PAYMENT_APPROVED) {
                $updateAttributes['is_visible_recipient'] = true;
            }

            throw_if(
                !$result = $transaction->update($updateAttributes),
                Exception::class,
                'Update status failed.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );

            if ($data['event'] == Transaction::PAYMENT_APPROVED || $data['event'] == Transaction::PAYMENT_REJECTED) {
                event(new StatusUpdated($transaction, $data['event'] == Transaction::PAYMENT_APPROVED ? JobApplication::STATUS_APPROVE : JobApplication::STATUS_PENDING));
            }

            if ($data['event'] == Transaction::PAYMENT_APPROVED) {
                $this->notificationService->createByPaymentApproved($transaction);
            }

            if ($data['event'] == Transaction::PAYMENT_DISBURSED) {
                $this->notificationService->createByPayment($transaction);
            }

            return $result;
        }

        return false;
    }

    /**
     * @param int $jobId
     * @param mixed ...$receiverId
     * @return bool
     */
    public function cancelTransactionWhenJobDone(int $jobId, ...$receiverId)
    {
        $statuses = [Transaction::CANCELED, Transaction::PAYMENT_APPROVED, Transaction::COMPLETE];

        return $this->cancelTransactionsByJob($jobId, $statuses, ...$receiverId);
    }

    /**
     * @param string|null $dueDateOrder
     * @return mixed
     */
    public function getListEscrowTransactionEmployer($dueDateOrder = null)
    {
        return $this->model
            ->select(
                'transactions.*',
                'jobs.due_date as due_date'
            )
            ->join('jobs', 'jobs.id', '=', 'transactions.job_id')
            ->where('sender_id', auth()->id())
            ->with('job', 'sender', 'receiver')
            ->when($dueDateOrder, function ($e, $dueDateOrder) {
                $e->orderBy('due_date', $this->handleOrderBy($dueDateOrder));
            })
            ->orderBy('id', 'DESC')
            ->paginate(config('paging.quantity'));
    }

    /**
     * @param string|null $dueDateOrder
     * @return mixed
     */
    public function getListTransactionByFreelancer($dueDateOrder = null)
    {
        return $this->model->where('receiver_id', auth()->id())
            ->select(
                'transactions.*',
                'jobs.due_date as due_date'
            )
            ->where('is_visible_recipient', true)
            ->join('jobs', 'jobs.id', '=', 'transactions.job_id')
            ->with('job', 'sender', 'receiver')
            ->when($dueDateOrder, function ($e, $dueDateOrder) {
                $e->orderBy('due_date', $this->handleOrderBy($dueDateOrder));
            })
            ->orderBy('id', 'DESC')
            ->paginate(config('paging.quantity'));
    }

    /**
     * @param int $id
     * @return bool
     */
    public function cancel(int $id, string $message)
    {
        try {
            DB::beginTransaction();
            $transaction = $this->model->where('id', $id)
                ->where('sender_id', auth()->id())
                ->where('status', '!=', Transaction::CANCELED)
                ->first();

            throw_if(!$transaction, Exception::class, 'Transaction not found.', Response::HTTP_NOT_FOUND);

            $transaction->update([
                'status' => Transaction::CANCELED
            ]);
            event(new StatusUpdated($transaction, JobApplication::STATUS_PENDING));

            throw_if(
                !$this->escrowService->cancelTransaction($transaction->escrow_transaction_id, $message),
                Exception::class,
                'Cancel transaction failed.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }


    /**
     * @param $id
     * @return bool
     * @throws \Throwable
     */
    public function payNowTransaction($id)
    {
        $transaction = $this->model->where('id', $id)
            ->where('sender_id', auth()->id())
            ->whereNotIn('status', [Transaction::CANCELED, Transaction::COMPLETE, Transaction::CREATE])
            ->with('sender:id,escrow_email', 'receiver:id,escrow_email')
            ->first();

        throw_if(
            !$transaction ||
                !$this->escrowService->payNow($transaction),
            Exception::class,
            !$transaction ? 'Transaction not found.' : 'Pay now failed.',
            !$transaction ? Response::HTTP_NOT_FOUND : Response::HTTP_INTERNAL_SERVER_ERROR
        );

        return true;
    }

    /**
     * @param int $id
     * @return string
     */
    public function getFundingPage(int $id)
    {
        $transaction = $this->model->where('id', $id)
            ->where('sender_id', auth()->id())
            ->where('status', Transaction::CREATE)
            ->with('sender:id,escrow_email')
            ->first();

        throw_if(
            !$transaction ||
                !$url = $this->escrowService->getFundingPage($transaction->sender->escrow_email, $transaction->reference),
            Exception::class,
            !$transaction ? 'Transaction not found.' : 'Pay now failed.',
            !$transaction ? Response::HTTP_NOT_FOUND : Response::HTTP_INTERNAL_SERVER_ERROR
        );

        return $url;
    }

    /**
     * @param array|\Illuminate\Support\Collection $transactionItems
     * @return array
     */
    protected function calculateTheAmounts($transactionItems)
    {
        $amount = $transactionItems[0]['schedule'][0]['amount'] ?? 0;
        $amount = $amount < 0 ? 0 : $amount;
        $escrowFee = $transactionItems[0]['fees'][0]['amount'] ?? 0;
        $employerBrokerageFee = $transactionItems[1]['schedule'][0]['amount']  ?? 0;
        $freelancerBrokerageFee = $transactionItems[2]['schedule'][0]['amount']  ?? 0;

        if ($amount == 0) {
            return [
                'amount_sender' => $employerBrokerageFee + $escrowFee,
                'amount_receiver' => $freelancerBrokerageFee + $escrowFee,
            ];
        } else {
            return [
                'amount_sender' => $amount + $employerBrokerageFee + $escrowFee,
                'amount_receiver' => $amount - $freelancerBrokerageFee - $escrowFee,
            ];
        }
    }

    /**
     * @param int $jobId
     * @param array $statuses
     * @param mixed ...$receiverId
     * @return bool
     */
    public function cancelTransactionsByJob(int $jobId, array $statuses, ...$receiverId)
    {
        $transactions = $this->model->where('job_id', $jobId)
            ->whereIn('receiver_id', $receiverId)
            ->whereNotIn('status', $statuses)
            ->get();

        return $this->cancelTransactions($transactions);
    }

    /**
     * @param int $jobId
     * @param mixed ...$receiverId
     * @return bool
     */
    public function cancelTransactionWhenJobDeleted(int $jobId, ...$receiverId)
    {
        $statuses = [
            Transaction::COMPLETE,
            Transaction::PAYMENT_DISBURSED,
            Transaction::CANCELED
        ];

        return $this->cancelTransactionsByJob($jobId, $statuses, ...$receiverId);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function cancelTransactionWhenUserHidden(User $user)
    {
        $statuses = [
            Transaction::COMPLETE,
            Transaction::PAYMENT_DISBURSED,
            Transaction::CANCELED
        ];

        $transactions = $this->model
            ->whereHas('receiver', function ($e) use ($user) {
                $e->where('id', $user->id)->where('is_hidden', true);
            })
            ->whereNotIn('status', $statuses)
            ->get();

        return $this->cancelTransactions($transactions);
    }

    /**
     * @param array|\Illuminate\Support\Collection $transactions
     * @return bool
     */
    public function cancelTransactions($transactions)
    {
        $transactionsCancelSuccess = [];

        foreach ($transactions as $transaction) {
            if ($this->escrowService->cancelTransaction($transaction->escrow_transaction_id)) {
                $transactionsCancelSuccess[] = $transaction->id;
            }
        }

        try {
            if (count($transactionsCancelSuccess)) {
                $this->model->whereIn('id', $transactionsCancelSuccess)->update([
                    'status' => Transaction::CANCELED
                ]);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        return true;
    }
}
