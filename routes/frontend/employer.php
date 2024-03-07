<?php

use App\Domains\Auth\Http\Controllers\Frontend\Employer\EmployerController;
use App\Domains\Job\Http\Controllers\Frontend\JobController;
use App\Domains\Payment\Http\Controllers\Frontend\Employer\PaymentController;
use App\Domains\Saved\Http\Controllers\Frontend\SavedController;

Route::group([
    'as' => 'employer.',
    'prefix' => 'employer',
    'middleware' => ['auth', 'password.expires', config('base.access.middleware.verified')]
], function () {
    Route::get('/profile/{id}', [EmployerController::class, 'profile'])->name('profile');
    Route::group([
        'middleware' => 'is_employer'
    ], function () {
        Route::get('/', [EmployerController::class, 'index'])->name('index');
        Route::get('/setting', [EmployerController::class, 'setting'])->name('setting');
        Route::post('/update-details', [EmployerController::class, 'updateDetails'])->name('updateDetails');

        Route::group([
            'prefix' => 'jobs',
            'as' => 'jobs.',
        ], function () {
            Route::get('/', [JobController::class, 'index'])->name('index');
            Route::get('/{job}/applications', [EmployerController::class, 'applications'])->name('applications');
            Route::get('/{job}/preview', [JobController::class, 'preview'])->name('preview');
            Route::get('/create', [JobController::class, 'create'])->name('create');
            Route::post('/', [JobController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [JobController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [JobController::class, 'update'])->name('update');
            Route::delete('/{id}', [JobController::class, 'destroy'])->name('destroy');
        });

        Route::group([
            'prefix' => 'saved',
            'as' => 'saved.',
        ], function () {
            Route::get('/jobs', [SavedController::class, 'listJobs'])->name('job');
            Route::get('/jobs/{id}', [SavedController::class, 'jobPreview'])->name('job.preview');
            Route::get('/freelancers', [SavedController::class, 'listFreelancers'])->name('freelancer');
            Route::get('/freelancers/{id}', [SavedController::class, 'freelancerPreview'])->name('freelancer.preview');
            Route::post('/freelancer', [SavedController::class, 'saveFreelancer'])->name('save-freelancer');
        });

        Route::get('/find-freelancer', [EmployerController::class, 'findFreelancer'])->name('find-freelancer');
        Route::get('/preview-freelancer', [EmployerController::class, 'previewFreelancer'])->name('preview-freelancer');
        Route::get('/current-page-freelancer', [EmployerController::class, 'currentPageFreelancer'])->name('current-page-freelancer');
        Route::post('/add-review-freelancer', [EmployerController::class, 'addReviewFreelancer'])->name('add-review-freelancer');
        Route::get('/billing', [EmployerController::class, 'listPayment'])->name('billing');
        Route::get('/job-preview', [EmployerController::class, 'previewJob'])->name('job-preview');
        Route::get('/current-page-job', [EmployerController::class, 'currentPageJob'])->name('current-page-job');
        Route::get('/list-freelancer-job', [EmployerController::class, 'listFreelancerOfJob'])->name('list-freelancer-job');
        Route::get('/detail-freelancer-apply', [EmployerController::class, 'detailFreelancerApply'])->name('detail-freelancer-apply');
        Route::post('/update-status-job-application', [EmployerController::class, 'updateStatusJobApplication'])->name('update-status-job-application');

        Route::group([
            'prefix' => 'payments',
            'as' => 'payments.',
        ], function () {
            Route::group([
                'prefix' => 'escrow_account',
                'as' => 'escrow_account.',
            ], function () {
                Route::post('/', [PaymentController::class, 'storeEscrowAccount'])->name('store');
                Route::get('/', [PaymentController::class, 'addEscrowAccount'])->name('create');
            });

            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::get('/wait-for-redirect', [PaymentController::class, 'waitForRedirect'])->name('wait_for_redirect');
            Route::get('/{transactionId}/funding', [PaymentController::class, 'fundingTransaction'])->name('funding');
            Route::post('/{transactionId}/pay-now', [PaymentController::class, 'payNow'])->name('pay-now');
            Route::post('/{transactionId}', [PaymentController::class, 'cancel'])->name('cancel');
        });

        include (__DIR__.'/notification.php'); //notification routes
    });
});
