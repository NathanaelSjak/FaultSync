<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    // Get all categories
    public function index() {
        $categories = Category::where('user_id', Auth::id())
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    // Create Category
    public function create(CreateCategoryRequest $request) {

        $exists = Category::where('user_id', Auth::id())
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Category name already exists.'], 422);
        }

        $category = Category::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type
        ]);

        return response()->json($category, 201);
    }

    // Search Category
    public function search() {
        $query = Category::where('user_id', Auth::id());

        if (request()->has('search') && request()->search !== '') {
            $search = request()->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $categories = $query
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    // Update Category
    public function edit(EditCategoryRequest $request, $id) {
        $category = Category::where('user_id', Auth::id())->findOrFail($id);

        // cek nama duplikat kecuali dirinya sendiri
        $exists = Category::where('user_id', Auth::id())
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Category name already exists.'], 422);
        }

        $category->update([
            'name' => $request->name
        ]);

        return response()->json($category);
    }

    // View Category
    public function view($id) {
        // Ambil kategori yang dimiliki user yang login
        $category = Category::where('user_id', Auth::id())->findOrFail($id);
        
        return response()->json($category);
    }

    // Delete Category
    public function remove($id) {
        $category = Category::where('user_id', Auth::id())->findOrFail($id);

        $category->delete();

        return response()->json(['message' => 'Category Deleted']);
    }
}
