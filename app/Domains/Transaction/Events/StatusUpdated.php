<?php

namespace App\Domains\Transaction\Events;

use App\Domains\Transaction\Models\Transaction;
use Illuminate\Queue\SerializesModels;

/**
 * Class StatusUpdated.
 */
class StatusUpdated
{
    use SerializesModels;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @var string|int
     */
    public $status;

    /**
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction, $status)
    {
        $this->transaction = $transaction;
        $this->status = $status;
    }
}
