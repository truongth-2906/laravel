<?php

use App\Domains\MessageFile\Http\Controllers\FileMessageController;
use App\Domains\Portfolio\Http\Controllers\PortfolioController;


Route::get('/download/{filename}', [PortfolioController::class, 'download'])->name('download');

Route::get('/download/file-message/{filename}', [FileMessageController::class, 'download'])->name('download.file_message');
