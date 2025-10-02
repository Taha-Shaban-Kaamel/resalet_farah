<?php

use App\Http\Controllers\TestEmailController;
use Illuminate\Support\Facades\Route;

// Test Email Route
Route::get('/test-email', [TestEmailController::class, 'sendTestEmail']);

// Mail Configuration Test Route
Route::get('/mail-config', function() {
    return [
        'mail.mailer' => config('mail.mailer'),
        'mail.host' => config('mail.mailers.smtp.host'),
        'mail.port' => config('mail.mailers.smtp.port'),
        'mail.encryption' => config('mail.mailers.smtp.encryption'),
        'mail.username' => config('mail.mailers.smtp.username'),
        'mail.from.address' => config('mail.from.address'),
    ];
});

Route::get('/', function () {
    return view('welcome');
});
