<?php

use App\Http\Controllers\AllocationController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ViolationRecordController;
use Illuminate\Support\Facades\Route;

Route::prefix('member3')->name('member3.')->group(function (): void {
    Route::get('registrations', [AllocationController::class, 'registrations'])
        ->name('registrations.index');
    Route::patch('registrations/{registration}/approve', [AllocationController::class, 'approveRegistration'])
        ->name('registrations.approve');
    Route::patch('registrations/{registration}/reject', [AllocationController::class, 'rejectRegistration'])
        ->name('registrations.reject');

    Route::resource('allocations', AllocationController::class)->only(['index', 'create', 'store']);
    Route::resource('contracts', ContractController::class)->only(['index', 'create', 'store']);
    Route::resource('violations', ViolationRecordController::class)->only(['index', 'create', 'store']);
});
