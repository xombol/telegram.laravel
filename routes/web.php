<?php

use App\Http\Controllers\Telegram\TelegramController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


/**
 * Need add line in `app/Http/Middleware/VerifyCsrfToken.php`
 * for open request or user this route in 'routes/api.php`
 */

Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::post('/token/{api_token}', [TelegramController::class, 'token'])->name('token');
});
