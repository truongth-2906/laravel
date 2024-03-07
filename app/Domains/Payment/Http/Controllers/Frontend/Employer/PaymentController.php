<?php

namespace App\Domains\Payment\Http\Controllers\Frontend\Employer;

use App\Domains\Auth\Services\UserService;
use App\Domains\Escrow\Services\EscrowService;
use App\Domains\Transaction\Services\TransactionService;
use App\Domains\Payment\Http\Requests\AddEscrowAccountRequest;
use App\Domains\Payment\Http\Requests\CancelMessageRequest;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

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
     * @var EscrowService
     */
    protected $escrowService;

    /**
     * @param UserService  $userService
     * @param TransactionService $transactionService
     * @param EscrowService $escrowService
     */
    public function __construct(
        UserService  $userService,
        TransactionService $transactionService,
        EscrowService $escrowService
    ) {
        $this->userService = $userService;
        $this->transactionService = $transactionService;
        $this->escrowService = $escrowService;
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        $escrowEmail = auth()->user()->escrow_email;
        $escrowTransaction = $this->transactionService->getListEscrowTransactionEmployer($request->query('orderBy'));

        return view('frontend.employer.payment.index', compact('escrowEmail', 'escrowTransaction'));
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function addEscrowAccount()
    {
        $escrowEmail = auth()->user()->escrow_email;

        return view('frontend.employer.payment.add_escrow_account', compact('escrowEmail'));
    }

    /**
     * @param AddEscrowAccountRequest $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function storeEscrowAccount(AddEscrowAccountRequest $request)
    {
        try {
            $this->userService->addEscrowEmail($request->get('email'));
            $this->escrowService->createCustomer($request->get('email'));
            if ($request->query('job_id')) {
                return redirect()->route('frontend.employer.jobs.applications', $request->query('job_id'))->with('message', __('Add your Escrow account successfully. Please approved freelancer!'));
            }
            return redirect()->route('frontend.employer.payments.index')->with('message', __('Add your Escrow account successfully.'));
        } catch (Exception $e) {
            $message = __('Add your Escrow account failed.');

            if ($e->getCode() == Response::HTTP_NOT_FOUND) {
                $message = __('No account found on Escrow. Please double check the information entered.');
            }
            return redirect()->back()->withInput()->with('error', $message);
        }
    }

    /**
     * @param $id
     * @param CancelMessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel($id, CancelMessageRequest $request)
    {
        try {
            $this->transactionService->cancel($id, $request->get('message'));

            return response()->json([
                'message' => __('Cancel transaction success.')
            ], Response::HTTP_OK);
        } catch (Exception $e) {

            return response()->json([
                'message' => __($e->getCode() == Response::HTTP_NOT_FOUND ? 'Transaction not found.' : 'Cancel transaction failed.'),
                'error' => true
            ], $e->getCode() == Response::HTTP_NOT_FOUND ? Response::HTTP_NOT_FOUND : Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function payNow($id)
    {
        try {
            $this->transactionService->payNowTransaction($id);
            return response()->json([
                'message' => __('Pay transaction successfully.')
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'message' => __($e->getCode() == Response::HTTP_NOT_FOUND ? 'Transaction not found.' : 'Pay transaction failed.'),
                'error' => true
            ], $e->getCode() == Response::HTTP_NOT_FOUND ? Response::HTTP_NOT_FOUND : Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function fundingTransaction($id)
    {
        try {
            $url = $this->transactionService->getFundingPage($id);

            if (request()->ajax()) {
                return response()->json([
                    'redirect_url' => $url
                ], Response::HTTP_OK);
            }

            return redirect($url);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => true,
            ], $e->getCode() == Response::HTTP_NOT_FOUND ? Response::HTTP_NOT_FOUND : Response::HTTP_INTERNAL_SERVER_ERROR);


            return redirect()->back()->with('error', __('An error has occurred'));
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function waitForRedirect(Request $request)
    {
        $paymentsRoute = route(EMPLOYER_PAYMENT_INDEX);
        $redirectRoute = $paymentsRoute;

        if ($request->job) {
            $redirectRoute = route('frontend.employer.jobs.applications', ['job' => $request->job]);
        }

        return view('frontend.employer.payment.wait_for_redirect', compact('redirectRoute', 'paymentsRoute'));
    }
}
