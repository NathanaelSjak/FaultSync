<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Category::where('user_id', Auth::id());

            // Filter by type if provided
            if ($request->has('type') && $request->type !== '') {
                $query->where('type', $request->type);
            }

            // Search functionality
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'type');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection)
                  ->orderBy('name', 'asc');

            // Pagination or get all
            if ($request->has('per_page')) {
                $categories = $query->paginate($request->per_page);
            } else {
                $categories = $query->get();
            }

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CreateCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $exists = Category::where('user_id', Auth::id())
                ->where('name', $request->name)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category name already exists.'
                ], 422);
            }

            $category = Category::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description ?? null,
                'color' => $request->color ?? '#6c757d', // default color
                'icon' => $request->icon ?? 'fas fa-folder',
                'status' => $request->boolean('status', true)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::where('user_id', Auth::id())->findOrFail($id);

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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(EditCategoryRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $category = Category::where('user_id', Auth::id())->findOrFail($id);

            // Check for duplicate name (excluding current category)
            $exists = Category::where('user_id', Auth::id())
                ->where('name', $request->name)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category name already exists.'
                ], 422);
            }

            $category->update([
                'name' => $request->name,
                'type' => $request->type ?? $category->type,
                'description' => $request->description ?? $category->description,
                'color' => $request->color ?? $category->color,
                'icon' => $request->icon ?? $category->icon,
                'status' => $request->has('status') ? $request->boolean('status') : $category->status
            ]);

            DB::commit();

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
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $category = Category::where('user_id', Auth::id())->findOrFail($id);

            // Check if category has related transactions before deleting
            if (method_exists($category, 'transactions') && $category->transactions()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category because it has associated transactions'
                ], 422);
            }

            $category->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = Category::where('user_id', Auth::id());

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Additional filters
            if ($request->has('type') && $request->type !== '') {
                $query->where('type', $request->type);
            }

            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->boolean('status'));
            }

            $categories = $query->orderBy('type', 'asc')
                                ->orderBy('name', 'asc')
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories searched successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statistics()
    {
        try {
            $stats = Category::where('user_id', Auth::id())
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get();

            $total = Category::where('user_id', Auth::id())->count();
            $active = Category::where('user_id', Auth::id())->where('status', true)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'by_type' => $stats,
                    'total' => $total,
                    'active' => $active,
                    'inactive' => $total - $active
                ],
                'message' => 'Category statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();

            $category = Category::withTrashed()
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            if (!$category->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category is not deleted'
                ], 422);
            }

            $category->restore();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category restored successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $categories = Category::onlyTrashed()
                ->where('user_id', Auth::id())
                ->orderBy('deleted_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Trashed categories retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve trashed categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}