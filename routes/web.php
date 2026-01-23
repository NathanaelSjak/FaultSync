<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Views)
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

/*
|--------------------------------------------------------------------------
| Authentication API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->name('auth.')->group(function() {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function() {
    
    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/dashboard/summary', [DashboardController::class, 'summary'])->name('dashboard.summary');
    Route::get('/dashboard/account/{accountId}/balance', [DashboardController::class, 'accountBalance'])->name('dashboard.account.balance');
    
    /*
    |--------------------------------------------------------------------------
    | Bank Account Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/bank-accounts', function () {
        return view('bank-accounts.index');
    })->name('bank-accounts.index');
    
    // API routes for AJAX requests
    Route::prefix('api')->group(function() {
        Route::get('/bank-accounts', [BankAccountController::class, 'index']);
        Route::get('/bank-accounts/{id}', [BankAccountController::class, 'show']);
        Route::post('/bank-accounts', [BankAccountController::class, 'store']);
        Route::put('/bank-accounts/{id}', [BankAccountController::class, 'update']);
        Route::delete('/bank-accounts/{id}', [BankAccountController::class, 'destroy']);
    });
    
    Route::resource('bank-accounts', BankAccountController::class)->except(['index', 'create', 'edit', 'show', 'store', 'update', 'destroy']);
    
    /*
    |--------------------------------------------------------------------------
    | Category Routes
    |--------------------------------------------------------------------------
    */
    
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/api/categories', [CategoryController::class, 'list']);
    Route::resource('categories', CategoryController::class)->except(['index', 'create', 'edit']);
    
    Route::prefix('categories')->name('categories.')->group(function() {
        Route::get('/search', [CategoryController::class, 'search'])->name('search');
        Route::patch('/{id}/restore', [CategoryController::class, 'restore'])
            ->name('restore')
            ->where('id', '[0-9]+');
        Route::delete('/{id}/force-delete', [CategoryController::class, 'forceDelete'])
            ->name('force-delete')
            ->where('id', '[0-9]+');
        Route::get('/trashed', [CategoryController::class, 'trashed'])->name('trashed');
        Route::patch('/{id}/status', [CategoryController::class, 'updateStatus'])
            ->name('status.update')
            ->where('id', '[0-9]+');
        Route::post('/bulk/delete', [CategoryController::class, 'bulkDelete'])->name('bulk.delete');
        Route::post('/bulk/restore', [CategoryController::class, 'bulkRestore'])->name('bulk.restore');
        Route::get('/export', [CategoryController::class, 'export'])->name('export');
        Route::post('/import', [CategoryController::class, 'import'])->name('import');
        Route::get('/statistics', [CategoryController::class, 'statistics'])->name('statistics');
        Route::get('/type/{type}', [CategoryController::class, 'byType'])
            ->name('type')
            ->where('type', 'income|expense|transfer');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Transaction Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/transactions', function () {
        return view('transactions.index');
    })->name('transactions.index');
    
    // API routes for AJAX requests
    Route::prefix('api')->group(function() {
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::get('/transactions/{id}', [TransactionController::class, 'show']);
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::put('/transactions/{id}', [TransactionController::class, 'update']);
        Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
    });
    
    Route::resource('transactions', TransactionController::class)->except(['index', 'create', 'edit', 'show', 'store', 'update', 'destroy']);
    
    /*
    |--------------------------------------------------------------------------
    | User Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile.index');
    
    Route::prefix('profile')->name('profile.')->group(function() {
        Route::get('/api', [AuthController::class, 'profile'])->name('api.show');
        Route::put('/api', [AuthController::class, 'updateProfile'])->name('api.update');
        Route::delete('/api', [AuthController::class, 'deleteAccount'])->name('api.delete');
    });
    
});

/*
|--------------------------------------------------------------------------
| Locale Routes
|--------------------------------------------------------------------------
*/
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');