<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UtilityReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('utility-readings', UtilityReadingController::class)->only(['index', 'create', 'store']);
Route::get('invoices/revenue', [InvoiceController::class, 'revenue'])->name('invoices.revenue');
Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::patch('invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])->name('invoices.paid');
Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show']);
