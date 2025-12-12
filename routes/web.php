<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes for Category Management
|--------------------------------------------------------------------------
|
| All routes are protected by 'auth' middleware to ensure only
| authenticated users can access them.
|
*/

Route::middleware('auth')->group(function() {
    
    /*
    |--------------------------------------------------------------------------
    | Category Routes
    |--------------------------------------------------------------------------
    |
    | Standard RESTful routes for category CRUD operations
    | Following Laravel's resource controller naming conventions
    |
    */
    
    // Resource routes akan menghasilkan:
    // GET    /categories           → index()    - List all categories
    // GET    /categories/create    → create()   - Show create form (optional)
    // POST   /categories           → store()    - Store new category
    // GET    /categories/{id}      → show()     - Show single category
    // GET    /categories/{id}/edit → edit()     - Show edit form
    // PUT    /categories/{id}      → update()   - Update category
    // DELETE /categories/{id}      → destroy()  - Delete category
    Route::resource('categories', CategoryController::class);
    
    /*
    |--------------------------------------------------------------------------
    | Additional Category Routes
    |--------------------------------------------------------------------------
    |
    | Routes for additional functionality beyond basic CRUD
    |
    */
    Route::prefix('categories')->name('categories.')->group(function() {
        
        // Live search with AJAX
        Route::get('/search', [CategoryController::class, 'search'])
            ->name('search');
            
        // Soft delete restore
        Route::patch('/{id}/restore', [CategoryController::class, 'restore'])
            ->name('restore')
            ->where('id', '[0-9]+');
            
        // Force delete permanently
        Route::delete('/{id}/force-delete', [CategoryController::class, 'forceDelete'])
            ->name('force-delete')
            ->where('id', '[0-9]+');
            
        // View trashed (soft deleted) categories
        Route::get('/trashed', [CategoryController::class, 'trashed'])
            ->name('trashed');
            
        // Update category status (active/inactive)
        Route::patch('/{id}/status', [CategoryController::class, 'updateStatus'])
            ->name('status.update')
            ->where('id', '[0-9]+');
            
        // Bulk actions
        Route::post('/bulk/delete', [CategoryController::class, 'bulkDelete'])
            ->name('bulk.delete');
        Route::post('/bulk/restore', [CategoryController::class, 'bulkRestore'])
            ->name('bulk.restore');
            
        // Export categories to CSV/Excel
        Route::get('/export', [CategoryController::class, 'export'])
            ->name('export');
            
        // Import categories from file
        Route::post('/import', [CategoryController::class, 'import'])
            ->name('import');
            
        // Category statistics
        Route::get('/statistics', [CategoryController::class, 'statistics'])
            ->name('statistics');
            
        // Category type filter
        Route::get('/type/{type}', [CategoryController::class, 'byType'])
            ->name('type')
            ->where('type', 'income|expense|transfer');
    });
    
});