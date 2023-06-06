<?php

use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'show'])
        ->name('register');

    Route::post('register', [RegisterController::class, 'store']);

    Route::get('login', [AuthenticateController::class, 'show'])
        ->name('login');

    Route::post('login', [AuthenticateController::class, 'store']);

    Route::get('forgot-password', [PasswordResetController::class, 'showRequest'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetController::class, 'sendLink'])
        ->name('password.email');

    Route::get('reset-password/{token}', [PasswordResetController::class, 'showReset'])
        ->name('password.reset');

    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [VerificationController::class, 'show'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('verify-email', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::put('password', [ProfileController::class, 'updatePassword'])->name('password.update');

    Route::post('logout', [AuthenticateController::class, 'destroy'])
        ->name('logout');
});
