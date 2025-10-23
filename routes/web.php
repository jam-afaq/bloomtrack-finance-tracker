<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\InsightController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('landing');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Dashboard
    Route::post('/dashboard/generate-insights', [DashboardController::class, 'generateInsights'])
        ->name('dashboard.generate-insights');
    
    // Transactions
    Route::resource('transactions', TransactionController::class);
    Route::post('/transactions/import', [TransactionController::class, 'import'])
        ->name('transactions.import');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Budgets
    Route::resource('budgets', BudgetController::class);
    
    // Goals
    Route::resource('goals', GoalController::class);
    
    // Insights
    Route::resource('insights', InsightController::class);
});

require __DIR__.'/auth.php';
