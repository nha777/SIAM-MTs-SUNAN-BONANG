@extends('layouts.app')
@section('title', 'Manajemen Izin Akses')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-surface-900">Manajemen Izin Akses (Permissions)</h1>
    <p class="text-sm text-surface-500 mt-1">Lihat daftar izin akses yang tersedia dalam sistem.</p>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($permissions as $group => $perms)
        <div class="bg-surface-50 rounded-lg p-4 border border-surface-200">
            <h3 class="font-medium text-surface-900 mb-3 capitalize text-lg border-b border-surface-200 pb-2">{{ str_replace('_', ' ', $group) }}</h3>
            <ul class="space-y-2">
                @foreach($perms as $permission)
                <li class="flex justify-between items-center text-sm">
                    <span class="text-surface-700">{{ explode('.', $permission->name)[1] ?? $permission->name }}</span>
                    <a href="{{ route('permissions.show', $permission->id) }}" class="text-primary-600 hover:text-primary-900 text-xs font-medium bg-primary-50 px-2 py-1 rounded">Lihat</a>
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
</div>
@endsection
