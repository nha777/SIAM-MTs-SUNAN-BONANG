@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-surface-900">Dashboard</h1>
    <p class="text-sm text-surface-500 mt-1">Selamat datang di Sistem Informasi Akademik Sekolah (SIAM).</p>
</div>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Stat 1 -->
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <dt class="truncate text-sm font-medium text-surface-500">Total Siswa</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-surface-900">{{ $stats['total_students'] }}</dd>
    </div>
    
    <!-- Stat 2 -->
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <dt class="truncate text-sm font-medium text-surface-500">Total Pegawai</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-surface-900">{{ $stats['total_employees'] }}</dd>
    </div>

    <!-- Stat 3 -->
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <dt class="truncate text-sm font-medium text-surface-500">Total Kelas</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-surface-900">{{ $stats['total_classes'] }}</dd>
    </div>
    
    <!-- Stat 4 -->
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <dt class="truncate text-sm font-medium text-surface-500">Tagihan Belum Lunas</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-danger-600">{{ $stats['total_unpaid_invoices'] }}</dd>
    </div>
</div>
@endsection
