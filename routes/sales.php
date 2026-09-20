<?php

use App\Http\Controllers\Sales\SaleController;
use App\Http\Controllers\Sales\ReturnController;
use Illuminate\Support\Facades\Route;

Route::get('sales', [SaleController::class, 'index'])->middleware('permission:sales.view')->name('sales.index');
Route::get('sales/{sale}/returns/create', [ReturnController::class, 'create'])->middleware('permission:sales.return')->name('sales.returns.create');
Route::post('sales/{sale}/returns', [ReturnController::class, 'store'])->middleware('permission:sales.return')->name('sales.returns.store');
Route::get('sale-returns/{saleReturn}', [ReturnController::class, 'show'])->middleware('permission:sales.view')->name('sale-returns.show');
Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->middleware('permission:sales.view')->name('sales.receipt');
Route::get('sales/{sale}', [SaleController::class, 'show'])->middleware('permission:sales.view')->name('sales.show');
