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
        
        /* Navigation container - vertical layout */
        nav {
            display: flex !important;
            flex-direction: column !important;
        }
        
        nav > div {
            display: flex !important;
            flex-direction: column !important;
            gap: 4px;
        }
        
        /* Sidebar link styling - horizontal items in vertical list */
        .sidebar-link {
            @apply flex items-center px-4 py-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 w-full;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            width: 100% !important;
            margin-bottom: 4px;
        }
        
        .sidebar-link.active {
            @apply bg-blue-50 text-blue-600 font-medium shadow-sm;
        }
        
        .sidebar-link i {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }
        
        .sidebar-link span {
            flex: 1;
        }
        
        .sidebar {
            transition: transform 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            /* Overlay when sidebar is open */
            .sidebar.open::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: -1;
            }
        }
        
        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
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
    <aside class="sidebar fixed left-0 top-0 h-full w-64 bg-white shadow-lg z-50 flex flex-col">
        <div class="p-6 border-b">
            <a href="/dashboard" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-university text-white text-lg"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">FaultSync</h1>
            </a>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-4">
            <div class="px-4 space-y-1">
                <a href="/dashboard" class="sidebar-link {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="/bank-accounts" class="sidebar-link {{ request()->is('bank-accounts*') ? 'active' : '' }}">
                    <i class="fas fa-university mr-3"></i>
                    <span>Akun Bank</span>
                </a>
                
                <a href="/categories" class="sidebar-link {{ request()->is('categories*') ? 'active' : '' }}">
                    <i class="fas fa-tags mr-3"></i>
                    <span>Kategori</span>
                </a>
                
                <a href="/transactions" class="sidebar-link {{ request()->is('transactions*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt mr-3"></i>
                    <span>Transaksi</span>
                </a>
                
                <a href="/profile" class="sidebar-link {{ request()->is('profile*') ? 'active' : '' }}">
                    <i class="fas fa-user mr-3"></i>
                    <span>Profil</span>
                </a>
            </div>
        </nav>
        
        <div class="absolute bottom-0 w-full p-4 border-t bg-white">
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                </div>
                <form id="logoutForm" action="/auth/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>
    @endauth

    {{-- Main Content --}}
    <div class="@auth ml-64 @endauth min-h-screen main-content">
        @auth
        {{-- Top Navbar --}}
        <header class="bg-white shadow-sm sticky top-0 z-40 border-b">
            <div class="px-4 md:px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button id="mobileMenuToggle" class="md:hidden text-gray-600 hover:text-gray-800 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                            @hasSection('breadcrumb')
                            <nav class="flex items-center space-x-2 text-sm text-gray-500 mt-1">
                                <a href="/dashboard" class="hover:text-blue-500 transition-colors">Dashboard</a>
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span class="text-gray-700">@yield('breadcrumb')</span>
                            </nav>
                            @endif
                        </div>
                    </div>
                    <div class="hidden md:flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        </div>
                    </div>
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
        // Mobile menu toggle
        document.getElementById('mobileMenuToggle')?.addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.toggle('open');
            }
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.getElementById('mobileMenuToggle');
            
            if (window.innerWidth < 768 && sidebar && toggle) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });

        // Handle logout form submission
        document.getElementById('logoutForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/login';
                } else {
                    alert('Gagal logout: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                // Fallback: redirect anyway
                window.location.href = '/login';
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Sidebar scroll behavior
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.sidebar');
            if (window.innerWidth >= 768 && sidebar) {
                sidebar.classList.remove('open');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>