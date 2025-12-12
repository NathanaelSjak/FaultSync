@extends('category')

@section('title', 'Categories')

@section('page-title', 'Category Management')

@section('breadcrumb', 'All Categories')

@section('content')
<div class="container mx-auto">
    <!-- Header with Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
            <p class="text-gray-600 mt-1">Manage your income and expense categories</p>
        </div>
        
        <div class="flex space-x-3 mt-4 md:mt-0">
            <!-- Search Input -->
            <div class="relative">
                <input type="text" 
                       placeholder="Search categories..." 
                       class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <!-- Create Button -->
            <button onclick="openCreateModal()" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i>
                New Category
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Categories</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">24</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tags text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Income Categories</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">8</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-500 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Expense Categories</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">16</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-red-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" 
                                     style="background-color: {{ $category->color ?? '#6b7280' }}">
                                    <i class="{{ $category->icon ?? 'fas fa-folder' }} text-white"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $category->name }}</div>
                                    @if($category->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($category->description, 30) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $category->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($category->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $category->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $category->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $category->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="editCategory({{ $category->id }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteCategory({{ $category->id }})" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button onclick="viewCategory({{ $category->id }})" 
                                        class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Modal (Contoh) -->
<div id="categoryModal" class="modal-overlay hidden">
    <div class="modal-content w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4" id="modalTitle">Create Category</h3>
            
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId" name="id">
                
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" id="categoryName" class="form-input" required>
                    </div>
                    
                    <div>
                        <label class="form-label">Type</label>
                        <select name="type" id="categoryType" class="form-input" required>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" id="categoryDescription" class="form-input" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()" class="btn btn-outline">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Create Category';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryModal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('categoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush