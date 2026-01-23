@extends('layouts.auth')

@section('title', __('messages.auth_register_button'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 relative">
    <div class="absolute top-6 right-6">
        <x-language-switcher />
    </div>
    
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-university text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('messages.app_name') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('messages.auth_register_title') }}</p>
        </div>

        <form id="registerForm" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.label_name') }}</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.label_email') }}</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.label_password') }}</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.auth_confirm_password') }}</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg font-medium transition">
                {{ __('messages.auth_register_button') }}
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                {{ __('messages.auth_already_have_account') }}
                <a href="/login" class="text-blue-500 hover:text-blue-600 font-medium">{{ __('messages.auth_login_here') }}</a>
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
    
    errorDiv.addClass('hidden').html('');
    
    if (password !== passwordConfirm) {
        errorDiv.text("{{ __('messages.register_password_mismatch') }}").removeClass('hidden');
        return;
    }
    
    submitBtn.prop('disabled', true).text("{{ __('messages.auth_registering') }}");
    
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
                const errors = Object.values(xhr.responseJSON.errors).flat();
                console.log('Validation Errors:', errors);
                errorDiv.html(errors.join('<br>')).removeClass('hidden');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                console.log('Message Error:', xhr.responseJSON.message);
                errorDiv.text(xhr.responseJSON.message).removeClass('hidden');
            } else if (xhr.status >= 500) {
                errorDiv.text("{{ __('messages.register_server_error') }}").removeClass('hidden');
            } else {
                errorDiv.text("{{ __('messages.register_error') }}").removeClass('hidden');
            }
            
            submitBtn.prop('disabled', false).text('Daftar');
        }
    });
});
</script>
@endpush
@endsection



