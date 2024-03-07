<?php

use App\Domains\Auth\Http\Controllers\Backend\Employer\EmployerController;

Route::group([
    'prefix' => 'employer',
    'as' => 'employer.',
], function () {
    Route::get('/', [EmployerController::class, 'index'])->name('index');
    Route::get('/create', [EmployerController::class, 'create'])->name('create');
    Route::post('/store', [EmployerController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [EmployerController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [EmployerController::class, 'update'])->name('update');
    Route::get('/delete/{id}', [EmployerController::class, 'delete'])->name('delete');
    Route::get('export', [EmployerController::class, 'export'])->name('export');
});
