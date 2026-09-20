<?php

use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Purchase\PurchasePaymentController;
use App\Http\Controllers\Purchase\SupplierController;
use Illuminate\Support\Facades\Route;

Route::resource('customers', CustomerController::class)->except(['show']);
Route::resource('suppliers', SupplierController::class)->except(['show']);
Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
Route::post('purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
Route::post('purchases/{purchase}/payments', [PurchasePaymentController::class, 'store'])->name('purchases.payments.store');
Route::resource('purchases', PurchaseController::class)->except(['show']);
Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
