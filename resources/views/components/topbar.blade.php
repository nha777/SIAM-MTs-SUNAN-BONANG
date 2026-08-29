<header class="sticky top-0 z-30 flex h-16 w-full items-center justify-between bg-white px-6 shadow-sm border-b border-surface-200">
    <!-- Left: Hamburger button for mobile -->
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="text-surface-500 hover:text-surface-700 focus:outline-none">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h2 class="text-xl font-semibold text-surface-900">
            @yield('header_title', 'Dashboard')
        </h2>
    </div>

    <!-- Right: User Info & Actions -->
    <div class="flex items-center gap-4">
        @if(auth()->check())
        <!-- Role Badge -->
        <span class="hidden sm:inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-700/10">
            {{ auth()->user()->roles->first()->name ?? 'User' }}
        </span>

        <!-- User Dropdown -->
        <div x-data="{ dropdownOpen: false }" class="relative">
            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 focus:outline-none">
                <div class="h-8 w-8 rounded-full bg-surface-200 flex items-center justify-center text-surface-700 font-semibold">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <span class="hidden md:block text-sm font-medium text-surface-700">
                    {{ auth()->user()->name ?? 'User' }}
                </span>
                <svg class="h-4 w-4 text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition class="absolute right-0 mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5">
                <div class="px-4 py-2 text-xs text-surface-500 border-b border-surface-100">
                    Masuk sebagai:<br>
                    <span class="font-medium text-surface-900">{{ auth()->user()->email ?? '' }}</span>
                </div>
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">Profil Saya</a>
                
                <form method="POST" action="{{ route('logout') ?? '#' }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-danger-600 hover:bg-danger-50">
                        Logout
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="text-sm font-medium text-surface-500">Guest</div>
        @endif
    </div>
</header>
