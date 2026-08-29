@extends('layouts.app')
@section('title', 'Tagihan Anak Saya')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-surface-900">Tagihan & Pembayaran</h1>
    <p class="text-sm text-surface-500 mt-1">Daftar tagihan administrasi madrasah untuk anak Anda.</p>
</div>

<x-alert />

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900">Tagihan</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Siswa</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Total</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Tenggat</th>
                    <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($invoices as $invoice)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-surface-900">
                        <strong>{{ $invoice->title }}</strong><br>
                        <span class="text-xs text-surface-500">{{ $invoice->invoice_number }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">{{ $invoice->student->name ?? '-' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-surface-900">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        @if($invoice->status == 'Paid')
                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Lunas</span>
                        @elseif($invoice->status == 'Partial')
                            <span class="inline-flex items-center rounded-md bg-warning-50 px-2 py-1 text-xs font-medium text-warning-800 ring-1 ring-inset ring-warning-600/20">Sisa: Rp {{ number_format($invoice->amount - $invoice->paid_amount, 0, ',', '.') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/20">Belum Dibayar</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $invoice->due_date->format('d M Y') }}</td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <a href="{{ route('portal.invoices.show', $invoice->id) }}" class="inline-flex items-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
                            Detail & Bayar
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-3 py-8 text-center text-sm text-surface-500">Tidak ada tagihan saat ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div class="border-t border-surface-200 px-4 py-3 sm:px-6">
        {{ $invoices->links() }}
    </div>
    @endif
</div>
@endsection
