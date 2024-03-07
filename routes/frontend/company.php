<?php

use App\Domains\Company\Http\Controllers\Frontend\CompanyController;

Route::group([
    'prefix' => 'company',
    'as' => 'company.',
    'middleware' => ['auth', 'password.expires', config('base.access.middleware.verified')]
], function () {
    Route::post('/store', [CompanyController::class, 'store'])->name('store');
});
