<?php

use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Inventory\StockAdjustmentController;
use App\Http\Controllers\Inventory\StockTransferController;
use Illuminate\Support\Facades\Route;

Route::get('inventory', [InventoryController::class, 'index'])->middleware('permission:inventory.view')->name('inventory.index');
Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['index', 'show'])->middleware('permission:inventory.view');
Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['create', 'store'])->middleware('permission:inventory.adjust');
Route::resource('stock-transfers', StockTransferController::class)->only(['index', 'show'])->middleware('permission:inventory.view');
Route::resource('stock-transfers', StockTransferController::class)->only(['create', 'store'])->middleware('permission:inventory.transfer');
