<?php

namespace App\Domains\Job\Models\Traits\Relationship;

use App\Domains\Category\Models\Category;
use App\Domains\Company\Models\Company;
use App\Domains\Country\Models\Country;
use App\Domains\Experience\Models\Experience;
use App\Domains\Auth\Models\User;
use App\Domains\Notification\Models\Notification;
use App\Domains\Saved\Models\Saved;
use App\Domains\Portfolio\Models\Portfolio;
use App\Domains\Timezone\Models\Timezone;
use App\Domains\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Class JobRelationship.
 */
trait JobRelationship
{
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
    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsTo
     */
    public function timezone(): BelongsTo
    {
        return $this->belongsTo(Timezone::class);
    }

    /**
     * @return BelongsToMany
     */
    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_applications', 'job_id', 'user_id')->withPivot('status')->withTimestamps()->as('application')->where('is_hidden', false);
    }

    /**
     * @return MorphMany
     */
    public function savers(): MorphMany
    {
        return $this->morphMany(Saved::class, 'savedable', 'saved_type', 'saved_id');
    }

    /**
     * @return MorphOne
     */
    public function mySaved(): MorphOne
    {
        return $this->morphOne(Saved::class, 'savedable', 'saved_type', 'saved_id')->where('user_id', auth()->id());
    }

    /**
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable', 'notifiable_type', 'notifiable_id');
    }

    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'job_id', 'id');
    }
}
