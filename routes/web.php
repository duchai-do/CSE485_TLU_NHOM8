<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UtilityReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('utility-readings', UtilityReadingController::class)->except(['show']);
Route::get('invoices/revenue', [InvoiceController::class, 'revenue'])->name('invoices.revenue');
Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::patch('invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])->name('invoices.paid');
Route::patch('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
Route::resource('invoices', InvoiceController::class);
