<?php

use App\Domains\Company\Http\Controllers\Backend\CompanyController;

Route::group([
    'prefix' => 'company',
    'as' => 'company.',
], function () {
    Route::post('/store', [CompanyController::class, 'store'])->name('store');
});
