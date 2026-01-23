@extends('layouts.app')

@section('title', __('messages.profile_title'))
@section('page-title', __('messages.profile_manage'))

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">{{ __('messages.profile_title') }}</h2>
        
        <form id="profileForm" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.label_name') }}</label>
                <input type="text" name="name" id="userName" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.label_email') }}</label>
                <input type="email" name="email" id="userEmail" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.profile_new_password') }} </label>
                <input type="password" name="password" id="userPassword" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.auth_confirm_password') }}</label>
                <input type="password" name="password_confirmation" id="userPasswordConfirmation" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                    {{ __('messages.button_save') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-xl font-semibold text-red-800 mb-4">{{ __('messages.message_warning') }}</h2>
        <p class="text-gray-600 mb-4">{{ __('messages.message_confirm_delete') }}</p>

        <button type="button" onclick="openDeleteModal()" class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
            {{ __('messages.button_delete') }}
        </button>
    </div>
</div>

<div id="deleteAccountModal" class="modal-overlay hidden">
    <div class="modal-content w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-red-800 mb-4">{{ __('messages.button_delete') }}</h3>
            <p class="text-gray-600 mb-4">{{ __('messages.profile_current_password') }}</p>
            
            <form id="deleteAccountForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.label_password') }}</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        {{ __('messages.button_cancel') }}
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        {{ __('messages.button_delete') }}
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
                alert("{{ __('messages.profile_updated_successfully') }}");
                loadProfile();
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                alert('Error: ' + Object.values(xhr.responseJSON.errors).flat().join(', '));
            } else {
                alert("{{ __('messages.profile_update_error') }}");
            }
        }
    });
}

function openDeleteModal() {
    if (!confirm("{{ __('messages.profile_delete_confirm') }}")) {
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
                alert("{{ __('messages.profile_deleted_successfully') }}");
                window.location.href = '/login';
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alert(xhr.responseJSON.message);
            } else {
                alert("{{ __('messages.profile_delete_error') }}");
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

