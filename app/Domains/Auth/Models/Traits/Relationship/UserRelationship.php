<?php

namespace App\Domains\Auth\Models\Traits\Relationship;

use App\Domains\Auth\Models\PasswordHistory;
use App\Domains\Category\Models\Category;
use App\Domains\Company\Models\Company;
use App\Domains\Country\Models\Country;
use App\Domains\Experience\Models\Experience;
use App\Domains\Job\Models\Job;
use App\Domains\JobApplication\Models\JobApplication;
use App\Domains\Message\Models\Message;
use App\Domains\MessageReaction\Models\MessageReaction;
use App\Domains\Notification\Models\Notification;
use App\Domains\Saved\Models\Saved;
use App\Domains\Sector\Models\Sector;
use App\Domains\Timezone\Models\Timezone;
use App\Domains\Portfolio\Models\Portfolio;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\Voucher\Models\Voucher;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class UserRelationship.
 */
trait UserRelationship
{
    /**
     * @return mixed
     */
    public function passwordHistories()
    {
        return $this->morphMany(PasswordHistory::class, 'model');
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function timezone(): BelongsTo
    {
        return $this->belongsTo(Timezone::class);
    }

    /**
     * @return BelongsTo
     */
    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    /**
     * @return BelongsTo
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * @return HasMany
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    /**
     * @return HasOne
     */
    public function verifyUser(): HasOne
    {
        return $this->hasOne('App\VerifyUser');
    }

    /**
     * @return BelongsToMany
     */
    public function jobApplications(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_applications', 'user_id', 'job_id')->withPivot('status')->withTimestamps()->as('application');
    }

    /**
     * @return HasMany
     */
    public function jobsSaved(): HasMany
    {
        return $this->hasMany(Saved::class, 'user_id', 'id')->where('saved_type', Saved::TYPE_JOB);
    }

    /**
     * @return HasMany
     */
    public function freelancersSaved(): HasMany
    {
        return $this->hasMany(Saved::class, 'user_id', 'id')->where('saved_type', Saved::TYPE_FREELANCER);
    }

    /**
     * @return MorphMany
     */
    public function savers(): MorphMany
    {
        return $this->morphMany(Saved::class, 'savedable', 'saved_type', 'saved_id');
    }

    /**
     * @return BelongsTo
     */
    public function utc(): BelongsTo
    {
        return $this->belongsTo(Timezone::class, 'timezone_id');
    }

    /**
     * @return HasMany
     */
    public function notificationsSent(): HasMany
    {
        return $this->hasMany(Notification::class, 'sender_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function notificationsReceived(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class, 'notification_user', 'user_id', 'notification_id')
            ->whereNull('notification_user.deleted_at')
            ->withPivot('is_read')
            ->withTimestamps()
            ->as('notification_pivot');
    }

    /**
     * @return HasMany
     */
    public function messagesSent(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function messagesReceived(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id', 'id');
    }

    public function latestMessageTo()
    {
        return $this->hasOne(Message::class, 'sender_id')->where('receiver_id', auth()->id())->latest();
    }

    public function latestMessageFrom()
    {
        return $this->hasOne(Message::class, 'receiver_id')->where('sender_id', auth()->id())->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'sender_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedTransaction(): HasMany
    {
        return $this->hasMany(Transaction::class, 'receiver_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function callingCode(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'calling_code_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobApplicationsDetail(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function vouchersUsed(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class, 'saved_vouchers', 'user_id', 'voucher_id')
            ->as('saved_info')
            ->withPivot('status', 'deleted_at')
            ->withTimestamps()
            ->wherePivotNull('deleted_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vouchersCreated(): HasMany
    {
        return $this->hasMany(Voucher::class, 'user_id', 'id');
    }
}
