@extends('layouts.app')

@section('title', __('messages.transactions_title'))
@section('page-title', __('messages.transactions_manage'))

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.transactions_title') }}</h1>
        <button onclick="openCreateModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            {{ __('messages.transactions_add') }}
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.table_account') }}</label>
                <select id="filterAccount" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">{{ __('messages.categories_all_types') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.table_category') }}</label>
                <select id="filterCategory" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">{{ __('messages.categories_all_types') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.filter_start_date') }}</label>
                <input type="date" id="filterStartDate" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.filter_end_date') }}</label>
                <input type="date" id="filterEndDate" class="w-full px-3 py-2 border rounded-lg">
            </div>
        </div>
        <div class="mt-4">
            <button onclick="applyFilters()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                {{ __('messages.categories_apply') }}
            </button>
            <button onclick="resetFilters()" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                {{ __('messages.button_cancel') }}
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.table_date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.table_account') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.table_category') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.table_type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.table_amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.table_description') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.table_actions') }}</th>
                    </tr>
                </thead>
                <tbody id="transactionsTable" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">{{ __('messages.dashboard_loading') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="transactionModal" class="modal-overlay hidden">
    <div class="modal-content w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4" id="modalTitle">{{ __('messages.transactions_add_modal') }}</h3>
            
            <form id="transactionForm">
                @csrf
                <input type="hidden" id="transactionId" name="id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.table_account') }}</label>
                        <select name="bank_account_id" id="transactionAccount" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">{{ __('messages.transactions_select_account') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.table_category') }}</label>
                        <select name="category_id" id="transactionCategory" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">{{ __('messages.transactions_select_category') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.table_type') }}</label>
                        <select name="type" id="transactionType" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="income">{{ __('messages.transactions_income') }}</option>
                            <option value="expense">{{ __('messages.transactions_expense') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.table_amount') }}</label>
                        <input type="number" step="0.01" name="amount" id="transactionAmount" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.table_date') }}</label>
                        <input type="date" name="date" id="transactionDate" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.label_description') }}</label>
                        <textarea name="description" id="transactionDescription" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        {{ __('messages.button_cancel') }}
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        {{ __('messages.button_save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const translations = {
    income: "{{ __('messages.transactions_income') }}",
    expense: "{{ __('messages.transactions_expense') }}",
    deleteConfirm: "{{ __('messages.transaction_delete_confirm') }}",
    deleteSuccess: "{{ __('messages.transaction_deleted_successfully') }}",
    deleteError: "{{ __('messages.transaction_delete_error') }}",
    selectAccount: "{{ __('messages.transactions_select_account') }}",
    selectCategory: "{{ __('messages.select_category_optional') }}",
    allAccounts: "{{ __('messages.filter_all_accounts') }}",
    allCategories: "{{ __('messages.filter_all_categories') }}"
};

$(document).ready(function() {
    loadTransactions();
    loadBankAccounts();
    loadCategories();
    
    $('#transactionForm').on('submit', function(e) {
        e.preventDefault();
        saveTransaction();
    });
    
    $('#transactionDate').val(new Date().toISOString().split('T')[0]);
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
                let options = '<option value="">' + translations.selectAccount + '</option>';
                response.data.forEach(account => {
                    options += `<option value="${account.id}">${account.bank_name} - ${account.account_number}</option>`;
                });
                $('#transactionAccount').html(options);
                $('#filterAccount').html('<option value="">' + translations.allAccounts + '</option>' + response.data.map(a => `<option value="${a.id}">${a.bank_name} - ${a.account_number}</option>`).join(''));
            }
        },
        error: function(xhr) {
            console.error('Error loading bank accounts:', xhr);
        }
    });
}

function loadCategories() {
    $.ajax({
        url: '/api/categories',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                let options = '<option value="">' + translations.selectCategory + '</option>';
                response.data.forEach(category => {
                    options += `<option value="${category.id}">${category.name} (${category.type})</option>`;
                });
                $('#transactionCategory').html(options);
                $('#filterCategory').html('<option value="">' + translations.allCategories + '</option>' + response.data.map(c => `<option value="${c.id}">${c.name}</option>`).join(''));
            }
        },
        error: function(xhr) {
            console.error('Error loading categories:', xhr);
        }
    });
}

