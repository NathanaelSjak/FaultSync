@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-university text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">FaultSync</h1>
            <p class="text-gray-600 mt-2">Buat akun baru</p>
        </div>

        <form id="registerForm" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg font-medium transition">
                Daftar
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Sudah punya akun? 
                <a href="/login" class="text-blue-500 hover:text-blue-600 font-medium">Masuk di sini</a>
            </p>
        </div>
        
        <div id="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm hidden"></div>
    </div>
</div>

@push('scripts')
<script>
$('#registerForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '/auth/register',
        method: 'POST',
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                window.location.href = '/dashboard';
            }
        },
        error: function(xhr) {
            const errorDiv = $('#errorMessage');
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorDiv.html(errors.join('<br>')).removeClass('hidden');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorDiv.text(xhr.responseJSON.message).removeClass('hidden');
            } else {
                errorDiv.text('Terjadi kesalahan saat registrasi').removeClass('hidden');
            }
        }
    });
});
</script>
@endpush
@endsection

