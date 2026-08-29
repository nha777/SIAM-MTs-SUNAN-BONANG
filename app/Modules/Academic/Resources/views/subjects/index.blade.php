@extends('layouts.app')
@section('title', 'Mata Pelajaran')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Mata Pelajaran</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola data mata pelajaran.</p>
    </div>
    
    @can('subject.create')
    <div>
        <a href="{{ route('subjects.create') }}" class="inline-flex items-center justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Mapel
        </a>
    </div>
    @endcan
</div>

<x-alert />

<div class="bg-white p-4 shadow sm:rounded-lg mb-6">
    <form action="{{ route('subjects.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="w-full sm:w-64">
            <label for="search" class="block text-sm font-medium leading-6 text-surface-900">Cari Mapel</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Kode, Nama..." class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
        </div>
        <div class="w-full sm:w-48">
            <label for="type" class="block text-sm font-medium leading-6 text-surface-900">Jenis</label>
            <select name="type" id="type" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="">Semua Jenis</option>
                <option value="Umum" {{ request('type') == 'Umum' ? 'selected' : '' }}>Umum</option>
                <option value="Peminatan" {{ request('type') == 'Peminatan' ? 'selected' : '' }}>Peminatan</option>
                <option value="Muatan Lokal" {{ request('type') == 'Muatan Lokal' ? 'selected' : '' }}>Muatan Lokal</option>
            </select>
        </div>
        <div>
            <button type="submit" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">Filter</button>
            @if(request()->hasAny(['search', 'type']))
                <a href="{{ route('subjects.index') }}" class="ml-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-danger-600 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900 sm:pl-6">Kode</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Nama Mapel</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Jenis</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">SKS</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status</th>
                    <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($subjects as $subject)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900 sm:pl-6">{{ $subject->code }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">{{ $subject->name }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $subject->type }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $subject->credits }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        @if($subject->is_active)
                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Aktif</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-surface-50 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex justify-end gap-2">
                            @can('subject.update')
                            <a href="{{ route('subjects.edit', $subject->id) }}" class="text-warning-600 hover:text-warning-900">Edit</a>
                            @endcan
                            @can('subject.delete')
                            <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
                    <td colspan="6" class="px-3 py-8 text-center text-sm text-surface-500">Tidak ada data yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subjects->hasPages())
    <div class="border-t border-surface-200 px-4 py-3 sm:px-6">
        {{ $subjects->links() }}
    </div>
    @endif
</div>
@endsection
