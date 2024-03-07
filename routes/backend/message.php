<?php

use App\Domains\Message\Http\Controllers\Backend\MessageController;

Route::group([
    'prefix' => 'messages',
    'as' => 'messages.',
], function () {
    Route::get('/', [MessageController::class, 'index'])->name('index');
    Route::get('/{sender}/with/{receiver}', [MessageController::class, 'show'])->name('show');
});
