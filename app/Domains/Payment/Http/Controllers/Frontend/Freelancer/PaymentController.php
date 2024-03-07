<?php

namespace App\Domains\Payment\Http\Controllers\Frontend\Freelancer;

use App\Domains\Auth\Services\UserService;
use App\Domains\Payment\Http\Requests\AddEscrowAccountRequest;
use App\Domains\Transaction\Services\TransactionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var TransactionService
     */
    protected TransactionService $transactionService;

    /**
     * @param UserService $userService
     * @param TransactionService $transactionService
     */
    public function __construct(
        UserService $userService,
        TransactionService $transactionService
    ) {
        $this->userService = $userService;
        $this->transactionService = $transactionService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $escrowEmail = auth()->user()->escrow_email;
        $transactions = $this->transactionService->getListTransactionByFreelancer($request->query('orderBy'));
        return view('frontend.freelancer.payment.index', compact('escrowEmail', 'transactions'));
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function addEscrowAccount()
    {
        $escrowEmail = auth()->user()->escrow_email;

        return view('frontend.freelancer.payment.add_escrow_account', compact('escrowEmail'));
    }

    /**
     * @param AddEscrowAccountRequest $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function storeEscrowAccount(AddEscrowAccountRequest $request)
    {
        try {
            $this->userService->addEscrowEmail($request->get('email'));
            if ($request->query('job_id')) {
                return redirect()->route('frontend.freelancer.job-detail', ['id' => $request->query('job_id')])->with('message', __('Add your Escrow account successfully. Please apply job!'));
            }
            return redirect()->route('frontend.freelancer.payments.index')->with('message', __('Add your Escrow account successfully.'));
        } catch (Exception $e) {
            $message = __('Add your Escrow account failed.');

            if ($e->getCode() == Response::HTTP_NOT_FOUND) {
                $message = __('No account found on Escrow. Please double check the information entered.');
            }
            return redirect()->back()->withInput()->with('error', $message);
        }
    }
}
