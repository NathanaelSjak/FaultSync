<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Transaction::where('user_id', Auth::id())
                ->with(['bankAccount', 'category']);

            // Filter by bank account
            if ($request->has('bank_account_id') && $request->bank_account_id) {
                $query->where('bank_account_id', $request->bank_account_id);
            }

            // Filter by category
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by type
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->start_date) {
                $query->where('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->where('date', '<=', $request->end_date);
            }

            $transactions = $query->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $transactions,
                'message' => 'Transactions retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transactions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'bank_account_id' => 'required|exists:bank_accounts,id',
                'category_id' => 'nullable|exists:categories,id',
                'type' => 'required|in:income,expense',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:255',
                'date' => 'required|date',
            ]);

            // Verify bank account belongs to user
            $bankAccount = BankAccount::where('user_id', Auth::id())
                ->findOrFail($request->bank_account_id);

            DB::beginTransaction();

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'bank_account_id' => $request->bank_account_id,
                'category_id' => $request->category_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $transaction->load(['bankAccount', 'category']),
                'message' => 'Transaction created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $transaction = Transaction::where('user_id', Auth::id())
                ->with(['bankAccount', 'category'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $transaction,
                'message' => 'Transaction retrieved successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'bank_account_id' => 'required|exists:bank_accounts,id',
                'category_id' => 'nullable|exists:categories,id',
                'type' => 'required|in:income,expense',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:255',
                'date' => 'required|date',
            ]);

            // Verify bank account belongs to user
            $bankAccount = BankAccount::where('user_id', Auth::id())
                ->findOrFail($request->bank_account_id);

            DB::beginTransaction();

            $transaction = Transaction::where('user_id', Auth::id())
                ->findOrFail($id);

            $transaction->update([
                'bank_account_id' => $request->bank_account_id,
                'category_id' => $request->category_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $transaction->load(['bankAccount', 'category']),
                'message' => 'Transaction updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::where('user_id', Auth::id())
                ->findOrFail($id);

            $transaction->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}



