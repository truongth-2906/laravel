<?php

namespace App\Domains\Notification\Models\Traits\Method;

use App\Domains\Auth\Models\User;
use App\Domains\Job\Models\Job;

/**
 * Class NotificationMethod.
 */
trait NotificationMethod
{
    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return optional($this->receiverPivot)->is_read;
    }

    /**
     * @return bool
     */
    public function isJobApplyType(): bool
    {
        return $this->type == $this::JOB_APPLY_TYPE;
    }

    /**
     * @return bool
     */
    public function isJobDoneType(): bool
    {
        return $this->type == $this::JOB_DONE_TYPE;
    }

    /**
     * @return bool
     */
    public function isPaymentType(): bool
    {
        return $this->type == $this::PAYMENT_TYPE;
    }

    /**
     * @return string
     */
    public function getTitleAttribute()
    {
        /** @var \App\Domains\Auth\Models\User */
        $user = User::find($this->receiverPivot->user_id);
        $title = __('Notification');

        if ($user && $user->isEmployer()) {
            switch ($this->type) {
                case $this::JOB_APPLY_TYPE:
                    $title = __('New job application from :freelancer_name.', ['freelancer_name' => optional($this->sender)->name ?? '']);
                    break;

                case $this::PAYMENT_TYPE:
                    $title = __('Escrow payment to :freelancer_name has been accepted.', ['freelancer_name' => optional($this->sender)->name ?? '']);
                    break;
            }
        } else if ($user && $user->isFreelancer()) {
            switch ($this->type) {
                case $this::JOB_DONE_TYPE:
                    $title = __('New job application from :employer_name.', ['employer_name' => optional($this->sender)->name ?? '']);
                    break;

                case $this::PAYMENT_TYPE:
                    $title = __('New payment received from :employer_name.', ['employer_name' => optional($this->sender)->name ?? '']);
                    break;
            }
        }

        return $title;
    }

    /**
     * @return string
     */
    public function getIconAttribute()
    {
        $iconUrl = '';

        switch ($this->type) {
            case $this::JOB_DONE_TYPE:
            case $this::JOB_APPLY_TYPE:
                $iconUrl = '/img/icon-favorite-list.svg';
                break;

            case $this::PAYMENT_TYPE:
                $iconUrl = '/img/icon-money.svg';
                break;

            default:
                $iconUrl = '/img/icon-info-circle.svg';
                break;
        }

        return $iconUrl;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getActionsAttribute()
    {
        $actions = collect();

        /** @var \App\Domains\Auth\Models\User */
        $user = User::find($this->receiverPivot->user_id);

        if ($user && $user->isEmployer()) {
            switch ($this->type) {
                case $this::JOB_APPLY_TYPE:
                    $actions->put('title', __('View application'));
                    $actions->put(
                        'route',
                        notificationRoute('read', [
                            'notification' => $this->id,
                            'redirect_to' => $this->notifiable instanceof Job ? route('frontend.employer.jobs.applications', ['job' => $this->notifiable_id])  : '#',
                        ], $user->type)
                    );
                    break;
                case $this::PAYMENT_TYPE:
                    $actions->put('title', __('Go to billing & payments'));
                    $actions->put(
                        'route',
                        notificationRoute('read', [
                            'notification' => $this->id,
                            'redirect_to' => route(EMPLOYER_PAYMENT_INDEX)
                        ])
                    );
                    break;
            }
        } else if ($user && $user->isFreelancer()) {
            switch ($this->type) {
                case $this::JOB_DONE_TYPE:
                    $actions->put('title', __('View application'));
                    $actions->put(
                        'route',
                        notificationRoute('read', [
                            'notification' => $this->id,
                            'redirect_to' => $this->notifiable instanceof Job ? route(FREELANCER_JOB_DONE_PREVIEW, ['job' => $this->notifiable_id])  : '#',
                        ], $user->type)
                    );
                    break;
                case $this::PAYMENT_TYPE:
                    $actions->put('title', __('Go to billing & payments'));
                    $actions->put(
                        'route',
                        notificationRoute('read', [
                            'notification' => $this->id,
                            'redirect_to' => route('frontend.freelancer.payments.index')
                        ])
                    );
                    break;
            }
        }

        return $actions;
    }
}
