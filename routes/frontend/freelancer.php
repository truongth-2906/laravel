<?php

use App\Domains\Auth\Http\Controllers\Frontend\Freelancer\FreelancerController;
use App\Domains\Payment\Http\Controllers\Frontend\Freelancer\PaymentController;
use App\Domains\Saved\Http\Controllers\Frontend\SavedController;

Route::group([
    'as' => 'freelancer.',
    'prefix' => 'freelancer',
    'middleware' => ['auth', 'password.expires', config('base.access.middleware.verified')]
], function () {
    Route::get('/profile/{id}', [FreelancerController::class, 'profile'])->name('profile');
    Route::post('/add-review-job', [FreelancerController::class, 'addReviewJob'])->name('add-review-job')->middleware('allow_if_not_hidden');
    Route::group([
        'middleware' => 'is_freelancer'
    ], function () {
        Route::get('/', [FreelancerController::class, 'index'])->name('index');
        Route::get('/setting', [FreelancerController::class, 'setting'])->name('setting');
        Route::get('/available', [FreelancerController::class, 'available'])->name('available');
        Route::post('/setting-available', [FreelancerController::class, 'settingAvailable'])->name('setting-available');
        Route::get('/job-applications', [FreelancerController::class, 'jobApplication'])->name('job-applications');
        Route::get('/job-saved', [FreelancerController::class, 'jobSaved'])->name('job-saved');
        Route::get('/job-done', [FreelancerController::class, 'jobDone'])->name('job-done');
        Route::group(['middleware' => 'allow_if_not_hidden'], function () {
            Route::get('/apply/{id}', [FreelancerController::class, 'jobDetail'])->name('job-detail');
            Route::post('/apply', [FreelancerController::class, 'jobApply'])->name('job-apply');
        });
        Route::post('/update', [FreelancerController::class, 'update'])->name('update');
        Route::get('/job-preview', [FreelancerController::class, 'jobPreview'])->name('job-preview');
        Route::get('/current-page-job', [FreelancerController::class, 'getCurrentPageJob'])->name('current-page-job');
        Route::get('/job-applications/{job}', [FreelancerController::class, 'jobApplicationPreview'])->name('job-application-preview');
        Route::get('/billing', [FreelancerController::class, 'listPayment'])->name('billing');
        Route::get('/get-review-job-done/{id}', [FreelancerController::class, 'getReviewJobDone'])->name('get-review-job-done');

        Route::group([
            'prefix' => 'jobs',
            'as' => 'jobs.',
        ], function () {
            Route::get('/{job}/done', [FreelancerController::class, 'jobDonePreview'])->name('done.preview');
            Route::get('/{job}/saved', [SavedController::class, 'jobPreview'])->name('saved.preview');
            Route::get('/{job}/view-status', [FreelancerController::class, 'viewStatus'])->name('view_status')->name('get-review-job-done');
        });

        Route::group([
            'prefix' => 'payments',
            'as' => 'payments.',
        ], function () {
            Route::group([
                'prefix' => 'escrow_account',
                'as' => 'escrow_account.',
            ], function () {
                Route::get('/', [PaymentController::class, 'addEscrowAccount'])->name('create');
                Route::post('/', [PaymentController::class, 'storeEscrowAccount'])->name('store');
            });

            Route::get('/', [PaymentController::class, 'index'])->name('index');
        });

        include (__DIR__.'/notification.php');//notification routes
    });
});
