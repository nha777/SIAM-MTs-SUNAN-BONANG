@extends('layouts.app')
@section('title', 'Dashboard Keuangan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-surface-900">Dashboard Keuangan</h1>
    <p class="text-sm text-surface-500 mt-1">Ringkasan transaksi, tagihan, dan pendapatan.</p>
</div>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border-l-4 border-primary-500">
        <dt class="truncate text-sm font-medium text-surface-500">Total Potensi Tagihan</dt>
        <dd class="mt-1 text-2xl font-semibold tracking-tight text-surface-900">Rp {{ number_format($stats['total_invoices_amount'], 0, ',', '.') }}</dd>
    </div>
    
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border-l-4 border-emerald-500">
        <dt class="truncate text-sm font-medium text-surface-500">Total Pendapatan (Lunas/Cicilan)</dt>
        <dd class="mt-1 text-2xl font-semibold tracking-tight text-emerald-600">Rp {{ number_format($stats['total_paid_amount'], 0, ',', '.') }}</dd>
    </div>

    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border-l-4 border-danger-500">
        <dt class="truncate text-sm font-medium text-surface-500">Total Piutang (Belum Dibayar)</dt>
        <dd class="mt-1 text-2xl font-semibold tracking-tight text-danger-600">Rp {{ number_format($stats['total_unpaid_amount'], 0, ',', '.') }}</dd>
    </div>
    
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border-l-4 border-warning-500 relative">
        <dt class="truncate text-sm font-medium text-surface-500">Menunggu Verifikasi</dt>
        <dd class="mt-1 text-2xl font-semibold tracking-tight text-surface-900">{{ $stats['pending_verifications'] }} Pembayaran</dd>
        @if($stats['pending_verifications'] > 0)
        <a href="{{ route('payment-verifications.index') }}" class="absolute inset-0"></a>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-white shadow rounded-lg p-6">
        <h3 class="text-base font-semibold leading-6 text-surface-900 mb-4">Status Tagihan</h3>
        <ul class="space-y-4">
            <li class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-danger-500 mr-2"></span>
                    <span class="text-sm text-surface-600">Belum Dibayar</span>
                </div>
                <span class="font-semibold text-surface-900">{{ $stats['count_unpaid'] }}</span>
            </li>
            <li class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-warning-500 mr-2"></span>
                    <span class="text-sm text-surface-600">Cicilan</span>
                </div>
                <span class="font-semibold text-surface-900">{{ $stats['count_partial'] }}</span>
            </li>
            <li class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></span>
                    <span class="text-sm text-surface-600">Lunas</span>
                </div>
                <span class="font-semibold text-surface-900">{{ $stats['count_paid'] }}</span>
            </li>
        </ul>
    </div>
    
    <div class="lg:col-span-2 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-surface-200">
            <h3 class="text-base font-semibold leading-6 text-surface-900">Pembayaran Terakhir (Terverifikasi)</h3>
        </div>
        <ul role="list" class="divide-y divide-surface-100">
            @forelse($recentPayments as $payment)
            <li class="px-4 py-4 sm:px-6 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-surface-900">{{ $payment->invoice->student->name ?? 'Siswa' }}</span>
                    <span class="text-xs text-surface-500">{{ $payment->invoice->title ?? 'Tagihan' }} - {{ $payment->payment_date->format('d M Y') }}</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-bold text-emerald-600">+ Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    <br>
                    <span class="text-xs text-surface-500">{{ $payment->payment_method }}</span>
                </div>
            </li>
            @empty
            <li class="px-4 py-4 sm:px-6 text-center text-sm text-surface-500">Belum ada pembayaran diverifikasi.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
