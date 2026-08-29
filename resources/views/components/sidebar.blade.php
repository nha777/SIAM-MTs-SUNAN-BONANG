<!-- Backdrop for mobile -->
<div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-surface-900/50 lg:hidden" @click="sidebarOpen = false" x-transition.opacity></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-surface-200 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col">
    <!-- Brand -->
    <div class="flex h-16 items-center justify-center border-b border-surface-200 px-6">
        <a href="/" class="flex items-center gap-2">
            <div class="rounded bg-primary-600 p-1">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="text-xl font-bold text-surface-900 tracking-tight">SIAM</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
        <!-- Dashboard -->
        <x-sidebar-link href="/" :active="request()->is('/')">
            <x-slot name="icon">
                <svg class="h-5 w-5 {{ request()->is('/') ? 'text-primary-700' : 'text-surface-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </x-slot>
            Dashboard
        </x-sidebar-link>

        <!-- Master Data -->
        @if(auth()->check() && (auth()->user()->can('student.view') || auth()->user()->can('guardian.view')))
        <x-sidebar-group title="Master Data" :active="request()->routeIs('students.*') || request()->routeIs('guardians.*')">
            @can('student.view')
            <x-sidebar-link href="{{ route('students.index') }}" :active="request()->routeIs('students.*')">
                Siswa
            </x-sidebar-link>
            @endcan

            @can('guardian.view')
            <x-sidebar-link href="{{ route('guardians.index') }}" :active="request()->routeIs('guardians.*')">
                Wali Murid
            </x-sidebar-link>
            @endcan
        
            @can('academic.view')
            <x-sidebar-link href="{{ route('enrollments.index') }}" :active="request()->routeIs('enrollments.*')">
                Enrollment (Rombel)
            </x-sidebar-link>

            @endcan
</x-sidebar-group>
        @endif

        
        
        <!-- Keuangan -->
        @if(auth()->check() && (auth()->user()->can('finance.view')))
        <x-sidebar-group title="Keuangan" :active="request()->routeIs('financial-dashboard.index') || request()->routeIs('invoices.*') || request()->routeIs('billing-categories.*') || request()->routeIs('payment-verifications.*')">
            <x-sidebar-link href="{{ route('financial-dashboard.index') }}" :active="request()->routeIs('financial-dashboard.index')">
                Dashboard Keuangan
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('billing-categories.index') }}" :active="request()->routeIs('billing-categories.*')">
                Template Tagihan
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('invoices.index') }}" :active="request()->routeIs('invoices.*')">
                Data Tagihan
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('payment-verifications.index') }}" :active="request()->routeIs('payment-verifications.*')">
                Verifikasi Pembayaran
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('financial-reports.index') }}" :active="request()->routeIs('financial-reports.*')">
                Laporan Keuangan
            </x-sidebar-link>
        </x-sidebar-group>
        @endif

        <!-- Portal Orang Tua -->
        @if(auth()->check())
        <x-sidebar-group title="Portal Orang Tua" :active="request()->routeIs('portal.*')">
            <x-sidebar-link href="{{ route('portal.invoices') }}" :active="request()->routeIs('portal.*')">
                Tagihan Anak Saya
            </x-sidebar-link>
        </x-sidebar-group>
        @endif

        <!-- Kepegawaian -->
        @if(auth()->check() && (auth()->user()->can('employee.view')))
        <x-sidebar-group title="Kepegawaian" :active="request()->routeIs('employees.*')">
            @can('employee.view')
            <x-sidebar-link href="{{ route('employees.index') }}" :active="request()->routeIs('employees.*')">
                Pegawai & Guru
            </x-sidebar-link>
            @endcan
        </x-sidebar-group>
        @endif

        <!-- Akademik -->
        @if(auth()->check() && (auth()->user()->can('academic_year.view') || auth()->user()->can('semester.view') || auth()->user()->can('class.view')))
        <x-sidebar-group title="Akademik" :active="request()->routeIs('academic-years.*') || request()->routeIs('semesters.*') || request()->routeIs('classes.*') || request()->routeIs('subjects.*')">
            @can('academic_year.view')
            <x-sidebar-link href="{{ route('academic-years.index') }}" :active="request()->routeIs('academic-years.*')">
                Tahun Ajaran
            </x-sidebar-link>
            @endcan

            @can('semester.view')
            <x-sidebar-link href="{{ route('semesters.index') }}" :active="request()->routeIs('semesters.*')">
                Semester
            </x-sidebar-link>
            @endcan

            @can('class.view')
            <x-sidebar-link href="{{ route('classes.index') }}" :active="request()->routeIs('classes.*')">
                Kelas
            </x-sidebar-link>
            @endcan
        

    </x-sidebar-group>
        @endif

        <!-- Foundation / Pengguna -->
        @if(auth()->check() && (auth()->user()->can('user.view') || auth()->user()->can('role.view') || auth()->user()->can('permission.view')))
        <x-sidebar-group title="Akses & Pengguna" :active="request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*')">
            @can('user.view')
            <x-sidebar-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')">
                Pengguna
            </x-sidebar-link>
            @endcan
            @can('role.view')
            <x-sidebar-link href="{{ route('roles.index') }}" :active="request()->routeIs('roles.*')">
                Role
            </x-sidebar-link>
            @endcan
            @can('permission.view')
            <x-sidebar-link href="{{ route('permissions.index') }}" :active="request()->routeIs('permissions.*')">
                Izin Akses
            </x-sidebar-link>
            @endcan
        </x-sidebar-group>
        @endif

        <!-- Administrasi -->
        @if(auth()->check() && (auth()->user()->hasRole('Super Admin') || auth()->user()->can('activity_log.view')))
        <x-sidebar-group title="Administrasi" :active="request()->routeIs('activity-logs.*')">
            @can('activity_log.view')
            <x-sidebar-link href="{{ route('activity-logs.index') }}" :active="request()->routeIs('activity-logs.*')">
                Log Aktivitas
            </x-sidebar-link>
            @endcan
            @if(auth()->user()->hasRole('Super Admin'))
            <x-sidebar-link href="#">
                Pengaturan
            </x-sidebar-link>
            @endif
        </x-sidebar-group>
        @endif

        <!-- Dev Only (Visible in Local/Debug) -->
        @if(app()->environment('local') || config('app.debug') === true)
        <x-sidebar-group title="Development" :active="request()->routeIs('dev.components')">
            <x-sidebar-link href="{{ route('dev.components') }}" :active="request()->routeIs('dev.components')">
                UI Components
            </x-sidebar-link>
        </x-sidebar-group>
        @endif
    </nav>
</aside>
