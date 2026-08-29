@extends('layouts.app')
@section('title', 'Detail Izin Akses')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Detail Izin Akses: {{ $permission->name }}</h1>
        <p class="text-sm text-surface-500 mt-1">Informasi peran yang memiliki izin ini.</p>
    </div>
    
    <div>
        <a href="{{ route('permissions.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-base font-semibold leading-6 text-surface-900">Daftar Role</h3>
        <p class="mt-1 max-w-2xl text-sm text-surface-500">Peran yang memiliki akses ini.</p>
    </div>
    <div class="border-t border-surface-200">
        <ul role="list" class="divide-y divide-surface-200">
            @forelse($permission->roles as $role)
            <li class="flex items-center justify-between px-4 py-4 sm:px-6">
                <div class="flex items-center gap-x-3">
                    <p class="text-sm font-semibold leading-6 text-surface-900">{{ $role->name }}</p>
                    @if($role->name === 'Super Admin')
                        <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">Sistem</span>
                    @endif
                </div>
                <div class="flex flex-none items-center gap-x-4">
                    <a href="{{ route('roles.show', $role->id) }}" class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50 sm:block">Lihat Role<span class="sr-only">, {{ $role->name }}</span></a>
                </div>
            </li>
            @empty
            <li class="px-4 py-8 text-center text-sm text-surface-500 sm:px-6">
                Tidak ada peran yang memiliki izin akses ini selain Super Admin.
            </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
