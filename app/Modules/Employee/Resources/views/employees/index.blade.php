@extends('layouts.app')
@section('title', 'Data Pegawai & Guru')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Pegawai & Guru</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola data guru dan tenaga kependidikan.</p>
    </div>
    
    @can('employee.create')
    <div>
        <a href="{{ route('employees.create') }}" class="inline-flex items-center justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Pegawai
        </a>
    </div>
    @endcan
</div>

<x-alert />

<div class="bg-white p-4 shadow sm:rounded-lg mb-6">
    <form action="{{ route('employees.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="w-full sm:w-64">
            <label for="search" class="block text-sm font-medium leading-6 text-surface-900">Cari Pegawai</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nama, NIP..." class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
        </div>
        <div class="w-full sm:w-48">
            <label for="position" class="block text-sm font-medium leading-6 text-surface-900">Posisi</label>
            <select name="position" id="position" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="">Semua Posisi</option>
                <option value="Guru" {{ request('position') == 'Guru' ? 'selected' : '' }}>Guru</option>
                <option value="Staff" {{ request('position') == 'Staff' ? 'selected' : '' }}>Staff / TU</option>
            </select>
        </div>
        <div>
            <button type="submit" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">Filter</button>
            @if(request()->hasAny(['search', 'position']))
                <a href="{{ route('employees.index') }}" class="ml-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-danger-600 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900 sm:pl-6">Nama</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">NIP/NUPTK</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Posisi</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status</th>
                    <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($employees as $employee)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900 sm:pl-6">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-surface-200 flex items-center justify-center text-surface-600 font-bold">
                                    {{ substr($employee->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-surface-900">{{ $employee->name }}</div>
                                <div class="text-surface-500">{{ $employee->email ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        {{ $employee->nip ?? '-' }} <br>
                        <span class="text-xs text-surface-400">{{ $employee->nuptk ?? '-' }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        {{ $employee->position }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        @if($employee->is_active)
                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Aktif</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-surface-50 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex justify-end gap-2">
                            @can('employee.view')
                            <a href="{{ route('employees.show', $employee->id) }}" class="text-primary-600 hover:text-primary-900">Detail</a>
                            @endcan
                            @can('employee.update')
                            <a href="{{ route('employees.edit', $employee->id) }}" class="text-warning-600 hover:text-warning-900">Edit</a>
                            @endcan
                            @can('employee.delete')
                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-danger-600 hover:text-danger-900">Hapus</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-3 py-8 text-center text-sm text-surface-500">Tidak ada data pegawai yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($employees->hasPages())
    <div class="border-t border-surface-200 px-4 py-3 sm:px-6">
        {{ $employees->links() }}
    </div>
    @endif
</div>
@endsection
