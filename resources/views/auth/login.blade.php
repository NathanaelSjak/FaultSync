@extends('layouts.auth')

@section('title', 'Login')

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
            <p class="text-gray-600 mt-2">{{ __('messages.auth_login_title') }}</p>
        </div>

        <form id="loginForm" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.label_email') }}</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.label_password') }}</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-sm text-gray-600">{{ __('messages.auth_remember_me') }}</span>
                </label>
            </div>
            
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg font-medium transition">
                {{ __('messages.auth_login_button') }}
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                {{ __('messages.auth_dont_have_account') }}
                <a href="/register" class="text-blue-500 hover:text-blue-600 font-medium">{{ __('messages.auth_register_here') }}</a>
            </p>
        </div>
        
        <div id="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm hidden"></div>
    </div>
</div>

@push('scripts')
<script>
const loginTranslations = {
    processing: "{{ __('messages.auth_logging_in') }}",
    serverError: "{{ __('messages.auth_server_error') }}",
    loginError: "{{ __('messages.message_error') }}"
};

$('#loginForm').on('submit', function(e) {
    e.preventDefault();
    
    const errorDiv = $('#errorMessage');
    const submitBtn = $(this).find('button[type="submit"]');
    
    errorDiv.addClass('hidden').html('');
    
    submitBtn.prop('disabled', true).text(loginTranslations.processing);
    
    $.ajax({
        url: '/auth/login',
        method: 'POST',
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                window.location.href = '/dashboard';
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorDiv.html(errors.join('<br>')).removeClass('hidden');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorDiv.text(xhr.responseJSON.message).removeClass('hidden');
            } else if (xhr.status >= 500) {
                errorDiv.text(loginTranslations.serverError).removeClass('hidden');
            } else {
                errorDiv.text(loginTranslations.loginError).removeClass('hidden');
            }
            
            submitBtn.prop('disabled', false).text("{{ __('messages.auth_login_button') }}");
        }
    });
});
</script>
@endpush
@endsection



