<?php

use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Inventory\StockAdjustmentController;
use App\Http\Controllers\Inventory\StockTransferController;
use Illuminate\Support\Facades\Route;

Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['index', 'create', 'store', 'show']);
Route::resource('stock-transfers', StockTransferController::class)->only(['index', 'create', 'store', 'show']);
