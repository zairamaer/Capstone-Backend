<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\PaymentController;

// Admin Login Page
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

// Admin Login Submission
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

// Admin Dashboard (Protected Route)
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware('auth:admin')->name('admin.dashboard');


