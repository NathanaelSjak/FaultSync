<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
    
    public function summary()
    {
        try {
            $userId = Auth::id();

            // Get all bank accounts with balances
            $bankAccounts = BankAccount::where('user_id', $userId)
                ->with(['transactions' => function($query) {
                    $query->select('bank_account_id', 'type', DB::raw('SUM(amount) as total'))
                        ->groupBy('bank_account_id', 'type');
                }])
                ->get()
                ->map(function($account) {
                    $income = Transaction::where('bank_account_id', $account->id)
                        ->where('type', 'income')
                        ->sum('amount');
                    
                    $expense = Transaction::where('bank_account_id', $account->id)
                        ->where('type', 'expense')
                        ->sum('amount');
                    
                    return [
                        'id' => $account->id,
                        'bank_name' => $account->bank_name,
                        'account_number' => $account->account_number,
                        'type' => $account->type,
                        'balance' => $income - $expense,
                        'income' => $income,
                        'expense' => $expense,
                    ];
                });

            // Global totals
            $totalIncome = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->sum('amount');

            $totalExpense = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->sum('amount');

            // Combined transaction history
            $transactions = Transaction::where('user_id', $userId)
                ->with(['bankAccount', 'category'])
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'bank_accounts' => $bankAccounts,
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'net_balance' => $totalIncome - $totalExpense,
                    'recent_transactions' => $transactions,
                ],
                'message' => 'Summary retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function accountBalance($accountId)
    {
        try {
            $account = BankAccount::where('user_id', Auth::id())
                ->findOrFail($accountId);

            $income = Transaction::where('bank_account_id', $accountId)
                ->where('type', 'income')
                ->sum('amount');

            $expense = Transaction::where('bank_account_id', $accountId)
                ->where('type', 'expense')
                ->sum('amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'account' => $account,
                    'income' => $income,
                    'expense' => $expense,
                    'balance' => $income - $expense,
                ],
                'message' => 'Account balance retrieved successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bank account not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve account balance',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}



