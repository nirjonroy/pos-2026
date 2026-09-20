<?php

use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Purchase\SupplierController;
use Illuminate\Support\Facades\Route;

Route::resource('customers', CustomerController::class)->except(['show']);
Route::resource('suppliers', SupplierController::class)->except(['show']);
