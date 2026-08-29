@extends('layouts.app')

@section('title', 'Detail Siswa - SIAM')
@section('header_title', 'Detail Siswa')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-2xl">
                {{ substr($student->name, 0, 1) }}
            </div>
            <div>
                <h3 class="text-xl font-bold leading-7 text-surface-900 sm:truncate sm:tracking-tight">{{ $student->name }}</h3>
                <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-surface-500">
                        <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-surface-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM6.75 9.25a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" />
                        </svg>
                        NISN: {{ $student->nisn }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-surface-500">
                        @if($student->trashed())
                            <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">Tidak Aktif / Dihapus</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">Aktif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 flex gap-2">
            <a href="{{ route('students.index') }}" class="block rounded-md bg-white px-3 py-2 text-center text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
                Kembali
            </a>
            @if(!$student->trashed())
                @can('update', $student)
                <a href="{{ route('students.edit', $student->id) }}" class="block rounded-md bg-yellow-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-yellow-500">
                    Ubah Data
                </a>
                @endcan
            @endif
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left Column (Bio) -->
        <div class="md:col-span-2 space-y-6">
            <!-- Biodata Card -->
            <div class="bg-white rounded-lg shadow-sm border border-surface-200 overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-surface-200 bg-surface-50">
                    <h3 class="text-base font-semibold leading-6 text-surface-900">Biodata Siswa</h3>
                    <p class="mt-1 max-w-2xl text-sm text-surface-500">Informasi pribadi siswa.</p>
                </div>
                <div class="border-t border-surface-100">
                    <dl class="divide-y divide-gray-100">
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-surface-50">
                            <dt class="text-sm font-medium text-surface-900">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $student->name }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-surface-50">
                            <dt class="text-sm font-medium text-surface-900">NISN</dt>
                            <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $student->nisn }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-surface-50">
                            <dt class="text-sm font-medium text-surface-900">Tempat, Tanggal Lahir</dt>
                            <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">
                                {{ $student->birth_place ?: '-' }}, 
                                {{ $student->birth_date ? $student->birth_date->format('d F Y') : '-' }}
                            </dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-surface-50">
                            <dt class="text-sm font-medium text-surface-900">Jenis Kelamin</dt>
                            <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">
                                {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-surface-50">
                            <dt class="text-sm font-medium text-surface-900">Agama</dt>
                            <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $student->religion ?: '-' }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-surface-50">
                            <dt class="text-sm font-medium text-surface-900">Alamat</dt>
                            <dd class="mt-1 text-sm leading-6 text-surface-700 sm:col-span-2 sm:mt-0">{{ $student->address ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Right Column (Guardian & Academic) -->
        <div class="space-y-6">
            
            <!-- Guardian Card -->
            <div class="bg-white rounded-lg shadow-sm border border-surface-200 overflow-hidden">
                <div class="px-4 py-4 sm:px-6 border-b border-surface-200 bg-surface-50">
                    <h3 class="text-sm font-semibold leading-6 text-surface-900">Data Wali Murid</h3>
                </div>
                <div class="px-4 py-5 sm:p-6 text-sm text-surface-700">
                    @if($student->guardian)
                        <div class="space-y-3">
                            <div>
                                <span class="block text-xs font-medium text-surface-500 uppercase">Nama</span>
                                <span class="font-medium text-surface-900">{{ $student->guardian->name }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-surface-500 uppercase">Hubungan</span>
                                <span>{{ $student->guardian->relationship ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-surface-500 uppercase">No. Telepon</span>
                                <span>{{ $student->guardian->phone ?: '-' }}</span>
                            </div>
                            <div class="pt-2">
                                <a href="#" class="text-primary-600 hover:text-primary-800 font-medium">Lihat Detail Wali &rarr;</a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4 text-surface-500">
                            Belum ada data wali murid.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Academic Status Card -->
            <div class="bg-white rounded-lg shadow-sm border border-surface-200 overflow-hidden">
                <div class="px-4 py-4 sm:px-6 border-b border-surface-200 bg-surface-50">
                    <h3 class="text-sm font-semibold leading-6 text-surface-900">Status Akademik</h3>
                </div>
                <div class="px-4 py-5 sm:p-6 text-sm text-surface-700">
                    @if($student->academicClass)
                        <div class="space-y-3">
                            <div>
                                <span class="block text-xs font-medium text-surface-500 uppercase">Kelas Saat Ini</span>
                                <span class="font-medium text-surface-900">{{ $student->academicClass->name }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-surface-500 uppercase">Tahun Ajaran</span>
                                <span>{{ $student->academicClass->academicYear->name ?? '-' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4 text-surface-500">
                            Belum terdaftar di kelas manapun.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
