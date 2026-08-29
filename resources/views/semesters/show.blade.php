@extends('layouts.app')

@section('title', 'Detail Semester')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Detail Semester</h1>
        <p class="text-sm text-surface-500 mt-1">Informasi lengkap semester {{ ucfirst($semester->semester) }} tahun {{ $semester->academicYear->name ?? '' }}.</p>
    </div>
    
    <div class="flex gap-2">
        <a href="{{ route('semesters.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
        @can('semester.update')
        <a href="{{ route('semesters.edit', $semester->id) }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Edit
        </a>
        @endcan
    </div>
</div>

<x-alert />

<div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-base font-semibold leading-6 text-surface-900">Informasi Dasar</h3>
        <p class="mt-1 max-w-2xl text-sm text-surface-500">Rincian status dan informasi semester.</p>
    </div>
    <div class="border-t border-surface-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200">
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Tahun Ajaran</dt>
                <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $semester->academicYear->name ?? '-' }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Semester</dt>
                <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ ucfirst($semester->semester) }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Status</dt>
                <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">
                    @if($semester->is_active)
                        <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">Aktif</span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-surface-50 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">Non-Aktif</span>
                    @endif
                </dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Dibuat Pada</dt>
                <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $semester->created_at->format('d F Y, H:i') }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-surface-500">Terakhir Diperbarui</dt>
                <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $semester->updated_at->format('d F Y, H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>

@if(method_exists($semester, 'auditLogs'))
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-base font-semibold leading-6 text-surface-900">Riwayat Audit (Audit Trail)</h3>
        <p class="mt-1 max-w-2xl text-sm text-surface-500">Log aktivitas terbaru pada data ini.</p>
    </div>
    <div class="border-t border-surface-200">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($semester->auditLogs()->with('user')->latest()->take(5)->get() as $log)
            <li class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-indigo-600 truncate">
                        {{ $log->event_name }}
                    </p>
                    <div class="ml-2 flex-shrink-0 flex">
                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-surface-100 text-surface-800">
                            {{ $log->created_at->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>
                <div class="mt-2 sm:flex sm:justify-between">
                    <div class="sm:flex flex-col">
                        <p class="flex items-center text-sm text-surface-500">
                            Oleh: {{ $log->user->name ?? 'System' }} | Severity: {{ ucfirst($log->severity) }}
                        </p>
                        @if($log->request_id)
                        <p class="flex items-center text-xs text-surface-400 mt-1">
                            Request ID: {{ $log->request_id }}
                        </p>
                        @endif
                        @if($log->metadata)
                        <div class="mt-2 text-xs text-surface-500 bg-surface-50 p-2 rounded">
                            <pre class="overflow-x-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                    </div>
                </div>
            </li>
            @empty
            <li class="px-4 py-4 sm:px-6 text-sm text-surface-500 text-center">
                Belum ada log aktivitas.
            </li>
            @endforelse
        </ul>
    </div>
</div>
@endif
@endsection
