@extends('layouts.app')
@section('title', 'Detail Tagihan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Detail Tagihan: {{ $invoice->invoice_number }}</h1>
    </div>
    <div>
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
    </div>
</div>

<x-alert />

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 border-b border-surface-200">
                <h3 class="text-base font-semibold leading-6 text-surface-900">Informasi Tagihan</h3>
            </div>
            <div class="border-t border-surface-200 px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-surface-200">
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Judul</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $invoice->title }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Siswa</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $invoice->student->name ?? '-' }} ({{ $invoice->student->nisn ?? '-' }})</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Tahun Ajaran</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $invoice->academicYear->name ?? '-' }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Total Tagihan</dt>
                        <dd class="mt-1 text-sm font-bold text-surface-900 sm:col-span-2 sm:mt-0">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Sudah Dibayar</dt>
                        <dd class="mt-1 text-sm font-bold text-emerald-600 sm:col-span-2 sm:mt-0">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Sisa Tagihan</dt>
                        <dd class="mt-1 text-sm font-bold text-danger-600 sm:col-span-2 sm:mt-0">Rp {{ number_format($invoice->amount - $invoice->paid_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Status</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">
                            @if($invoice->status == 'Paid')
                                <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Lunas</span>
                            @elseif($invoice->status == 'Partial')
                                <span class="inline-flex items-center rounded-md bg-warning-50 px-2 py-1 text-xs font-medium text-warning-800 ring-1 ring-inset ring-warning-600/20">Cicilan</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/20">Belum Dibayar</span>
                            @endif
                        </dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Tenggat</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ optional($invoice->due_date)->format('d M Y') }}</dd>
                    </div>
                    @if($invoice->description)
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-surface-500">Deskripsi</dt>
                        <dd class="mt-1 text-sm text-surface-900 sm:col-span-2 sm:mt-0">{{ $invoice->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-surface-200 flex justify-between items-center">
                <h3 class="text-base font-semibold leading-6 text-surface-900">Riwayat Pembayaran</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-surface-50">
                        <tr>
                            <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900">Tanggal</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">No. Ref</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Metode</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Nominal</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Pencatat</th>
                            <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($invoice->payments as $payment)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-surface-900">{{ $payment->payment_date->format('d M Y') }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">{{ $payment->payment_number }}<br><span class="text-xs text-surface-500">{{ $payment->reference_number }}</span></td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">{{ $payment->payment_method }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-emerald-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $payment->recordedBy->name ?? '-' }}</td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                @can('finance.delete')
                                <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini? Saldo tagihan akan dikembalikan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-danger-600 hover:text-danger-900">Batal</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-3 py-8 text-center text-sm text-surface-500">Belum ada pembayaran untuk tagihan ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($invoice->status != 'Paid')
        @can('finance.create')
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-surface-200">
                <h3 class="text-base font-semibold leading-6 text-surface-900">Catat Pembayaran Baru</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="payment_number" class="block text-sm font-medium leading-6 text-surface-900">No. Kwitansi *</label>
                            <input type="text" name="payment_number" id="payment_number" value="{{ old('payment_number') }}" placeholder="Otomatis" readonly class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6 bg-surface-50">
                            @error('payment_number')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="payment_date" class="block text-sm font-medium leading-6 text-surface-900">Tanggal Pembayaran *</label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            @error('payment_date')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="amount" class="block text-sm font-medium leading-6 text-surface-900">Nominal (Rp) *</label>
                            <input type="number" name="amount" id="amount" value="{{ old('amount', $invoice->amount - $invoice->paid_amount) }}" max="{{ $invoice->amount - $invoice->paid_amount }}" min="1" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <p class="mt-1 text-xs text-surface-500">Sisa tagihan: Rp {{ number_format($invoice->amount - $invoice->paid_amount, 0, ',', '.') }}</p>
                            @error('amount')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="payment_method" class="block text-sm font-medium leading-6 text-surface-900">Metode Pembayaran *</label>
                            <select name="payment_method" id="payment_method" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                                <option value="Tunai">Tunai</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="E-Wallet">E-Wallet</option>
                            </select>
                            @error('payment_method')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="reference_number" class="block text-sm font-medium leading-6 text-surface-900">Nomor Referensi (Opsional)</label>
                            <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" placeholder="Misal: No. Rekening / Ref Trf" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            @error('reference_number')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium leading-6 text-surface-900">Catatan</label>
                            <textarea name="notes" id="notes" rows="2" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('notes') }}</textarea>
                            @error('notes')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 sm:w-auto">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
        @endcan
        @endif
    </div>
</div>
@endsection
