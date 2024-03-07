<?php

namespace App\Domains\Transaction\Models\Traits\Method;

/**
 * Class TransactionMethod.
 */
trait TransactionMethod
{
    /**
     * @return bool
     */
    public function isComplete()
    {
        return in_array($this->status, [
            $this::COMPLETE,
            $this::PAYMENT_DISBURSED,
            $this::ACCEPT
        ]);
    }

    /**
     * @return bool
     */
    public function isProcessing()
    {
        return !$this->isComplete();
    }

    /**
     * @return bool
     */
    public function isCancel()
    {
        return $this->status == $this::CANCELED;
    }

    /**
     * @return bool
     */
    public function isHasCancel()
    {
        return in_array($this->status, [
            $this::CREATE,
            $this::PAYMENT_APPROVED
        ]);
    }

    /**
     * @return bool
     */
    public function isPayNow()
    {
        return $this->status == $this::PAYMENT_APPROVED || in_array($this->status, [$this::SHIP, $this::RECEIVER]);
    }

    /**
     * @return bool
     */
    public function isCreated()
    {
        return $this->status == $this::CREATE;
    }

    /**
     * @return bool
     */
    public function isReceived()
    {
        return in_array($this->status, [
            $this::COMPLETE,
            $this::PAYMENT_DISBURSED
        ]);
    }

    /**
     * @return bool
     */
    public function isVisibleRecipient()
    {
        return $this->is_visible_recipient;
    }
}
