<?php

use App\Domains\Auth\Http\Controllers\Backend\User\SettingUserController;
use App\Domains\Auth\Http\Controllers\Backend\User\UserController;
use App\Http\Controllers\Backend\DashboardController;
use Tabuna\Breadcrumbs\Trail;

// All route names are prefixed with 'admin.'.
Route::redirect('/', '/admin/dashboard', 301);
Route::get('dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->breadcrumbs(function (Trail $trail) {
        $trail->push(__('Home'), route('admin.dashboard'));
    });

Route::group([
    'prefix' => 'setting',
    'as' => 'setting.',
], function () {
    Route::get('/password', [SettingUserController::class, 'settingPassword'])->name('password');
    Route::post('/password/update', [SettingUserController::class, 'update'])->name('password.update');
});

Route::group([
    'prefix' => 'users',
    'as' => 'users.',
], function () {
    Route::get('/for-select2', [UserController::class, 'getNormalUserForSelect2'])->name('for_select2');
});
