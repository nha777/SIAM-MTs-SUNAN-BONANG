@extends('layouts.app')
@section('title', 'Detail Log Aktivitas')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Detail Log Aktivitas</h1>
        <p class="text-sm text-surface-500 mt-1">Catatan rinci tentang aktivitas yang dilakukan.</p>
    </div>
    
    <div>
        <a href="{{ route('activity-logs.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-base font-semibold leading-6 text-surface-900">Informasi Umum</h3>
            <p class="mt-1 max-w-2xl text-sm text-surface-500">Detail metadata aktivitas.</p>
        </div>
        @php
            $badgeClass = match($log->event) {
                'created' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                'updated' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                'deleted' => 'bg-danger-50 text-danger-700 ring-danger-600/10',
                'restored' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
                'login_success', 'logout_success' => 'bg-surface-100 text-surface-700 ring-surface-500/10',
                default => 'bg-surface-50 text-surface-600 ring-surface-500/10'
            };
            $eventName = match($log->event) {
                'created' => 'Create',
                'updated' => 'Update',
                'deleted' => 'Delete',
                'restored' => 'Restore',
                'login_success' => 'Login',
                'logout_success' => 'Logout',
                default => ucfirst($log->event)
            };
        @endphp
        <span class="inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium ring-1 ring-inset {{ $badgeClass }}">{{ $eventName }}</span>
    </div>
    <div class="border-t border-surface-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-surface-200">
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Waktu Kejadian</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $log->created_at->format('d M Y H:i:s') }} ({{ $log->created_at->diffForHumans() }})</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Pengguna</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">
                    @if($log->user)
                        <div class="flex items-center">
                            <span>{{ $log->user->name }} ({{ $log->user->email }})</span>
                            <a href="{{ route('users.show', $log->user_id) }}" class="ml-2 text-xs text-primary-600 hover:text-primary-900">Lihat Profil</a>
                        </div>
                    @else
                        <span class="text-surface-400 italic">Sistem/Guest</span>
                    @endif
                </dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">IP Address</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $log->ip_address ?? 'Tidak diketahui' }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">User Agent</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0 break-all">{{ $log->user_agent ?? 'Tidak diketahui' }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-surface-50">
                <dt class="text-sm font-medium text-surface-900">Target Modul</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">
                    @if($log->auditable_type)
                        <span class="font-mono text-xs bg-surface-200 px-2 py-1 rounded">{{ $log->auditable_type }}</span> (ID: {{ $log->auditable_id }})
                    @else
                        -
                    @endif
                </dd>
            </div>
        </dl>
    </div>
</div>

@if($log->old_values || $log->new_values)
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Nilai Lama -->
    @if($log->old_values && count($log->old_values) > 0)
    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 bg-danger-50 border-b border-danger-100">
            <h3 class="text-base font-semibold leading-6 text-danger-900">Data Sebelum Perubahan (Old)</h3>
        </div>
        <div class="p-4">
            <pre class="bg-surface-900 text-surface-50 p-4 rounded-md overflow-x-auto text-sm"><code>@php
                echo json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            @endphp</code></pre>
        </div>
    </div>
    @endif

    <!-- Nilai Baru -->
    @if($log->new_values && count($log->new_values) > 0)
    <div class="bg-white shadow sm:rounded-lg overflow-hidden {{ !$log->old_values || count($log->old_values) == 0 ? 'lg:col-span-2' : '' }}">
        <div class="px-4 py-5 sm:px-6 bg-emerald-50 border-b border-emerald-100">
            <h3 class="text-base font-semibold leading-6 text-emerald-900">Data Setelah Perubahan (New)</h3>
        </div>
        <div class="p-4">
            <pre class="bg-surface-900 text-surface-50 p-4 rounded-md overflow-x-auto text-sm"><code>@php
                echo json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            @endphp</code></pre>
        </div>
    </div>
    @endif
</div>
@endif

@endsection
