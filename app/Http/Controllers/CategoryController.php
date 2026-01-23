<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index');
    }

    public function list(Request $request)
    {
        try {
            $query = Category::byUser();

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('search')) {
                $query->search($request->search);
            }

            $categories = $query
                ->orderBy('type')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new category
     */
    public function store(CreateCategoryRequest $request)
    {
        try {
            // Ambil data validasi (sudah ada user_id, default, unique check)
            $data = $request->validated();

            // Jaga-jaga: set default icon kalau kosong
            if (empty($data['icon'])) {
                $data['icon'] = match ($data['type']) {
                    'income'   => 'fas fa-money-bill-wave',
                    'expense'  => 'fas fa-shopping-cart',
                    'transfer' => 'fas fa-exchange-alt',
                    default    => 'fas fa-tag',
                };
            }

            $category = Category::create($data);

            return response()->json([
                'success' => true,
                'data'    => $category,
                'message' => 'Category created successfully'
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Create Category Failed', [
                'user_id' => Auth::id(),
                'request' => $request->except(['_token']),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create category'
            ], 500);
        }
    }

    /**
     * Show category detail
     */
    public function show($id)
    {
        try {
            $category = Category::byUser()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category retrieved successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
    }

    /**
     * Update category
     */
    public function update(EditCategoryRequest $request, $id)
    {
        try {
            $category = Category::byUser()->findOrFail($id);

            $exists = Category::byUser()
                ->where('name', $request->name)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category name already exists'
                ], 422);
            }

            // Set default icon based on type if not provided
            $defaultIcon = match($request->type ?? $category->type) {
                'income' => 'fas fa-money-bill-wave',
                'expense' => 'fas fa-shopping-cart',
                'transfer' => 'fas fa-exchange-alt',
                default => 'fas fa-tag',
            };

            $category->update([
                'name'        => $request->name,
                'type'        => $request->type ?? $category->type,
                'description' => $request->description ?? $category->description,
                'color'       => $request->color ?? $category->color ?? '#6c757d',
                'icon'        => $request->icon ?? $category->icon ?? $defaultIcon,
                'status'      => $request->has('status')
                    ? $request->boolean('status')
                    : $category->status,
            ]);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category updated successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
    }

    /**
     * Delete category (soft delete)
     */
    public function destroy($id)
    {
        try {
            $category = Category::byUser()->findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
    }
}