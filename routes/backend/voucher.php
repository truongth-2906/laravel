<?php

use App\Domains\Voucher\Http\Controllers\Backend\SavedVoucherController;
use App\Domains\Voucher\Http\Controllers\Backend\VoucherController;

Route::group([
    'prefix' => 'vouchers',
    'as' => 'vouchers.'
], function () {
    Route::get('/', [VoucherController::class, 'index'])->name('index');
    Route::get('/create', [VoucherController::class, 'create'])->name('create');
    Route::post('/', [VoucherController::class, 'store'])->name('store');
    Route::post('/{voucher}/update-status', [VoucherController::class, 'updateStatus'])->name('update_status');

    Route::group([
        'prefix' => '/{voucherId}/saved',
        'as' => 'saved.'
    ], function () {
        Route::get('/', [SavedVoucherController::class, 'listByVoucherId'])->name('list');
    });
});
