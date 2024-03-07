<?php

use App\Domains\Message\Http\Controllers\Frontend\MessageController;

Route::group([
    'prefix' => 'message',
    'as' => 'message.',
    'middleware' => ['auth', 'is_user', 'password.expires', config('base.access.middleware.verified')]
], function () {
    Route::get('/', [MessageController::class, 'index'])->name('index');
    Route::middleware('allow_if_not_hidden')->group(function () {
        Route::get('/chat/{id}', [MessageController::class, 'chat'])->name('chat');
        Route::post('/chat/{id}', [MessageController::class, 'store'])->name('store');
        Route::get('/list-user', [MessageController::class, 'getListUser'])->name('list-user');
        Route::post('/{id}/reaction', [MessageController::class, 'reaction'])->name('reaction.add');
        Route::delete('/{id}/reaction', [MessageController::class, 'reaction'])->name('reaction.remove');
        Route::post('/mark-is-read', [MessageController::class, 'markIsRead'])->name('mark-is-read');
    });
});
