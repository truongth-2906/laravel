<?php

use App\Domains\Notification\Http\Controllers\Frontend\NotificationController;

Route::group([
    'as' => 'notifications.',
    'prefix' => 'notifications',
], function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{notification}', [NotificationController::class, 'read'])->name('read');
    Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
});
