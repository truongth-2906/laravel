<?php

namespace App\Domains\Voucher\Models\Traits\Relationship;

use App\Domains\Auth\Models\User;
use App\Domains\Voucher\Models\SavedVoucher;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait VoucherRelationship
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saves(): HasMany
    {
        return $this->hasMany(SavedVoucher::class, 'voucher_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function savers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_vouchers', 'voucher_id', 'user_id')
            ->as('saved_info')
            ->withPivot('status', 'deleted_at')
            ->withTimestamps()
            ->wherePivotNull('deleted_at');
    }
}
