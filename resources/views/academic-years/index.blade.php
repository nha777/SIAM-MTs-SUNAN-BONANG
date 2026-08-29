@extends('layouts.app')

@section('title', 'Manajemen Tahun Ajaran')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Manajemen Tahun Ajaran</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola data tahun ajaran aktif dan riwayat tahun ajaran.</p>
    </div>
    
    @can('academic_year.create')
    <a href="{{ route('academic-years.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
        </svg>
        Tambah Tahun Ajaran
    </a>
    @endcan
</div>

<x-alert />

<!-- Filters -->
<div class="bg-white p-4 shadow sm:rounded-lg mb-6">
    <form action="{{ route('academic-years.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="w-full sm:w-64">
            <label for="status" class="block text-sm font-medium leading-6 text-surface-900">Status Aktif/Dihapus</label>
            <select id="status" name="status" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                <option value="active" {{ request('status') === 'active' || !request('status') ? 'selected' : '' }}>Aktif & Non-Aktif (Valid)</option>
                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Data (Termasuk Dihapus)</option>
                <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Hanya Data Dihapus</option>
            </select>
        </div>
        <div>
            <button type="submit" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900 sm:pl-6">Nama Tahun Ajaran</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Dibuat Pada</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Aksi</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($academicYears as $year)
                <tr class="{{ $year->trashed() ? 'bg-danger-50' : '' }}">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900 sm:pl-6">
                        {{ $year->name }}
                        @if($year->trashed())
                            <span class="ml-2 inline-flex items-center rounded-md bg-danger-100 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">Dihapus</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        @if($year->is_active)
                            <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">Aktif</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-surface-50 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        {{ $year->created_at->format('d M Y') }}
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex items-center justify-end gap-2" x-data>
                            @if(!$year->trashed())
                                @can('academic_year.view')
                                <a href="{{ route('academic-years.show', $year->id) }}" class="text-surface-600 hover:text-surface-900">Lihat</a>
                                @endcan
                                
                                @can('academic_year.update')
                                <a href="{{ route('academic-years.edit', $year->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endcan

                                @if(!$year->is_active)
                                    @can('academic_year.activate')
                                    <button type="button" 
                                        @click="$dispatch('open-activate-modal', { actionUrl: '{{ route('academic-years.activate', $year->id) }}', title: 'Aktifkan Tahun Ajaran', message: 'Apakah Anda yakin ingin mengaktifkan tahun ajaran {{ $year->name }}? Tahun ajaran yang aktif saat ini akan dinonaktifkan otomatis.' })"
                                        class="text-primary-600 hover:text-primary-900">
                                        Aktifkan
                                    </button>
                                    @endcan
                                @endif
                                
                                @if(!$year->is_active)
                                    @can('academic_year.delete')
                                    <button type="button" 
                                        @click="$dispatch('open-confirm-modal', { actionUrl: '{{ route('academic-years.destroy', $year->id) }}', title: 'Hapus Tahun Ajaran', message: 'Apakah Anda yakin ingin menghapus tahun ajaran {{ $year->name }}? Data tidak akan benar-benar terhapus (soft delete).' })"
                                        class="text-danger-600 hover:text-danger-900">
                                        Hapus
                                    </button>
                                    @endcan
                                @endif
                            @else
                                @can('academic_year.restore')
                                <button type="button" 
                                    @click="$dispatch('open-restore-modal', { actionUrl: '{{ route('academic-years.restore', $year->id) }}', title: 'Pulihkan Tahun Ajaran', message: 'Apakah Anda yakin ingin memulihkan tahun ajaran {{ $year->name }}?' })"
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
                    <td colspan="4" class="px-3 py-8 text-center text-sm text-surface-500">
                        Tidak ada data tahun ajaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($academicYears->hasPages())
    <div class="border-t border-surface-200 px-4 py-3 sm:px-6">
        {{ $academicYears->links() }}
    </div>
    @endif
</div>

<x-confirm-modal />
<x-restore-modal />
<x-activate-modal />
@endsection
