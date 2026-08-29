@extends('layouts.app')
@section('title', 'Detail Kelas')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center gap-4">
        <a href="{{ route('classes.index') }}" class="text-surface-500 hover:text-surface-900">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold text-surface-900">Detail Kelas</h1>
            <p class="text-sm text-surface-500 mt-1">Informasi lengkap kelas {{ $class->full_name }}.</p>
        </div>
    </div>
    
    <div class="flex gap-2">
        @can('class.update')
        <a href="{{ route('classes.edit', $class->id) }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            <x-heroicon-o-pencil class="-ml-0.5 mr-1.5 h-4 w-4 text-surface-400" />
            Edit
        </a>
        @endcan
    </div>
</div>

<x-alert />

<div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <h3 class="text-base font-semibold leading-6 text-surface-900">Informasi Kelas</h3>
        @if($class->trashed())
            <span class="inline-flex items-center rounded-md bg-danger-100 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">Dihapus (Soft Delete)</span>
        @endif
    </div>
    <div class="border-t border-surface-200">
        <dl class="divide-y divide-surface-200">
            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Nama Lengkap</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-900 sm:col-span-2 sm:mt-0">{{ $class->full_name }}</dd>
            </div>
            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Tingkat</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-900 sm:col-span-2 sm:mt-0">{{ App\Modules\Academic\Models\AcademicClass::getRomanGrade($class->grade) }} ({{ $class->grade }})</dd>
            </div>
            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Nama Kelas (Tanpa Tingkat)</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-900 sm:col-span-2 sm:mt-0">{{ $class->name }}</dd>
            </div>
            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Tahun Ajaran</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-900 sm:col-span-2 sm:mt-0">{{ $class->academicYear->name ?? '-' }} {!! isset($class->academicYear) && $class->academicYear->is_active ? '<span class="text-xs text-primary-600">(Aktif)</span>' : '' !!}</dd>
            </div>
            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Kapasitas</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-900 sm:col-span-2 sm:mt-0">{{ $class->capacity }} Siswa</dd>
            </div>
            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Jumlah Siswa Saat Ini</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-900 sm:col-span-2 sm:mt-0">{{ $class->students->count() }} Siswa</dd>
            </div>
            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Urutan Tampilan</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-900 sm:col-span-2 sm:mt-0">{{ $class->display_order }}</dd>
            </div>
        </dl>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <h3 class="text-base font-semibold leading-6 text-surface-900">Daftar Siswa</h3>
    </div>
    <div class="border-t border-surface-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-surface-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900 sm:pl-6">NIS / NISN</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Nama Siswa</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($class->students as $student)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-surface-500 sm:pl-6">
                            {{ $student->nis ?? '-' }} <br>
                            <span class="text-xs">{{ $student->nisn ?? '-' }}</span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-surface-900">
                            {{ $student->name }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                            <span class="inline-flex items-center rounded-md bg-surface-50 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">
                                {{ ucfirst($student->status ?? 'aktif') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-3 py-8 text-center text-sm text-surface-500">
                            Tidak ada siswa di kelas ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
