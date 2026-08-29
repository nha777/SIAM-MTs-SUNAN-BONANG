@extends('layouts.app')
@section('title', 'Detail Role')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Detail Role: {{ $role->name }}</h1>
        <p class="text-sm text-surface-500 mt-1">Informasi lengkap peran dan izin akses yang dimilikinya.</p>
    </div>
    
    <div class="flex gap-2">
        <a href="{{ route('roles.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
        @if($role->name !== 'Super Admin')
            @can('role.update')
            <a href="{{ route('roles.edit', $role->id) }}" class="inline-flex items-center justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                Edit Role
            </a>
            @endcan
        @endif
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-base font-semibold leading-6 text-surface-900">Informasi Role</h3>
            <p class="mt-1 max-w-2xl text-sm text-surface-500">Detail peran pengguna.</p>
        </div>
        @if($role->name === 'Super Admin')
            <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">Role Sistem - Tidak Dapat Diubah</span>
        @endif
    </div>
    <div class="border-t border-surface-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-surface-200">
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Nama Role</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $role->name }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Total Pengguna</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $role->users()->count() }} Pengguna</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Izin Akses (Permissions)</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">
                    @if($role->name === 'Super Admin')
                        <span class="text-surface-700 italic">Memiliki semua akses ke dalam sistem.</span>
                    @else
                        @if($role->permissions->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                            @foreach($role->permissions->groupBy(function($data) { return explode('.', $data->name)[0]; }) as $group => $perms)
                                <div class="mb-2">
                                    <strong class="block text-xs uppercase text-surface-500 mb-1">{{ str_replace('_', ' ', $group) }}</strong>
                                    <div class="flex flex-wrap gap-1">
                                    @foreach($perms as $perm)
                                        <span class="inline-flex items-center rounded-md bg-surface-100 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">{{ explode('.', $perm->name)[1] ?? $perm->name }}</span>
                                    @endforeach
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @else
                            <span class="text-surface-400 italic">Tidak ada akses terdaftar</span>
                        @endif
                    @endif
                </dd>
            </div>
        </dl>
    </div>
</div>
@endsection
