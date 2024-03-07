<?php

namespace App\Providers;

use App\Domains\Auth\Models\User;
use App\Domains\Job\Models\Job;
use App\Domains\Notification\Models\Notification;
use App\Domains\Saved\Models\Saved;
use App\Domains\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        /** custom morphable type */
        Relation::morphMap([
            Saved::TYPE_FREELANCER => User::class,
            Saved::TYPE_JOB => Job::class,
            Notification::PAYMENT_NOTIFIABLE_TYPE => Transaction::class,
        ]);
    }
}
