<?php

namespace App\Domains\Job\Models\Traits\Method;

use App\Domains\Escrow\Services\EscrowService;

/**
 * Class JobMethod.
 */
trait JobMethod
{
    /**
     * @return bool
     */
    public function isAuthor(): bool
    {
        return $this->user_id === auth()->id();
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->status === $this::STATUS_OPEN;
    }

    /**
     * @return bool
     */
    public function isSaved(): bool
    {
        return !is_null($this->mySaved);
    }

    /**
     * @return bool
     */
    public function isMarkDone(): bool
    {
        return $this->mark_done == $this::MARK_DONE;
    }

    /**
     * @return float
     */
    public function getFreelanceServiceFeePayAttribute(): float
    {
        return $this->wage ? round($this->wage * EscrowService::BROKERAGE_FEE_PERCENTAGE_WITH_FREELANCER, 2) : 0;
    }

    /**
     * @return float
     */
    public function getEscrowFeeAttribute(): float
    {
        if (!$this->wage) {
            return 0;
        }

        if ($this->wage >= 25000.01) {
            return round($this->wage * $this::ESCROW_FEE_PERCENTAGE_3, 2);
        }

        if ($this->wage >= 5000.01 && $this->wage <= 25000) {
            return $this::ESCROW_FEE_2 + round(($this->wage - 5000) * $this::ESCROW_FEE_PERCENTAGE_2, 2);
        }

        if ($this->wage > 0 && $this->wage <= 5000) {
            return round($this->wage * $this::ESCROW_FEE_PERCENTAGE_1, 2);
        }
    }

    /**
     * @return float
     */
    public function getTotalReceivedAfterAttribute(): float
    {
        return $this->wage ? round($this->wage - $this->getFreelanceServiceFeePayAttribute() - $this->getEscrowFeeAttribute(), 2) : 0;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getFieldAllowSort(string $key)
    {
        if (array_key_exists($key, $this::FIELDS_ALLOWED_SORT)) {
            return $this::FIELDS_ALLOWED_SORT[$key];
        }

        return false;
    }
}
