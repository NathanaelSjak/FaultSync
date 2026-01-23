@extends('layouts.app')

@section('title', __('messages.categories_title'))
@section('page-title', __('messages.categories_manage'))

@section('content')
<div class="container mx-auto px-4">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.categories_title') }}</h1>
        <button onclick="openCreateModal()"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            {{ __('messages.categories_add') }}
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-4 mb-6
                flex flex-col md:flex-row md:items-center gap-4">
        <select id="filterType" class="px-3 py-2 border rounded-lg">
            <option value="">{{ __('messages.categories_all_types') }}</option>
            <option value="income">{{ __('messages.categories_income') }}</option>
            <option value="expense">{{ __('messages.categories_expense') }}</option>
        </select>

        <input type="text" id="searchCategory"
            placeholder="{{ __('messages.categories_search') }}"
            class="px-3 py-2 border rounded-lg w-full md:w-64">

        <button onclick="loadCategories()"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
            {{ __('messages.categories_apply') }}
        </button>
    </div>

    <div id="categoryGrid"
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <p class="text-gray-500 col-span-full text-center">{{ __('messages.loading') }}</p>
    </div>
</div>

<div id="categoryModal" class="modal-overlay hidden">
    <div class="modal-content w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4" id="modalTitle">
                {{ __('messages.category_title_add') }}
            </h3>

            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('messages.label_category_name') }}</label>
                        <input type="text" id="categoryName" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('messages.label_category_type') }}</label>
                        <select id="categoryType" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="income">{{ __('messages.categories_income') }}</option>
                            <option value="expense">{{ __('messages.categories_expense') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('messages.label_category_icon') }}</label>
                        <div class="relative">
                            <select id="categoryIcon" required
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 appearance-none">
                                <option value="fas fa-tag">Tag</option>
                                <option value="fas fa-money-bill-wave">Uang</option>
                                <option value="fas fa-wallet">Dompet</option>
                                <option value="fas fa-coins">Koin</option>
                                <option value="fas fa-piggy-bank">Celengan</option>
                                <option value="fas fa-credit-card">Kartu Kredit</option>
                                <option value="fas fa-shopping-cart">Belanja</option>
                                <option value="fas fa-utensils">Makanan</option>
                                <option value="fas fa-car">Mobil</option>
                                <option value="fas fa-home">Rumah</option>
                                <option value="fas fa-heart">Kesehatan</option>
                                <option value="fas fa-graduation-cap">Pendidikan</option>
                                <option value="fas fa-film">Hiburan</option>
                                <option value="fas fa-soccer">Olahraga</option>
                                <option value="fas fa-file-invoice">Tagihan</option>
                                <option value="fas fa-chart-line">Investasi</option>
                                <option value="fas fa-gift">Hadiah</option>
                                <option value="fas fa-laptop">Freelance</option>
                                <option value="fas fa-briefcase">Pekerjaan</option>
                                <option value="fas fa-exchange-alt">Transfer</option>
                                <option value="fas fa-ellipsis-h">⋯ Lainnya</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i id="selectedIconPreview" class="fas fa-tag text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('messages.label_color') }}</label>
                        <input type="color" id="categoryColor" value="#6c757d"
                            class="w-full h-10 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('messages.label_description') }}</label>
                        <textarea id="categoryDescription"
                            class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        {{ __('messages.button_cancel') }}
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                        {{ __('messages.button_save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    loadCategories();

    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();
        saveCategory();
    });

    $('#categoryIcon').on('change', function() {
        updateIconPreview();
    });
});

