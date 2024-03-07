<?php

namespace App\Domains\Voucher\Models\Traits\Relationship;

use App\Domains\Auth\Models\User;
use App\Domains\Voucher\Models\HistoryUseVoucher;
use App\Domains\Voucher\Models\Voucher;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait SavedVoucherRelationship
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function historiesUsed(): HasMany
    {
        return $this->hasMany(HistoryUseVoucher::class, 'saved_voucher_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
