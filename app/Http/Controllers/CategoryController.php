<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display all categories
     */
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
            $exists = Category::byUser()
                ->where('name', $request->name)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category name already exists'
                ], 422);
            }

            $category = Category::create([
                'user_id'     => Auth::id(),
                'name'        => $request->name,
                'type'        => $request->type,
                'description' => $request->description,
                'color'       => $request->color,
                'icon'        => $request->icon,
                'status'      => true,
            ]);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
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

            $category->update([
                'name'        => $request->name,
                'type'        => $request->type,
                'description' => $request->description,
                'color'       => $request->color,
                'icon'        => $request->icon,
                'status'      => $request->boolean('status', true),
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