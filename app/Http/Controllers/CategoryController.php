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
     * Display all categories (with optional search & filter)
     */
    public function index(Request $request)
    {
        $query = Category::byUser();

        // filter type (income / expense)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // search by name
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $categories = $query
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        // kalau dipakai untuk AJAX
        if ($request->expectsJson()) {
            return response()->json($categories);
        }

        // kalau dipakai untuk blade
        return view('categories.index', compact('categories'));
    }

    /**
     * Store new category
     */
    public function store(CreateCategoryRequest $request)
    {
        $exists = Category::byUser()
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json([
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

        return response()->json($category, 201);
    }

    /**
     * Show single category
     */
    public function show($id)
    {
        $category = Category::byUser()->findOrFail($id);
        return response()->json($category);
    }

    /**
     * Update category
     */
    public function update(EditCategoryRequest $request, $id)
    {
        $category = Category::byUser()->findOrFail($id);

        $exists = Category::byUser()
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
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

        return response()->json($category);
    }

    /**
     * Delete category (soft delete)
     */
    public function destroy($id)
    {
        $category = Category::byUser()->findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}