@extends('layouts.app')
@section('title', 'Log Aktivitas')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Log Aktivitas Sistem</h1>
        <p class="text-sm text-surface-500 mt-1">Pantau seluruh aktivitas dan perubahan data dalam sistem.</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white p-4 shadow sm:rounded-lg mb-6">
    <form action="{{ route('activity-logs.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="w-full sm:w-64">
            <label for="search" class="block text-sm font-medium leading-6 text-surface-900">Cari Pengguna/Modul</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nama, email, modul..." class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 placeholder:text-surface-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
        </div>

        <div class="w-full sm:w-48">
            <label for="event" class="block text-sm font-medium leading-6 text-surface-900">Jenis Aktivitas</label>
            <select id="event" name="event" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="">Semua Aktivitas</option>
                <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Data Baru (Create)</option>
                <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Perubahan (Update)</option>
                <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Penghapusan (Delete)</option>
                <option value="restored" {{ request('event') === 'restored' ? 'selected' : '' }}>Pemulihan (Restore)</option>
                <option value="login_success" {{ request('event') === 'login_success' ? 'selected' : '' }}>Login</option>
                <option value="logout_success" {{ request('event') === 'logout_success' ? 'selected' : '' }}>Logout</option>
            </select>
        </div>
        
        <div>
            <button type="submit" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
                Filter
            </button>
            @if(request()->hasAny(['search', 'event']) && (request('search') || request('event')))
                <a href="{{ route('activity-logs.index') }}" class="ml-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-danger-600 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900 sm:pl-6">Waktu</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Pengguna</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Aktivitas</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Modul/Target</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">IP Address</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Detail</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($logs as $log)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-surface-500 sm:pl-6" title="{{ $log->created_at->format('d M Y H:i:s') }}">
                        {{ $log->created_at->diffForHumans() }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-surface-900">
                        @if($log->user)
                            <div class="flex flex-col">
                                <span>{{ $log->user->name }}</span>
                                <span class="text-xs text-surface-500 font-normal">{{ $log->user->email }}</span>
                            </div>
                        @else
                            <span class="text-surface-400 italic">Sistem/Guest</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
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
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $badgeClass }}">{{ $eventName }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        @if($log->auditable_type)
                            <div class="flex flex-col">
                                <span class="truncate max-w-[200px]" title="{{ $log->auditable_type }}">
                                    {{ class_basename($log->auditable_type) }}
                                </span>
                                <span class="text-xs text-surface-400">ID: {{ $log->auditable_id }}</span>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                        {{ $log->ip_address ?? '-' }}
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <a href="{{ route('activity-logs.show', $log->id) }}" class="text-primary-600 hover:text-primary-900">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-3 py-8 text-center text-sm text-surface-500">
                        Tidak ada catatan aktivitas yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="border-t border-surface-200 px-4 py-3 sm:px-6">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