function loadCategories() {
    const params = {
        type: $('#filterType').val(),
        search: $('#searchCategory').val()
    };

    $.get('/api/categories', params, function (res) {
        if (!res.success) return;

        let html = '';

        if (res.data.length === 0) {
            html = `<p class="col-span-full text-center text-gray-500">{{ __("messages.no_categories") }}</p>`;
        }

        res.data.forEach(cat => {
            html += `
            <div class="bg-white border rounded-xl p-4 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-3 py-1 text-xs rounded-full"
                        style="background:${cat.color};color:white">
                        ${cat.type === 'income' ? "{{ __('messages.categories_income') }}" : "{{ __('messages.categories_expense') }}"}
                    </span>
                    <div class="flex gap-2">
                        <button onclick="editCategory(${cat.id})" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteCategory(${cat.id})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <i class="${cat.icon ?? 'fas fa-tag'} text-gray-500"></i>
                    <h4 class="font-semibold text-gray-800">${cat.name}</h4>
                </div>

                <p class="text-sm text-gray-500 mt-1">${cat.description ?? '-'}</p>
            </div>`;
        });

        $('#categoryGrid').html(html);
    }).fail(function (xhr) {
        console.error('Error loading categories:', xhr);
        $('#categoryGrid').html(
            '<p class="col-span-full text-center text-red-500">Gagal memuat kategori. Coba refresh halaman.</p>'
        );
    });
}

function openCreateModal() {
    $('#modalTitle').text("{{ __('messages.categories_add_modal') }}");
    $('#categoryForm')[0].reset();
    $('#categoryId').val('');
    $('#categoryColor').val('#6c757d');
    $('#categoryIcon').val('fas fa-tag');
    updateIconPreview();
    $('#categoryModal').removeClass('hidden');
}

function updateIconPreview() {
    const selectedIcon = $('#categoryIcon').val();
    $('#selectedIconPreview').attr('class', selectedIcon + ' text-gray-400');
}

function closeModal() {
    $('#categoryModal').addClass('hidden');
}

function editCategory(id) {
    $.get(`/categories/${id}`, function (res) {
        if (!res.success) {
            alert("{{ __('messages.category_load_error') }}");
            return;
        }
        const c = res.data;
        $('#modalTitle').text("{{ __('messages.categories_edit_modal') }}");
        $('#categoryId').val(c.id);
        $('#categoryName').val(c.name);
        $('#categoryType').val(c.type);
        $('#categoryColor').val(c.color || '#6c757d');
        $('#categoryIcon').val(c.icon || 'fas fa-tag');
        $('#categoryDescription').val(c.description || '');
        updateIconPreview();
        $('#categoryModal').removeClass('hidden');
    }).fail(function(xhr) {
        console.error('Error loading category:', xhr);
        alert("{{ __('messages.category_load_error') }}");
    });
}

function saveCategory() {
    const id = $('#categoryId').val();
    const url = id ? `/categories/${id}` : '/categories';
    const method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        type: method,
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            name: $('#categoryName').val(),
            type: $('#categoryType').val(),
            icon: $('#categoryIcon').val() || 'fas fa-tag',
            color: $('#categoryColor').val() || '#6c757d',
            description: $('#categoryDescription').val()
        },
        success(res) {
            if (!res || res.success === false) {
                alert(res && res.message ? res.message : "{{ __('messages.category_saved_successfully') }}");
                return;
            }
            closeModal();
            loadCategories();
            alert("{{ __('messages.category_saved_successfully') }}");
        },
        error(xhr) {
            console.error('Error saving category:', xhr);
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                alert('Error: ' + Object.values(xhr.responseJSON.errors).flat().join(', '));
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                alert(xhr.responseJSON.message);
            } else {
                alert("{{ __('messages.category_saved_successfully') }}");
            }
        }
    });
}

function deleteCategory(id) {
    if (!confirm('Hapus kategori ini?')) return;

    $.ajax({
        url: `/categories/${id}`,
        type: 'DELETE',
        data: { _token: $('meta[name="csrf-token"]').attr('content') },
        success(res) {
            if (!res || res.success === false) {
                alert(res && res.message ? res.message : 'Gagal menghapus kategori');
                return;
            }
            loadCategories();
            alert('Kategori dihapus');
        },
        error(xhr) {
            console.error('Error deleting category:', xhr);
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alert(xhr.responseJSON.message);
            } else {
                alert('Terjadi kesalahan saat menghapus kategori');
            }
        }
    });
}

$('#categoryModal').on('click', function (e) {
    if (e.target === this) closeModal();
});
</script>
@endpush