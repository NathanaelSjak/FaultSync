<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Category Management') - YourApp</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/category.css') }}">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
        }
        
        /* Custom Scrollbar */
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
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    
    @stack('styles')
</head>
<body class="h-full bg-gray-50">
    
    {{-- Sidebar --}}
    <aside class="sidebar fixed left-0 top-0 h-full w-64 bg-white shadow-lg z-50">
        <div class="p-6 border-b">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-white text-lg"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">YourApp</h1>
            </div>
        </div>
        
        <nav class="p-4 space-y-2">
            <a href="/dashboard" class="sidebar-link">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard
            </a>
            
            <a href="/categories" class="sidebar-link active">
                <i class="fas fa-tags mr-3"></i>
                Categories
            </a>
            
            <a href="/transactions" class="sidebar-link">
                <i class="fas fa-exchange-alt mr-3"></i>
                Transactions
            </a>
            
            <a href="/reports" class="sidebar-link">
                <i class="fas fa-chart-bar mr-3"></i>
                Reports
            </a>
            
            <a href="/settings" class="sidebar-link">
                <i class="fas fa-cog mr-3"></i>
                Settings
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
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="text-gray-400 hover:text-red-500">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="main-content ml-64 min-h-screen">
        
        {{-- Navbar --}}
        <header class="navbar bg-white shadow-sm sticky top-0 z-40">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Category Management')</h2>
                    <nav class="flex space-x-2 text-sm text-gray-600 mt-1">
                        <a href="/" class="hover:text-blue-500">Home</a>
                        <span>/</span>
                        <a href="/categories" class="hover:text-blue-500">Categories</a>
                        @hasSection('breadcrumb')
                            <span>/</span>
                            <span class="text-gray-800">@yield('breadcrumb')</span>
                        @endif
                    </nav>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button class="relative text-gray-600 hover:text-gray-800">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                    </button>
                    
                    <div class="relative group">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                            <span class="text-gray-700 font-medium hidden md:inline">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </button>
                        
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-2 hidden group-hover:block z-50">
                            <a href="/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="/settings" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <div class="border-t my-2"></div>
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();"
                               class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                            <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="p-6">
            {{-- Alert Messages --}}
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
            
            @if($errors->any())
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
            
            {{-- Main Content --}}
            @yield('content')
        </main>
        
        {{-- Footer --}}
        <footer class="bg-white border-t px-6 py-4 mt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-600 text-sm">
                    &copy; {{ date('Y') }} YourApp. All rights reserved.
                </div>
                <div class="flex space-x-4 mt-2 md:mt-0">
                    <a href="/privacy" class="text-gray-600 hover:text-blue-500 text-sm">Privacy Policy</a>
                    <a href="/terms" class="text-gray-600 hover:text-blue-500 text-sm">Terms of Service</a>
                    <a href="/help" class="text-gray-600 hover:text-blue-500 text-sm">Help Center</a>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/category.js') }}"></script>
    
    <script>
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.group');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target)) {
                    const menu = dropdown.querySelector('.group-hover\\:block');
                    if (menu) menu.classList.add('hidden');
                }
            });
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[class*="bg-"]');
            alerts.forEach(alert => {
                if (alert.classList.contains('bg-green-50') || alert.classList.contains('bg-red-50')) {
                    alert.remove();
                }
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>