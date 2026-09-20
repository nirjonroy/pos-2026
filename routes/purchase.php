<?php

use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Purchase\PurchasePaymentController;
use App\Http\Controllers\Purchase\SupplierController;
use Illuminate\Support\Facades\Route;

Route::resource('customers', CustomerController::class)->except(['show'])->middleware('permission:customers.view');
Route::resource('suppliers', SupplierController::class)->except(['show'])->middleware('permission:suppliers.view');
Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->middleware('permission:purchases.receive')->name('purchases.receive');
Route::post('purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->middleware('permission:purchases.create')->name('purchases.cancel');
Route::post('purchases/{purchase}/payments', [PurchasePaymentController::class, 'store'])->middleware('permission:purchases.payment')->name('purchases.payments.store');
Route::resource('purchases', PurchaseController::class)->except(['show'])->middleware('permission:purchases.view');
Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->middleware('permission:purchases.view')->name('purchases.show');