function loadTransactions() {
    const params = new URLSearchParams();
    if ($('#filterAccount').val()) params.append('bank_account_id', $('#filterAccount').val());
    if ($('#filterCategory').val()) params.append('category_id', $('#filterCategory').val());
    if ($('#filterStartDate').val()) params.append('start_date', $('#filterStartDate').val());
    if ($('#filterEndDate').val()) params.append('end_date', $('#filterEndDate').val());
    
    $.ajax({
        url: '/api/transactions' + (params.toString() ? '?' + params.toString() : ''),
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                let html = '';
                if (response.data.length > 0) {
                    response.data.forEach(transaction => {
                        const typeClass = transaction.type === 'income' ? 'text-green-600' : 'text-red-600';
                        html += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(transaction.date)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaction.bank_account ? transaction.bank_account.bank_name : '-'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaction.category ? transaction.category.name : '-'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full ${transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${transaction.type === 'income' ? translations.income : translations.expense}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold ${typeClass}">
                                    ${formatCurrency(transaction.amount)}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">${transaction.description || '-'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editTransaction(${transaction.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteTransaction(${transaction.id})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">{{ __("messages.no_transactions") }}</td></tr>';
                }
                $('#transactionsTable').html(html);
            }
        },
        error: function(xhr) {
            console.error('Error loading transactions:', xhr);
            $('#transactionsTable').html('<tr><td colspan="7" class="px-6 py-8 text-center text-red-500">{{ __("messages.error_loading_data") }}</td></tr>');
        }
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function openCreateModal() {
    $('#modalTitle').text("{{ __('messages.transactions_add_modal') }}");
    $('#transactionForm')[0].reset();
    $('#transactionId').val('');
    $('#transactionDate').val(new Date().toISOString().split('T')[0]);
    $('#transactionModal').removeClass('hidden');
}

function closeModal() {
    $('#transactionModal').addClass('hidden');
}

function editTransaction(id) {
    $.ajax({
        url: `/api/transactions/${id}`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const transaction = response.data;
                $('#modalTitle').text("{{ __('messages.transactions_edit_modal') }}");
                $('#transactionId').val(transaction.id);
                $('#transactionAccount').val(transaction.bank_account_id);
                $('#transactionCategory').val(transaction.category_id || '');
                $('#transactionType').val(transaction.type);
                $('#transactionAmount').val(transaction.amount);
                $('#transactionDate').val(transaction.date);
                $('#transactionDescription').val(transaction.description || '');
                $('#transactionModal').removeClass('hidden');
            }
        },
        error: function(xhr) {
            console.error('Error loading transaction:', xhr);
            alert("{{ __('messages.transaction_load_error') }}");
        }
    });
}

function saveTransaction() {
    const id = $('#transactionId').val();
    const url = id ? `/api/transactions/${id}` : '/api/transactions';
    const method = id ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        method: method,
        data: $('#transactionForm').serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                closeModal();
                loadTransactions();
                alert("{{ __('messages.transaction_saved_successfully') }}");
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                alert('Error: ' + Object.values(xhr.responseJSON.errors).flat().join(', '));
            } else {
                alert("{{ __('messages.transaction_save_error') }}");
            }
        }
    });
}

function deleteTransaction(id) {
    if (!confirm(translations.deleteConfirm)) {
        return;
    }
    
    $.ajax({
        url: `/api/transactions/${id}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                loadTransactions();
                alert(translations.deleteSuccess);
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON) {
                alert(xhr.responseJSON.message || translations.deleteError);
            } else {
                alert(translations.deleteError);
            }
        }
    });
}

function applyFilters() {
    loadTransactions();
}

function resetFilters() {
    $('#filterAccount').val('');
    $('#filterCategory').val('');
    $('#filterStartDate').val('');
    $('#filterEndDate').val('');
    loadTransactions();
}

$('#transactionModal').on('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush
@endsection


