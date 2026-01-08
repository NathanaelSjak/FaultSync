@extends('layouts.app')

@section('title', 'Akun Bank')
@section('page-title', 'Manajemen Akun Bank')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Akun Bank</h1>
        <button onclick="openCreateModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Akun Bank
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Rekening</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bankAccountsTable" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="bankAccountModal" class="modal-overlay hidden">
    <div class="modal-content w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4" id="modalTitle">Tambah Akun Bank</h3>
            
            <form id="bankAccountForm">
                @csrf
                <input type="hidden" id="bankAccountId" name="id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                        <input type="text" name="bank_name" id="bankName" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                        <input type="text" name="account_number" id="accountNumber" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <select name="type" id="accountType" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="savings">Tabungan</option>
                            <option value="checking">Giro</option>
                            <option value="credit">Kredit</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                        <textarea name="description" id="description" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    loadBankAccounts();
    
    $('#bankAccountForm').on('submit', function(e) {
        e.preventDefault();
        saveBankAccount();
    });
});

function loadBankAccounts() {
    $.ajax({
        url: '/api/bank-accounts',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                let html = '';
                if (response.data.length > 0) {
                    response.data.forEach(account => {
                        html += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${account.bank_name}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${account.account_number}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">${account.type}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">${account.description || '-'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editBankAccount(${account.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteBankAccount(${account.id})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada akun bank</td></tr>';
                }
                $('#bankAccountsTable').html(html);
            }
        },
        error: function(xhr) {
            console.error('Error loading bank accounts:', xhr);
            $('#bankAccountsTable').html('<tr><td colspan="5" class="px-6 py-8 text-center text-red-500">Error memuat data. Silakan refresh halaman.</td></tr>');
        }
    });
}

function openCreateModal() {
    $('#modalTitle').text('Tambah Akun Bank');
    $('#bankAccountForm')[0].reset();
    $('#bankAccountId').val('');
    $('#bankAccountModal').removeClass('hidden');
}

function closeModal() {
    $('#bankAccountModal').addClass('hidden');
}

function editBankAccount(id) {
    $.ajax({
        url: `/api/bank-accounts/${id}`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const account = response.data;
                $('#modalTitle').text('Edit Akun Bank');
                $('#bankAccountId').val(account.id);
                $('#bankName').val(account.bank_name);
                $('#accountNumber').val(account.account_number);
                $('#accountType').val(account.type);
                $('#description').val(account.description || '');
                $('#bankAccountModal').removeClass('hidden');
            }
        },
        error: function(xhr) {
            console.error('Error loading bank account:', xhr);
            alert('Terjadi kesalahan saat memuat data akun bank');
        }
    });
}

function saveBankAccount() {
    const id = $('#bankAccountId').val();
    const url = id ? `/api/bank-accounts/${id}` : '/api/bank-accounts';
    const method = id ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        method: method,
        data: $('#bankAccountForm').serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                closeModal();
                loadBankAccounts();
                alert('Akun bank berhasil disimpan');
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                alert('Error: ' + Object.values(xhr.responseJSON.errors).flat().join(', '));
            } else {
                alert('Terjadi kesalahan saat menyimpan');
            }
        }
    });
}

function deleteBankAccount(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus akun bank ini?')) {
        return;
    }
    
    $.ajax({
        url: `/api/bank-accounts/${id}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                loadBankAccounts();
                alert('Akun bank berhasil dihapus');
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON) {
                alert(xhr.responseJSON.message || 'Terjadi kesalahan saat menghapus');
            } else {
                alert('Terjadi kesalahan saat menghapus');
            }
        }
    });
}

$('#bankAccountModal').on('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush
@endsection


