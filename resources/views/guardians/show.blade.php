@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('guardians.index') }}" class="inline-flex items-center justify-center rounded-md bg-white p-2 text-surface-400 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50 hover:text-surface-500">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span class="sr-only">Kembali</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-surface-900">Detail Wali Murid</h1>
            <p class="mt-1 text-sm text-surface-500">Informasi lengkap wali murid dan riwayat sistem.</p>
        </div>
    </div>
    <div class="mt-4 sm:ml-4 sm:mt-0 flex gap-2">
        @if(!$guardian->trashed())
            @can('update', $guardian)
            <a href="{{ route('guardians.edit', $guardian->id) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Ubah Data
            </a>
            @endcan
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <x-alert />

    @if($guardian->trashed())
    <div class="rounded-md bg-danger-50 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-danger-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-danger-800">Wali Murid Tidak Aktif</h3>
                <div class="mt-2 text-sm text-danger-700">
                    <p>Data wali murid ini telah dihapus dari sistem pada {{ $guardian->deleted_at->format('d M Y H:i') }}.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-surface-200">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-surface-900">Informasi Biodata</h3>
            <p class="mt-1 max-w-2xl text-sm text-surface-500">Detail personal dan kontak wali murid.</p>
        </div>
        <div class="border-t border-surface-200">
            <dl>
                <div class="bg-surface-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-surface-500">Nama Lengkap</dt>
                    <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $guardian->guardian_name }}</dd>
                </div>
                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-surface-500">Hubungan</dt>
                    <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ ucfirst(str_replace('_', ' ', $guardian->guardian_relation)) }}</dd>
                </div>
                <div class="bg-surface-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-surface-500">No. Telepon</dt>
                    <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $guardian->phone_number }}</dd>
                </div>
                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-surface-500">Alamat</dt>
                    <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $guardian->address }}</dd>
                </div>
                <div class="bg-surface-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-surface-500">Akun Taut (User ID)</dt>
                    <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">
                        @if($guardian->user_id)
                            <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-700/10">ID: {{ $guardian->user_id }}</span>
                        @else
                            <span class="text-surface-400 italic">Tidak ditautkan</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-surface-500">Didaftarkan Pada</dt>
                    <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $guardian->created_at ? $guardian->created_at->format('d F Y H:i') : '-' }}</dd>
                </div>
                <div class="bg-surface-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-surface-500">Terakhir Diubah</dt>
                    <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $guardian->updated_at ? $guardian->updated_at->format('d F Y H:i') : '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Daftar Anak/Siswa -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-surface-200">
        <div class="px-4 py-5 sm:px-6 border-b border-surface-200">
            <h3 class="text-lg font-medium leading-6 text-surface-900">Siswa yang Diwaii</h3>
            <p class="mt-1 max-w-2xl text-sm text-surface-500">Daftar siswa yang berada di bawah perwalian ini.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-surface-50">
                    <tr>
                        <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-surface-500 sm:pl-6">NISN</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-surface-500">Nama Siswa</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-surface-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($guardian->students as $student)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900 sm:pl-6">{{ $student->nisn }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $student->name }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">
                                @if($student->trashed() || $student->status === 'keluar')
                                    <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10">{{ ucfirst($student->status) }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">{{ ucfirst($student->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-sm text-surface-500">Belum ada siswa yang diwaii.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
