<?php

use App\Http\Controllers\ExpenseController;

Route::get('/', [ExpenseController::class, 'index'])->name('expenses.index');
Route::post('/expenses', [ExpenseController::class, 'store']);
Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);
Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
Route::get('/expenses/filter', [ExpenseController::class, 'filterByCategory'])->name('expenses.filter');

