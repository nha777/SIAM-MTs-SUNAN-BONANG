@extends('layouts.app')
@section('title', 'Tagihan & Pembayaran')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Tagihan & Pembayaran</h1>
    </div>
    @can('finance.create')
    <div>
        <a href="{{ route('invoices.create') }}" class="inline-flex items-center justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            Buat Tagihan Baru
        </a>
    </div>
    @endcan
</div>

<x-alert />

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900">No. Tagihan</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Siswa</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Judul</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Nominal</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Terbayar</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">TenggatWaktu</th>
                    <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($invoices as $invoice)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900">{{ $invoice->invoice_number }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">{{ $invoice->student->name ?? '-' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">{{ $invoice->title }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-surface-900">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-emerald-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        @if($invoice->status == 'Paid')
                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Lunas</span>
                        @elseif($invoice->status == 'Partial')
                            <span class="inline-flex items-center rounded-md bg-warning-50 px-2 py-1 text-xs font-medium text-warning-800 ring-1 ring-inset ring-warning-600/20">Cicilan</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/20">Belum Dibayar</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $invoice->due_date->format('d M Y') }}</td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-primary-600 hover:text-primary-900">Detail</a>
                            @can('finance.update')
                            <a href="{{ route('invoices.edit', $invoice->id) }}" class="text-warning-600 hover:text-warning-900">Edit</a>
                            @endcan
                            @can('finance.delete')
                            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-danger-600 hover:text-danger-900">Hapus</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-3 py-8 text-center text-sm text-surface-500">Belum ada tagihan.</td></tr>
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
