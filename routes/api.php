<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::post('login', [AuthenticateController::class, 'createToken']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('order', [OrderController::class, 'store']);
    Route::post('logout', [AuthenticateController::class, 'destroyToken']);
});
