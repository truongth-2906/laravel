<?php

namespace App\Domains\Voucher\Models\Traits\Relationship;

use App\Domains\Transaction\Models\Transaction;
use App\Domains\Voucher\Models\SavedVoucher;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HistoryUsedVoucherRelation
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function savedInfo(): BelongsTo
    {
        return $this->belongsTo(SavedVoucher::class, 'saved_voucher_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }
}
