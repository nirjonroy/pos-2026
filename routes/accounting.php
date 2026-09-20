<?php

use App\Http\Controllers\Accounting\ExpenseCategoryController;
use App\Http\Controllers\Accounting\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::get('accounting/summary', [ExpenseController::class, 'summary'])->middleware('permission:expenses.view')->name('accounting.summary');
Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show'])->middleware('permission:expenses.manage');
Route::resource('expenses', ExpenseController::class)->middleware('permission:expenses.view');
