@extends('layouts.app')
@section('title', 'Manajemen Role')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Manajemen Role</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola peran (role) dan izin akses (permissions) dalam sistem.</p>
    </div>
    
    @can('role.create')
    <a href="{{ route('roles.create') }}" class="inline-flex items-center justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
        Tambah Role
    </a>
    @endcan
</div>

<x-alert />

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900 sm:pl-6">Nama Role</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Total Pengguna</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Total Akses</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Aksi</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($roles as $role)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900 sm:pl-6">
                        {{ $role->name }}
                        @if($role->name === 'Super Admin')
                            <span class="ml-2 inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">Sistem</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        {{ $role->users_count }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        {{ $role->permissions_count }}
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex items-center justify-end gap-2" x-data>
                            @can('role.view')
                            <a href="{{ route('roles.show', $role->id) }}" class="text-surface-600 hover:text-surface-900">Lihat</a>
                            @endcan
                            
                            @if($role->name !== 'Super Admin')
                                @can('role.update')
                                <a href="{{ route('roles.edit', $role->id) }}" class="text-primary-600 hover:text-primary-900">Edit</a>
                                @endcan
                                
                                @can('role.delete')
                                <button type="button" 
                                     @click="$dispatch('open-confirm-modal', { actionUrl: '{{ route('roles.destroy', $role->id) }}', title: 'Hapus Role', message: 'Apakah Anda yakin ingin menghapus role {{ $role->name }}? Pastikan role ini tidak sedang digunakan.' })"
                                    class="text-danger-600 hover:text-danger-900">
                                    Hapus
                                </button>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<x-confirm-modal />

@endsection
