@extends('layouts.app')

@section('title', 'Profil')
@section('page-title', 'Profil Saya')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Informasi Profil</h2>
        
        <form id="profileForm" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" id="userName" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="userEmail" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" id="userPassword" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="userPasswordConfirmation" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-xl font-semibold text-red-800 mb-4">Zona Bahaya</h2>
        <p class="text-gray-600 mb-4">Menghapus akun akan menghapus semua data Anda secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
        
        <button onclick="openDeleteModal()" class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
            Hapus Akun
        </button>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteAccountModal" class="modal-overlay hidden">
    <div class="modal-content w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-red-800 mb-4">Hapus Akun</h3>
            <p class="text-gray-600 mb-4">Untuk menghapus akun, masukkan password Anda untuk konfirmasi.</p>
            
            <form id="deleteAccountForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    loadProfile();
    
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        updateProfile();
    });
    
    $('#deleteAccountForm').on('submit', function(e) {
        e.preventDefault();
        deleteAccount();
    });
});

function loadProfile() {
    $.ajax({
        url: '/profile/api',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const user = response.data;
                $('#userName').val(user.name);
                $('#userEmail').val(user.email);
            }
        }
    });
}

function updateProfile() {
    $.ajax({
        url: '/profile/api',
        method: 'PUT',
        data: $('#profileForm').serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert('Profil berhasil diperbarui');
                loadProfile();
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                alert('Error: ' + Object.values(xhr.responseJSON.errors).flat().join(', '));
            } else {
                alert('Terjadi kesalahan saat memperbarui profil');
            }
        }
    });
}

function openDeleteModal() {
    if (!confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    $('#deleteAccountModal').removeClass('hidden');
}

function closeDeleteModal() {
    $('#deleteAccountModal').addClass('hidden');
    $('#deleteAccountForm')[0].reset();
}

function deleteAccount() {
    $.ajax({
        url: '/profile/api',
        method: 'DELETE',
        data: $('#deleteAccountForm').serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert('Akun berhasil dihapus');
                window.location.href = '/login';
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alert(xhr.responseJSON.message);
            } else {
                alert('Terjadi kesalahan saat menghapus akun');
            }
        }
    });
}

$('#deleteAccountModal').on('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush
@endsection

