@extends('layouts.app')

@section('title', 'Detail Tahun Ajaran')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Detail Tahun Ajaran</h1>
        <p class="text-sm text-surface-500 mt-1">Informasi lengkap tahun ajaran {{ $academicYear->name }}.</p>
    </div>
    
    <div class="flex gap-2">
        <a href="{{ route('academic-years.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
        @can('academic_year.update')
        <a href="{{ route('academic-years.edit', $academicYear->id) }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Edit
        </a>
        @endcan
    </div>
</div>

<x-alert />

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-base font-semibold leading-6 text-surface-900">Informasi Dasar</h3>
        <p class="mt-1 max-w-2xl text-sm text-surface-500">Rincian status dan informasi tahun ajaran.</p>
    </div>
    <div class="border-t border-surface-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200">
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Nama Tahun Ajaran</dt>
                <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $academicYear->name }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Status</dt>
                <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">
                    @if($academicYear->is_active)
                        <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">Aktif</span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-surface-50 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">Non-Aktif</span>
                    @endif
                </dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Dibuat Pada</dt>
                <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $academicYear->created_at->format('d F Y, H:i') }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Terakhir Diperbarui</dt>
                <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $academicYear->updated_at->format('d F Y, H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
