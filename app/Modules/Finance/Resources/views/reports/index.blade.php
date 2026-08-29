@extends('layouts.app')
@section('title', 'Laporan Keuangan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Laporan Keuangan</h1>
        <p class="text-sm text-surface-500 mt-1">Ringkasan pendapatan madrasah per periode.</p>
    </div>
    <div>
        <a href="{{ route('financial-reports.export', ['month' => $month, 'year' => $year]) }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 mr-2">
            Export CSV
        </a>
        <button onclick="window.print()" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Cetak Laporan
        </button>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('financial-reports.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div>
                <label for="month" class="block text-sm font-medium leading-6 text-surface-900">Bulan</label>
                <select name="month" id="month" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium leading-6 text-surface-900">Tahun</label>
                <select name="year" id="year" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @for($y=date('Y'); $y>=2020; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" class="inline-flex justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-emerald-50 rounded-lg p-6 border border-emerald-200">
            <h3 class="text-emerald-800 text-sm font-medium">Total Pendapatan (Bulan Ini)</h3>
            <p class="text-3xl font-bold text-emerald-900 mt-2">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
        
        <div class="mt-6 bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 border-b border-surface-200">
                <h3 class="text-base font-semibold leading-6 text-surface-900">Pendapatan per Metode</h3>
            </div>
            <ul class="divide-y divide-surface-200">
                @forelse($incomeByMethod as $method)
                <li class="px-4 py-4 flex justify-between">
                    <span class="text-sm font-medium text-surface-900">{{ $method->payment_method }}</span>
                    <span class="text-sm text-surface-500">Rp {{ number_format($method->total, 0, ',', '.') }}</span>
                </li>
                @empty
                <li class="px-4 py-4 text-center text-sm text-surface-500">Belum ada data.</li>
                @endforelse
            </ul>
        </div>
    </div>
    
    <div class="lg:col-span-2">
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 border-b border-surface-200">
                <h3 class="text-base font-semibold leading-6 text-surface-900">Rincian Pendapatan per Tanggal</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-surface-50">
                        <tr>
                            <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900">Tanggal</th>
                            <th class="px-3 py-3.5 text-right text-sm font-semibold text-surface-900">Total Pemasukan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($incomeByDate as $date)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-surface-900">{{ \Carbon\Carbon::parse($date->payment_date)->format('d F Y') }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-surface-900 text-right">Rp {{ number_format($date->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="px-3 py-8 text-center text-sm text-surface-500">Tidak ada transaksi pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
