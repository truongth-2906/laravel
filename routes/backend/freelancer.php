<?php

use App\Domains\Auth\Http\Controllers\Backend\Freelancer\FreelancerController;

Route::group([
    'prefix' => 'freelancer',
    'as' => 'freelancer.',
], function () {
    Route::post('/{freelancer}/{status}', [FreelancerController::class, 'updateStatusHidden'])->name('update_status_hidden');
    Route::get('/', [FreelancerController::class, 'index'])->name('index');
    Route::get('/create', [FreelancerController::class, 'create'])->name('create');
    Route::post('/store', [FreelancerController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [FreelancerController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [FreelancerController::class, 'update'])->name('update');
    Route::get('/delete/{id}', [FreelancerController::class, 'delete'])->name('delete');
    Route::get('export', [FreelancerController::class, 'export'])->name('export');
});
