@extends('layouts.app')
@section('title', 'Detail Pegawai')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Detail Pegawai</h1>
        <p class="text-sm text-surface-500 mt-1">Informasi lengkap pegawai atau guru.</p>
    </div>
    
    <div class="flex gap-2">
        @can('employee.update')
        <a href="{{ route('employees.edit', $employee->id) }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-warning-700 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Edit Pegawai
        </a>
        @endcan
        <a href="{{ route('employees.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Profil Singkat -->
    <div class="md:col-span-1 space-y-6">
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6 text-center">
                <div class="mx-auto h-24 w-24 rounded-full bg-surface-200 flex items-center justify-center text-3xl text-surface-600 font-bold mb-4">
                    {{ substr($employee->name, 0, 1) }}
                </div>
                <h3 class="text-lg font-bold text-surface-900">{{ $employee->name }}</h3>
                <p class="text-sm text-surface-500">{{ $employee->position }}</p>
                
                <div class="mt-4 flex justify-center">
                    @if($employee->is_active)
                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Aktif</span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-surface-50 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">Non-Aktif</span>
                    @endif
                </div>
            </div>
            
            <div class="border-t border-surface-200 px-4 py-4 sm:px-6">
                <h4 class="text-sm font-medium text-surface-900 mb-3">Informasi Akun</h4>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-surface-500">Status Akun Login</span>
                        @if($employee->user_id)
                            <span class="font-medium text-emerald-600">Tersedia</span>
                        @else
                            <span class="font-medium text-surface-500">Tidak Ada</span>
                        @endif
                    </div>
                    @if($employee->user_id)
                    <div class="flex justify-between text-sm">
                        <span class="text-surface-500">Username/Email</span>
                        <span class="font-medium text-surface-900">{{ $employee->user->email }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Data Detail -->
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-base font-semibold leading-6 text-surface-900">Informasi Pribadi & Kepegawaian</h3>
            </div>
            <div class="border-t border-surface-200 px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-surface-200">
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $employee->name }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $employee->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">NIP</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $employee->nip ?? '-' }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">NUPTK</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $employee->nuptk ?? '-' }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Tempat, Tanggal Lahir</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">
                            {{ $employee->birth_place ?? '-' }}, 
                            {{ $employee->birth_date ? $employee->birth_date->format('d F Y') : '-' }}
                        </dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">No. HP</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $employee->phone ?? '-' }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Email</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $employee->email ?? '-' }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Alamat</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $employee->address ?? '-' }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Tanggal Bergabung</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $employee->join_date ? $employee->join_date->format('d F Y') : '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
