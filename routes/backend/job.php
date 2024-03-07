<?php

use App\Domains\Job\Http\Controllers\Backend\JobController;

Route::group([
    'prefix' => 'job',
    'as' => 'job.',
], function () {
    Route::get('/', [JobController::class, 'index'])->name('index');
    Route::get('/create', [JobController::class, 'create'])->name('create');
    Route::post('/store', [JobController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [JobController::class, 'edit'])->name('edit');
    Route::delete('/{id}', [JobController::class, 'delete'])->name('delete');
    Route::post('/update/{id}', [JobController::class, 'update'])->name('update');
    Route::get('export', [JobController::class, 'export'])->name('export');
});
