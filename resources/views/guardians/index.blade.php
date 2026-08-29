@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-surface-900">Data Wali Murid</h1>
        <p class="mt-1 text-sm text-surface-500">Kelola informasi wali murid, kontak, dan daftar siswa yang diwaliinya.</p>
    </div>
    <div class="mt-4 sm:ml-4 sm:mt-0">
        @can('guardian.create')
        <a href="{{ route('guardians.create') }}" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
            </svg>
            Tambah Wali Murid
        </a>
        @endcan
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <x-alert />

    <!-- Filters -->
    <div class="bg-surface-50 px-4 py-3 border border-surface-200 rounded-lg sm:px-6">
        <form action="{{ route('guardians.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full sm:w-1/3">
                <label for="search" class="sr-only">Cari</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full rounded-md border-surface-300 pl-10 focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border" placeholder="Cari nama atau no. telepon...">
                </div>
            </div>
            
            <div class="w-full sm:w-1/4">
                <label for="status" class="block text-sm font-medium text-surface-700 mb-1">Status</label>
                <select id="status" name="status" class="block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border bg-white">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('status') === 'active' || !request('status') ? 'selected' : '' }}>Aktif</option>
                    <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Dihapus / Tidak Aktif</option>
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto rounded-md bg-white px-4 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-surface-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-surface-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900 sm:pl-6">Nama Wali</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Hubungan</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">No. Telepon</th>
                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-surface-900">Jumlah Anak</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($guardians as $guardian)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900 sm:pl-6">{{ $guardian->guardian_name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                            {{ ucfirst(str_replace('_', ' ', $guardian->guardian_relation)) }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                            {{ $guardian->phone_number }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500 text-center">
                            {{ $guardian->students_count }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                            @if($guardian->trashed())
                                <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">Tidak Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">Aktif</span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <div class="flex items-center justify-end gap-2">
                                @can('view', $guardian)
                                <a href="{{ route('guardians.show', $guardian->id) }}" class="text-primary-600 hover:text-primary-900 p-1" title="Detail">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                @endcan

                                @if(!$guardian->trashed())
                                    @can('update', $guardian)
                                    <a href="{{ route('guardians.edit', $guardian->id) }}" class="text-yellow-600 hover:text-yellow-900 p-1" title="Ubah">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    @endcan
                                    
                                    @can('delete', $guardian)
                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', {
                                                actionUrl: '{{ route('guardians.destroy', $guardian->id) }}',
                                                title: 'Hapus Wali Murid',
                                                message: 'Anda yakin ingin menghapus data wali murid {{ addslashes($guardian->guardian_name) }}? Semua siswa yang terkait dengannya akan dinonaktifkan (status menjadi keluar).'
                                            })"
                                            class="text-danger-600 hover:text-danger-900 p-1" title="Hapus">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    @endcan
                                @else
                                    @can('restore', $guardian)
                                    <button type="button" 
                                            @click="$dispatch('open-restore-modal', {
                                                actionUrl: '{{ route('guardians.restore', $guardian->id) }}',
                                                title: 'Pulihkan Wali Murid',
                                                message: 'Anda yakin ingin memulihkan data wali murid {{ addslashes($guardian->guardian_name) }}?'
                                            })"
                                            class="text-primary-600 hover:text-primary-900 p-1" title="Pulihkan">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    </button>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-sm text-surface-500">
                            Tidak ada data wali murid yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($guardians->hasPages())
        <div class="bg-white px-4 py-3 border-t border-surface-200 sm:px-6">
            {{ $guardians->links() }}
        </div>
        @endif
    </div>
</div>

<x-confirm-modal />
<x-restore-modal />
@endsection
