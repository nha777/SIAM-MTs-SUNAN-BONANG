@extends('layouts.app')
@section('title', 'Manajemen Kelas')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Manajemen Kelas</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola data kelas untuk setiap tahun ajaran.</p>
    </div>
    
    @can('class.create')
    <a href="{{ route('classes.create') }}" class="inline-flex items-center justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
        </svg>
        Tambah Kelas
    </a>
    @endcan
</div>

<x-alert />

<!-- Filters -->
<div class="bg-white p-4 shadow sm:rounded-lg mb-6">
    <form action="{{ route('classes.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="w-full sm:w-64">
            <label for="search" class="block text-sm font-medium leading-6 text-surface-900">Cari Nama Kelas</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cth: A, B, IPA" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 placeholder:text-surface-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
        </div>
        
        <div class="w-full sm:w-48">
            <label for="grade" class="block text-sm font-medium leading-6 text-surface-900">Tingkat</label>
            <select id="grade" name="grade" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="">Semua Tingkat</option>
                <option value="7" {{ request('grade') == '7' ? 'selected' : '' }}>VII (7)</option>
                <option value="8" {{ request('grade') == '8' ? 'selected' : '' }}>VIII (8)</option>
                <option value="9" {{ request('grade') == '9' ? 'selected' : '' }}>IX (9)</option>
            </select>
        </div>

        <div class="w-full sm:w-48">
            <label for="status" class="block text-sm font-medium leading-6 text-surface-900">Status</label>
            <select id="status" name="status" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="active" {{ request('status') === 'active' || !request('status') ? 'selected' : '' }}>Aktif</option>
                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Data</option>
                <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Hanya Dihapus</option>
            </select>
        </div>
        
        <div>
            <button type="submit" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
                Filter
            </button>
            @if(request()->hasAny(['search', 'grade', 'status']) && (request('search') || request('grade') || request('status') != 'active'))
                <a href="{{ route('classes.index') }}" class="ml-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-danger-600 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900 sm:pl-6">Nama Kelas</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Tahun Ajaran</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Kapasitas</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Urutan</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Aksi</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($classes as $class)
                <tr class="{{ $class->trashed() ? 'bg-danger-50' : '' }}">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900 sm:pl-6">
                        {{ $class->full_name }}
                        @if($class->trashed())
                            <span class="ml-2 inline-flex items-center rounded-md bg-danger-100 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">Dihapus</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        {{ $class->academicYear->name ?? '-' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        {{ $class->capacity ?? '-' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        {{ $class->display_order }}
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex items-center justify-end gap-2" x-data>
                            @if(!$class->trashed())
                                @can('class.view')
                                <a href="{{ route('classes.show', $class->id) }}" class="text-surface-600 hover:text-surface-900">Lihat</a>
                                @endcan
                                
                                @can('class.update')
                                <a href="{{ route('classes.edit', $class->id) }}" class="text-primary-600 hover:text-primary-900">Edit</a>
                                @endcan
                                
                                @can('class.delete')
                                <button type="button" 
                                     @click="$dispatch('open-confirm-modal', { actionUrl: '{{ route('classes.destroy', $class->id) }}', title: 'Hapus Kelas', message: 'Apakah Anda yakin ingin menghapus kelas {{ $class->full_name }}? Data tidak akan benar-benar terhapus (soft delete).' })"
                                    class="text-danger-600 hover:text-danger-900">
                                    Hapus
                                </button>
                                @endcan
                            @else
                                @can('class.restore')
                                <button type="button" 
                                     @click="$dispatch('open-restore-modal', { actionUrl: '{{ route('classes.restore', $class->id) }}', title: 'Pulihkan Kelas', message: 'Apakah Anda yakin ingin memulihkan kelas {{ $class->full_name }}?' })"
                                    class="text-emerald-600 hover:text-emerald-900">
                                    Pulihkan
                                </button>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-3 py-8 text-center text-sm text-surface-500">
                        Tidak ada data kelas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($classes->hasPages())
    <div class="border-t border-surface-200 px-4 py-3 sm:px-6">
        {{ $classes->links() }}
    </div>
    @endif
</div>

<x-confirm-modal />
<x-restore-modal />

@endsection
