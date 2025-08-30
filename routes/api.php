<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\VehicleSizeController;
use App\Http\Controllers\Api\ServiceTypeController;
use App\Http\Controllers\Api\ServiceRateController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReminderController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Customer Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware([CorsMiddleware::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('vehicle-sizes', VehicleSizeController::class);
    Route::apiResource('service-types', ServiceTypeController::class);
    Route::apiResource('service-rates', ServiceRateController::class);
    Route::apiResource('appointments', AppointmentController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('reminders', ReminderController::class);

    // 🔹 PayMongo-specific routes
    Route::prefix('payments')->group(function () {
        Route::post('create-checkout', [PaymentController::class, 'createCheckout']);
        Route::post('webhook', [PaymentController::class, 'handleWebhook']);
        Route::get('{paymentId}/status', [PaymentController::class, 'checkPaymentStatus']);
    });
});

// Admin Authentication Routes
Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
