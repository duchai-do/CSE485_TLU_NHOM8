<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomRegistrationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BuildingController; // Thêm Controller Tòa nhà
use App\Http\Controllers\RoomController;     // Thêm Controller Phòng
use App\Http\Controllers\BedController;      // Thêm Controller Giường
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    Route::middleware('role:admin,staff')->group(function () {
        Route::resource('students', StudentController::class);
        
        // 👉 TÍCH HỢP QUẢN LÝ TÒA NHÀ - PHÒNG - GIƯỜNG VÀO ĐÂY
        Route::resource('buildings', BuildingController::class);
        Route::resource('rooms', RoomController::class);
        Route::resource('beds', BedController::class);
    });

    Route::post('/room-registrations/{roomRegistration}/approve', [RoomRegistrationController::class, 'approve'])
        ->middleware('role:admin,staff')
        ->name('room-registrations.approve');

    Route::post('/room-registrations/{roomRegistration}/reject', [RoomRegistrationController::class, 'reject'])
        ->middleware('role:admin,staff')
        ->name('room-registrations.reject');

    Route::resource('room-registrations', RoomRegistrationController::class);
});

require __DIR__.'/member3.php';
require __DIR__.'/utility_invoice.php';