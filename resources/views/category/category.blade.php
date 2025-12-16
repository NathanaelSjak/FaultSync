@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Manajemen Kategori')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Kategori</h1>
        <button onclick="openCreateModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Kategori
        </button>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-6 flex gap-4">
        <select id="filterType" class="px-3 py-2 border rounded-lg">
            <option value="">Semua Tipe</option>
            <option value="income">Pemasukan</option>
            <option value="expense">Pengeluaran</option>
        </select>

        <input type="text" id="searchCategory" placeholder="Cari kategori..."
               class="px-3 py-2 border rounded-lg w-64">

        <button onclick="loadCategories()" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
            Terapkan
        </button>
    </div>

    <!-- Category Grid -->
    <div id="categoryGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <p class="text-gray-500 col-span-full text-center">Memuat data...</p>
    </div>
</div>

<!-- Modal -->
<div id="categoryModal" class="modal-overlay hidden">
    <div class="modal-content w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4" id="modalTitle">Tambah Kategori</h3>

            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm mb-1">Nama</label>
                        <input type="text" id="categoryName" required
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Tipe</label>
                        <select id="categoryType" required
                                class="w-full px-3 py-2 border rounded-lg">
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Warna</label>
                        <input type="color" id="categoryColor"
                               class="w-full h-10 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Deskripsi</label>
                        <textarea id="categoryDescription"
                                  class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 border rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg">
                        Simpan
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
});

function loadCategories() {
    const params = {
        type: $('#filterType').val(),
        search: $('#searchCategory').val()
    };

    $.get('/categories', params, function (res) {
        if (!res.success) return;

        let html = '';
        if (res.data.length === 0) {
            html = `<p class="col-span-full text-center text-gray-500">Tidak ada kategori</p>`;
        }

        res.data.forEach(cat => {
            html += `
            <div class="bg-white border rounded-xl p-4 shadow-sm hover:shadow transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-3 py-1 text-xs rounded-full"
                          style="background:${cat.color};color:white">
                        ${cat.type === 'income' ? 'Pemasukan' : 'Pengeluaran'}
                    </span>
                    <div class="flex gap-2">
                        <button onclick="editCategory(${cat.id})" class="text-blue-500">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteCategory(${cat.id})" class="text-red-500">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <h4 class="font-semibold text-gray-800">${cat.name}</h4>
                <p class="text-sm text-gray-500">${cat.description ?? '-'}</p>
            </div>`;
        });

        $('#categoryGrid').html(html);
    });
}

function openCreateModal() {
    $('#modalTitle').text('Tambah Kategori');
    $('#categoryForm')[0].reset();
    $('#categoryId').val('');
    $('#categoryModal').removeClass('hidden');
}

function closeModal() {
    $('#categoryModal').addClass('hidden');
}

function editCategory(id) {
    $.get(`/categories/${id}`, function (res) {
        const c = res.data;
        $('#modalTitle').text('Edit Kategori');
        $('#categoryId').val(c.id);
        $('#categoryName').val(c.name);
        $('#categoryType').val(c.type);
        $('#categoryColor').val(c.color);
        $('#categoryDescription').val(c.description);
        $('#categoryModal').removeClass('hidden');
    });
}

function saveCategory() {
    const id = $('#categoryId').val();
    const url = id ? `/categories/${id}` : '/categories';
    const method = id ? 'PUT' : 'POST';

    $.ajax({
        url, method,
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            name: $('#categoryName').val(),
            type: $('#categoryType').val(),
            color: $('#categoryColor').val(),
            description: $('#categoryDescription').val()
        },
        success() {
            closeModal();
            loadCategories();
            alert('Kategori berhasil disimpan');
        }
    });
}

function deleteCategory(id) {
    if (!confirm('Hapus kategori ini?')) return;

    $.ajax({
        url: `/categories/${id}`,
        method: 'DELETE',
        data: { _token: $('meta[name="csrf-token"]').attr('content') },
        success() {
            loadCategories();
            alert('Kategori dihapus');
        }
    });
}
</script>
@endpush