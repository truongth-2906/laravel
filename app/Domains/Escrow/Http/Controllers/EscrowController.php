<?php

namespace App\Domains\Escrow\Http\Controllers;

use App\Domains\Transaction\Services\TransactionService;
use Exception;
use Illuminate\Http\Request;
use Log;

class EscrowController
{
    /** @var TransactionService */
    protected $transactionService;

    /**
     * PaymentController constructor
     *
     * @param TransactionService $transactionService
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function webhookCallback(Request $request)
    {
        try {
            $this->transactionService->updateTransactionStatus($request->only('event', 'transaction_id'));
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
