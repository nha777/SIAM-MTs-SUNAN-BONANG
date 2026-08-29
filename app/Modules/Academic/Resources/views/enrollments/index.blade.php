@extends('layouts.app')
@section('title', 'Enrollment (Rombel)')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Rombongan Belajar</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola data siswa di dalam kelas.</p>
    </div>
    
    @can('academic.create')
    <div>
        <a href="{{ route('enrollments.create') }}" class="inline-flex items-center justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Rombel
        </a>
    </div>
    @endcan
</div>

<x-alert />

<div class="bg-white p-4 shadow sm:rounded-lg mb-6">
    <form action="{{ route('enrollments.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="w-full sm:w-48">
            <label for="academic_year_id" class="block text-sm font-medium leading-6 text-surface-900">Tahun Ajaran</label>
            <select name="academic_year_id" id="academic_year_id" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="">Semua</option>
                @foreach($academicYears as $ay)
                    <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-48">
            <label for="semester_id" class="block text-sm font-medium leading-6 text-surface-900">Semester</label>
            <select name="semester_id" id="semester_id" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="">Semua</option>
                @foreach($semesters as $sm)
                    <option value="{{ $sm->id }}" {{ request('semester_id') == $sm->id ? 'selected' : '' }}>{{ $sm->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-48">
            <label for="academic_class_id" class="block text-sm font-medium leading-6 text-surface-900">Kelas</label>
            <select name="academic_class_id" id="academic_class_id" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="">Semua</option>
                @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" {{ request('academic_class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->level }} - {{ $cls->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">Filter</button>
            @if(request()->hasAny(['academic_year_id', 'semester_id', 'academic_class_id']))
                <a href="{{ route('enrollments.index') }}" class="ml-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-danger-600 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900 sm:pl-6">Siswa</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Kelas</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Tahun/Semester</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Tgl Masuk</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status</th>
                    <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($enrollments as $enrollment)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900 sm:pl-6">
                        {{ $enrollment->student->name ?? '-' }}<br>
                        <span class="text-xs text-surface-500">{{ $enrollment->student->nisn ?? '-' }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">{{ $enrollment->academicClass->level ?? '-' }} - {{ $enrollment->academicClass->name ?? '-' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">
                        {{ $enrollment->academicYear->name ?? '-' }}<br>
                        <span class="text-xs text-surface-500">{{ $enrollment->semester->name ?? '-' }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $enrollment->enrollment_date->format('d M Y') }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">{{ $enrollment->status }}</span>
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex justify-end gap-2">
                            @can('academic.update')
                            <a href="{{ route('enrollments.edit', $enrollment->id) }}" class="text-warning-600 hover:text-warning-900">Edit</a>
                            @endcan
                            @can('academic.delete')
                            <form action="{{ route('enrollments.destroy', $enrollment->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
    @if($enrollments->hasPages())
    <div class="border-t border-surface-200 px-4 py-3 sm:px-6">
        {{ $enrollments->links() }}
    </div>
    @endif
</div>
@endsection
