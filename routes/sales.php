<?php

use App\Http\Controllers\Sales\SaleController;
use App\Http\Controllers\Sales\ReturnController;
use Illuminate\Support\Facades\Route;

Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('sales/{sale}/returns/create', [ReturnController::class, 'create'])->name('sales.returns.create');
Route::post('sales/{sale}/returns', [ReturnController::class, 'store'])->name('sales.returns.store');
Route::get('sale-returns/{saleReturn}', [ReturnController::class, 'show'])->name('sale-returns.show');
Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
