@extends('layouts.app')
@section('title', 'Detail Pengguna')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Detail Pengguna: {{ $user->name }}</h1>
        <p class="text-sm text-surface-500 mt-1">Informasi lengkap pengguna sistem.</p>
    </div>
    
    <div class="flex gap-2">
        <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
        @can('user.update')
        <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
            Edit Pengguna
        </a>
        @endcan
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-base font-semibold leading-6 text-surface-900">Informasi Akun</h3>
        <p class="mt-1 max-w-2xl text-sm text-surface-500">Detail identitas pengguna.</p>
    </div>
    <div class="border-t border-surface-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-surface-200">
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Nama Lengkap</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $user->name }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Email</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $user->email }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Status Akun</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">
                    @if($user->trashed())
                        <span class="inline-flex items-center rounded-md bg-danger-100 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">Dihapus (Soft Delete)</span>
                    @elseif($user->is_active)
                        <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">Aktif</span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-surface-50 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">Non-Aktif</span>
                    @endif
                </dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Peran (Roles)</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">
                    @forelse($user->roles as $role)
                        <span class="inline-flex items-center rounded-md bg-surface-100 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10 mr-1 mb-1">{{ $role->name }}</span>
                    @empty
                        <span class="text-surface-400 italic">Tidak ada role terdaftar</span>
                    @endforelse
                </dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Dibuat Pada</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $user->created_at->format('d M Y H:i') }}</dd>
            </div>
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-surface-900">Terakhir Diperbarui</dt>
                <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $user->updated_at->format('d M Y H:i') }}</dd>
            </div>
            @if($user->trashed())
            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-danger-50">
                <dt class="text-sm font-medium text-danger-900">Dihapus Pada</dt>
                <dd class="mt-1 text-sm leading-6 text-danger-700 sm:col-span-2 sm:mt-0">{{ $user->deleted_at->format('d M Y H:i') }}</dd>
            </div>
            @endif
        </dl>
    </div>
</div>
@endsection
