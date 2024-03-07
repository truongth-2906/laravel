<?php

use App\Domains\Escrow\Http\Controllers\EscrowController;

Route::group([
    'as' => 'escrow.',
    'prefix' => 'escrow',
    'middleware' => 'webhook_escrow_verification'
], function () {
    Route::any('/webhook/callback/{token?}', [EscrowController::class, 'webhookCallback'])->name('webhook.callback')->withoutMiddleware('web');
});
