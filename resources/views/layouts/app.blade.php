<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bank App') - FaultSync</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #2563eb;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        .sidebar-link {
            @apply flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors hover:bg-gray-100;
        }
        
        .sidebar-link.active {
            @apply bg-blue-50 text-blue-600 font-medium;
        }
        
        .modal-overlay {
            @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
        }
        
        .modal-content {
            @apply bg-white rounded-xl shadow-xl max-h-[90vh] overflow-y-auto;
        }
    </style>
    
    @stack('styles')
</head>
<body class="h-full bg-gray-50">
    @auth
    {{-- Sidebar --}}
    <aside class="sidebar fixed left-0 top-0 h-full w-64 bg-white shadow-lg z-50">
        <div class="p-6 border-b">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-university text-white text-lg"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">FaultSync</h1>
            </div>
        </div>
        
        <nav class="p-4 space-y-2">
            <a href="/dashboard" class="sidebar-link {{ request()->is('dashboard*') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard
            </a>
            
            <a href="/bank-accounts" class="sidebar-link {{ request()->is('bank-accounts*') ? 'active' : '' }}">
                <i class="fas fa-university mr-3"></i>
                Akun Bank
            </a>
            
            <a href="/categories" class="sidebar-link {{ request()->is('categories*') ? 'active' : '' }}">
                <i class="fas fa-tags mr-3"></i>
                Kategori
            </a>
            
            <a href="/transactions" class="sidebar-link {{ request()->is('transactions*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt mr-3"></i>
                Transaksi
            </a>
            
            <a href="/profile" class="sidebar-link {{ request()->is('profile*') ? 'active' : '' }}">
                <i class="fas fa-user mr-3"></i>
                Profil
            </a>
        </nav>
        
        <div class="absolute bottom-0 w-full p-4 border-t">
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>
                <form action="/auth/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-500">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>
    @endauth

    {{-- Main Content --}}
    <div class="@auth ml-64 @endauth min-h-screen">
        @auth
        {{-- Navbar --}}
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                </div>
            </div>
        </header>
        @endauth

        {{-- Page Content --}}
        <main class="p-6">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-green-700">{{ session('success') }}</span>
                    </div>
                    <button class="text-green-500 hover:text-green-700" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span class="text-red-700">{{ session('error') }}</span>
                    </div>
                    <button class="text-red-500 hover:text-red-700" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            
            @if(isset($errors) && $errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        <h4 class="text-red-700 font-semibold">Validation Errors</h4>
                    </div>
                    <ul class="text-red-600 text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>

