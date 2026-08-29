<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIAM Dashboard')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Scripts & Styles -->
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Fallback if Vite is not compiled yet -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#f0fdf4',
                                100: '#dcfce7',
                                200: '#bbf7d0',
                                300: '#86efac',
                                400: '#4ade80',
                                500: '#22c55e',
                                600: '#16a34a',
                                700: '#15803d',
                                800: '#166534',
                                900: '#14532d',
                                950: '#052e16',
                            },
                            danger: {
                                50: '#fef2f2',
                                100: '#fee2e2',
                                500: '#ef4444',
                                600: '#dc2626',
                                700: '#b91c1c',
                            },
                            warning: {
                                50: '#fefce8',
                                100: '#fef08a',
                                600: '#ca8a04',
                                800: '#854d0e',
                            },
                            surface: {
                                50: '#f9fafb',
                                100: '#f3f4f6',
                                200: '#e5e7eb',
                                300: '#d1d5db',
                                400: '#9ca3af',
                                500: '#6b7280',
                                600: '#4b5563',
                                700: '#374151',
                                800: '#1f2937',
                                900: '#111827',
                                950: '#030712',
                            }
                        }
                    }
                }
            }
        </script>
    @endif
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-surface-50 text-surface-800 antialiased" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content Wrapper -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <!-- Topbar -->
            <x-topbar />

            <!-- Main Content Area -->
            <main class="w-full grow p-6">
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t border-surface-200 py-4 px-6 text-sm text-surface-500 text-center mt-auto">
                &copy; {{ date('Y') }} Sistem Informasi Akademik Madrasah (SIAM). All rights reserved.
            </footer>
        </div>
    </div>
</body>
</html>
