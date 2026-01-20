@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" id="summaryCards">
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Pemasukan</p>
                    <p class="text-2xl font-bold text-green-600 mt-1" id="totalIncome">Rp 0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-green-500 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-red-600 mt-1" id="totalExpense">Rp 0</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-red-500 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Saldo Bersih</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1" id="netBalance">Rp 0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Accounts Summary -->
    <div class="bg-white rounded-xl shadow-sm border mb-8">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-800">Saldo per Akun Bank</h2>
        </div>
        <div class="p-6">
            <div id="bankAccountsList" class="space-y-4">
                <p class="text-gray-500 text-center py-8">Memuat data...</p>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl shadow-sm border">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-800">Riwayat Transaksi Terkini</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    </tr>
                </thead>
                <tbody id="recentTransactions" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    loadDashboard();
});

function loadDashboard() {
    $.ajax({
        url: '/dashboard/summary',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                // Update summary cards
                $('#totalIncome').text(formatCurrency(data.total_income));
                $('#totalExpense').text(formatCurrency(data.total_expense));
                $('#netBalance').text(formatCurrency(data.net_balance));
                
                // Update bank accounts
                let accountsHtml = '';
                if (data.bank_accounts.length > 0) {
                    data.bank_accounts.forEach(account => {
                        accountsHtml += `
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-800">${account.bank_name}</p>
                                    <p class="text-sm text-gray-500">${account.account_number} - ${account.type}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold ${account.balance >= 0 ? 'text-green-600' : 'text-red-600'}">
                                        ${formatCurrency(account.balance)}
                                    </p>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    accountsHtml = '<p class="text-gray-500 text-center py-4">Belum ada akun bank</p>';
                }
                $('#bankAccountsList').html(accountsHtml);
                
                // Update recent transactions
                let transactionsHtml = '';
                if (data.recent_transactions.length > 0) {
                    data.recent_transactions.forEach(transaction => {
                        const typeClass = transaction.type === 'income' ? 'text-green-600' : 'text-red-600';
                        const typeIcon = transaction.type === 'income' ? 'fa-arrow-down' : 'fa-arrow-up';
                        transactionsHtml += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${formatDate(transaction.date)}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${transaction.bank_account ? transaction.bank_account.bank_name : '-'}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${transaction.category ? transaction.category.name : '-'}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full ${transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${transaction.type === 'income' ? 'Pemasukan' : 'Pengeluaran'}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold ${typeClass}">
                                    ${formatCurrency(transaction.amount)}
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    transactionsHtml = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada transaksi</td></tr>';
                }
                $('#recentTransactions').html(transactionsHtml);
            }
        },
        error: function(xhr) {
            console.error('Error loading dashboard:', xhr);
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
</script>
@endpush
@endsection



