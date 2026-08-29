@extends('layouts.app')
@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-surface-900">Verifikasi Pembayaran</h1>
    <p class="text-sm text-surface-500 mt-1">Daftar pembayaran yang diunggah oleh orang tua/wali murid dan menunggu verifikasi.</p>
</div>

<x-alert />

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900">Tanggal</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Siswa / Tagihan</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Nominal</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Metode & Ref</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Bukti</th>
                    <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($payments as $payment)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-surface-900">
                        {{ $payment->payment_date->format('d M Y') }}<br>
                        <span class="text-xs text-surface-500">{{ $payment->payment_number }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">
                        <strong>{{ $payment->invoice->student->name ?? '-' }}</strong><br>
                        <a href="{{ route('invoices.show', $payment->invoice_id) }}" class="text-primary-600 hover:underline">{{ $payment->invoice->title ?? '-' }}</a>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-emerald-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">
                        {{ $payment->payment_method }}<br>
                        <span class="text-xs text-surface-500">{{ $payment->reference_number ?? '-' }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">
                        @if($payment->proof_of_payment)
                        <a href="{{ Storage::url($payment->proof_of_payment) }}" target="_blank" class="text-primary-600 hover:underline text-xs">Lihat Bukti</a>
                        @else
                        <span class="text-surface-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        @can('finance.update')
                        <div class="flex flex-col gap-2 justify-end">
                            <form action="{{ route('payment-verifications.verify', $payment->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Verifikasi pembayaran ini?');">
                                @csrf
                                <input type="hidden" name="status" value="Verified">
                                <button type="submit" class="text-xs inline-flex items-center rounded bg-emerald-600 px-2 py-1 font-semibold text-white shadow-sm hover:bg-emerald-500">Setujui</button>
                            </form>
                            
                            <form action="{{ route('payment-verifications.verify', $payment->id) }}" method="POST" class="inline-block" onsubmit="
                                const reason = prompt('Alasan penolakan:'); 
                                if(reason === null) return false; 
                                this.rejection_reason.value = reason; 
                                return true;
                            ">
                                @csrf
                                <input type="hidden" name="status" value="Rejected">
                                <input type="hidden" name="rejection_reason" value="">
                                <button type="submit" class="text-xs inline-flex items-center rounded bg-danger-600 px-2 py-1 font-semibold text-white shadow-sm hover:bg-danger-500">Tolak</button>
                            </form>
                        </div>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-3 py-8 text-center text-sm text-surface-500">Tidak ada pembayaran yang perlu diverifikasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="border-t border-surface-200 px-4 py-3 sm:px-6">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection
