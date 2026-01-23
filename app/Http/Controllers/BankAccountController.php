<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankAccountController extends Controller
{
    public function index()
    {
        try {
            $bankAccounts = BankAccount::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $bankAccounts,
                'message' => 'Bank accounts retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bank accounts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'bank_name' => 'required|string|max:100',
                'account_number' => 'required|string|max:50',
                'type' => 'required|string|max:50',
                'description' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            $bankAccount = BankAccount::create([
                'user_id' => Auth::id(),
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'type' => $request->type,
                'description' => $request->description,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $bankAccount,
                'message' => 'Bank account created successfully'
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
                'message' => 'Failed to create bank account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $bankAccount = BankAccount::where('user_id', Auth::id())
                ->with('transactions')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $bankAccount,
                'message' => 'Bank account retrieved successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bank account not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bank account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'bank_name' => 'required|string|max:100',
                'account_number' => 'required|string|max:50',
                'type' => 'required|string|max:50',
                'description' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            $bankAccount = BankAccount::where('user_id', Auth::id())
                ->findOrFail($id);

            $bankAccount->update([
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'type' => $request->type,
                'description' => $request->description,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $bankAccount,
                'message' => 'Bank account updated successfully'
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
                'message' => 'Bank account not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bank account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $bankAccount = BankAccount::where('user_id', Auth::id())
                ->findOrFail($id);

            if ($bankAccount->transactions()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete bank account because it has associated transactions'
                ], 422);
            }

            $bankAccount->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bank account deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Bank account not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bank account',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}



