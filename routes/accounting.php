<?php

use App\Http\Controllers\Accounting\ExpenseCategoryController;
use App\Http\Controllers\Accounting\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::get('accounting/summary', [ExpenseController::class, 'summary'])->name('accounting.summary');
Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show']);
Route::resource('expenses', ExpenseController::class);
