@extends('layouts.app')
@section('title', 'Detail Tagihan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">{{ $invoice->title }}</h1>
        <p class="text-sm text-surface-500 mt-1">Siswa: {{ $invoice->student->name }} | No. Tagihan: {{ $invoice->invoice_number }}</p>
    </div>
    <div>
        <a href="{{ route('portal.invoices') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
    </div>
</div>

<x-alert />

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-surface-200">
                <h3 class="text-base font-semibold leading-6 text-surface-900">Riwayat Pembayaran</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-surface-50">
                        <tr>
                            <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900">Tanggal</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Nominal</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Metode & Ref</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($invoice->payments as $payment)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-surface-900">{{ $payment->payment_date->format('d M Y') }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-surface-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">
                                {{ $payment->payment_method }}<br>
                                <span class="text-xs text-surface-500">{{ $payment->reference_number }}</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">
                                @if($payment->status == 'Verified')
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Diverifikasi</span>
                                        <a href="{{ route('receipts.show', $payment->id) }}" target="_blank" class="inline-flex items-center rounded bg-white px-2 py-1 text-xs font-semibold text-primary-600 shadow-sm ring-1 ring-inset ring-primary-600 hover:bg-primary-50">Cetak</a>
                                    </div>
                                @elseif($payment->status == 'Rejected')
                                    <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/20" title="{{ $payment->rejection_reason }}">Ditolak</span>
                                    <p class="text-xs text-danger-600 mt-1">{{ $payment->rejection_reason }}</p>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-warning-50 px-2 py-1 text-xs font-medium text-warning-800 ring-1 ring-inset ring-warning-600/20">Menunggu Verifikasi</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-3 py-8 text-center text-sm text-surface-500">Belum ada riwayat pembayaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($invoice->status != 'Paid')
        <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-surface-200">
            <div class="px-4 py-5 sm:px-6 bg-surface-50 border-b border-surface-200 flex items-center justify-between">
                <h3 class="text-base font-semibold leading-6 text-surface-900">Upload Bukti Pembayaran (Konfirmasi Pembayaran)</h3>
                <span class="text-sm text-surface-500">Sisa Tagihan: <strong class="text-danger-600">Rp {{ number_format($invoice->amount - $invoice->paid_amount, 0, ',', '.') }}</strong></span>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('portal.invoices.pay', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="amount" class="block text-sm font-medium leading-6 text-surface-900">Nominal yang Dibayarkan (Rp) *</label>
                            <input type="number" name="amount" id="amount" value="{{ old('amount', $invoice->amount - $invoice->paid_amount) }}" max="{{ $invoice->amount - $invoice->paid_amount }}" min="1" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            @error('amount')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="payment_date" class="block text-sm font-medium leading-6 text-surface-900">Tanggal Transfer *</label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            @error('payment_date')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium leading-6 text-surface-900">Bank Tujuan *</label>
                            <select name="payment_method" id="payment_method" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                                <option value="BSI (Bank Syariah Indonesia)">BSI (Bank Syariah Indonesia) - 123456789</option>
                                <option value="BCA">BCA - 987654321</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                            @error('payment_method')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="reference_number" class="block text-sm font-medium leading-6 text-surface-900">Atas Nama / No. Rek Pengirim *</label>
                            <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" required placeholder="Cth: Bpk Budi / 1234xxx" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            @error('reference_number')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="proof" class="block text-sm font-medium leading-6 text-surface-900">Upload Struk/Bukti Transfer (JPG, PNG) *</label>
                            <input type="file" name="proof" id="proof" accept="image/*" required class="mt-2 block w-full text-sm text-surface-900 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                            @error('proof')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:w-auto">Kirim Bukti Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-surface-200">
                <h3 class="text-base font-semibold leading-6 text-surface-900">Informasi Pembayaran</h3>
            </div>
            <div class="px-4 py-5 sm:p-6 text-sm text-surface-600 space-y-4">
                <p>Silakan lakukan pembayaran ke salah satu rekening madrasah berikut:</p>
                <div class="bg-surface-50 p-4 rounded-md border border-surface-200">
                    <p class="font-bold text-surface-900">Bank Syariah Indonesia (BSI)</p>
                    <p class="text-lg font-mono text-primary-700 my-1">123-456-7890</p>
                    <p>a.n. Madrasah SIAM</p>
                </div>
                <div class="bg-surface-50 p-4 rounded-md border border-surface-200">
                    <p class="font-bold text-surface-900">Bank BCA</p>
                    <p class="text-lg font-mono text-primary-700 my-1">098-765-4321</p>
                    <p>a.n. Yayasan SIAM</p>
                </div>
                <div class="bg-surface-50 p-4 rounded-md border border-surface-200 text-center">
                    <p class="font-bold text-surface-900 mb-2">Bayar dengan QRIS</p>
                    <!-- Mock QRIS Image -->
                    <div class="w-32 h-32 bg-white mx-auto border-4 border-surface-900 p-2 relative flex items-center justify-center">
                        <div class="absolute inset-0 grid grid-cols-2 grid-rows-2 gap-1 p-2">
                            <div class="bg-surface-900"></div>
                            <div class="bg-surface-900"></div>
                            <div class="bg-surface-900"></div>
                            <div class="bg-surface-900 rounded-bl-xl"></div>
                        </div>
                        <span class="bg-white z-10 px-2 font-bold text-xs">QRIS</span>
                    </div>
                    <p class="text-xs text-surface-500 mt-2">Scan menggunakan aplikasi M-Banking atau E-Wallet (OVO, GoPay, Dana, LinkAja)</p>
                </div>
                <p class="text-xs mt-4">Penting: Setelah melakukan transfer, mohon untuk segera mengupload bukti pembayaran agar dapat diverifikasi oleh bagian keuangan.</p>
            </div>
        </div>
    </div>
</div>
@endsection
