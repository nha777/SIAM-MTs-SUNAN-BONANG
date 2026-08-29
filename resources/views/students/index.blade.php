@extends('layouts.app')

@section('title', 'Manajemen Siswa - SIAM')
@section('header_title', 'Data Siswa')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Header & Actions -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h3 class="text-base font-semibold leading-6 text-surface-900">Daftar Siswa</h3>
            <p class="mt-2 text-sm text-surface-700">Kelola informasi data siswa madrasah, termasuk aksi lihat detail, ubah, atau hapus.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            @can('create', App\Modules\Student\Models\Student::class)
            <a href="{{ route('students.create') }}" class="block rounded-md bg-primary-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                Tambah Siswa
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 border border-surface-200 rounded-lg shadow-sm">
        <form method="GET" action="{{ route('students.index') }}" class="flex flex-col sm:flex-row gap-4 items-end sm:items-center">
            <div class="w-full sm:w-1/3">
                <label for="search" class="block text-sm font-medium text-surface-700 mb-1">Pencarian</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full rounded-md border-surface-300 pl-10 focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border" placeholder="Cari nama atau NISN...">
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
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900 sm:pl-6">NISN</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Nama Siswa</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Gender</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($students as $student)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900 sm:pl-6">{{ $student->nisn }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                            {{ $student->name }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                            {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                            @if($student->trashed())
                                <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">Tidak Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">Aktif</span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <div class="flex items-center justify-end gap-2">
                                @can('view', $student)
                                <a href="{{ route('students.show', $student->id) }}" class="text-primary-600 hover:text-primary-900 p-1" title="Detail">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                @endcan

                                @if(!$student->trashed())
                                    @can('update', $student)
                                    <a href="{{ route('students.edit', $student->id) }}" class="text-yellow-600 hover:text-yellow-900 p-1" title="Ubah">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    @endcan
                                    
                                    @can('delete', $student)
                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', { 
                                                actionUrl: '{{ route('students.destroy', $student->id) }}', 
                                                title: 'Hapus Siswa', 
                                                message: 'Anda yakin ingin menghapus data siswa {{ addslashes($student->name) }}? Data masih dapat dipulihkan nanti.' 
                                            })" 
                                            class="text-danger-600 hover:text-danger-900 p-1" title="Hapus">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    @endcan
                                @else
                                    @can('restore', $student)
                                    <form action="{{ route('students.restore', $student->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-primary-600 hover:text-primary-900 p-1" title="Pulihkan">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        </button>
                                    </form>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-center text-sm text-surface-500">
                            Tidak ada data siswa yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
        <div class="bg-white px-4 py-3 border-t border-surface-200 sm:px-6">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>

<x-confirm-modal />
@endsection
