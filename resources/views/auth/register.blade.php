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
    
    const errorDiv = $('#errorMessage');
    const submitBtn = $(this).find('button[type="submit"]');
    const password = $('input[name="password"]').val();
    const passwordConfirm = $('input[name="password_confirmation"]').val();
    
    // Clear previous errors
    errorDiv.addClass('hidden').html('');
    
    // Validate password confirmation
    if (password !== passwordConfirm) {
        errorDiv.text('Password dan konfirmasi password tidak cocok').removeClass('hidden');
        return;
    }
    
    // Disable submit button and show loading state
    submitBtn.prop('disabled', true).text('Mendaftar...');
    
    const csrfToken = $('input[name="_token"]').val();
    console.log('CSRF Token:', csrfToken);
    console.log('Form Data:', $(this).serialize());
    
    $.ajax({
        url: '/auth/register',
        method: 'POST',
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        dataType: 'json',
        success: function(response) {
            console.log('Success:', response);
            if (response.success) {
                window.location.href = '/dashboard';
            }
        },
        error: function(xhr) {
            console.log('Error Status:', xhr.status);
            console.log('Error Response:', xhr.responseJSON);
            
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                // Validation errors
                const errors = Object.values(xhr.responseJSON.errors).flat();
                console.log('Validation Errors:', errors);
                errorDiv.html(errors.join('<br>')).removeClass('hidden');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                // API message error
                console.log('Message Error:', xhr.responseJSON.message);
                errorDiv.text(xhr.responseJSON.message).removeClass('hidden');
            } else if (xhr.status >= 500) {
                // Server error
                errorDiv.text('Terjadi kesalahan pada server. Silakan coba lagi nanti.').removeClass('hidden');
            } else {
                // Generic error
                errorDiv.text('Terjadi kesalahan saat registrasi').removeClass('hidden');
            }
            
            // Re-enable submit button
            submitBtn.prop('disabled', false).text('Daftar');
        }
    });
});
</script>
@endpush
@endsection

