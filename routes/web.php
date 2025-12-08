<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    // Get All Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Create Category
    Route::post('/categories/create', [CategoryController::class, 'create'])->name('categories.create');

    // Search Category
    Route::get('/categories/search', [CategoryController::class, 'search'])->name('categories.search');

    // Edit Category
    Route::put('/categories/edit/{id}', [CategoryController::class, 'edit'])->name('categories.edit');

    // View Category 
    Route::get('/categories/{id}', [CategoryController::class, 'view'])->name('categories.view');

    // Remove Category
    Route::delete('/categories/remove/{id}', [CategoryController::class, 'remove'])->name('categories.remove');
});