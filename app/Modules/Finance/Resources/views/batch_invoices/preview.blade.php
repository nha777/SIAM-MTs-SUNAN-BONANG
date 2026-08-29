@extends('layouts.app')
@section('title', 'Preview Tagihan Massal')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Preview Tagihan Massal</h1>
        <p class="text-sm text-surface-500 mt-1">Periksa kembali daftar siswa dan nominal sebelum tagihan dibuat.</p>
    </div>
</div>

@if($duplicateCount > 0)
<div class="rounded-md bg-warning-50 p-4 mb-6 border border-warning-200">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-warning-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-warning-800">Peringatan Duplikasi ({{ $duplicateCount }} siswa)</h3>
            <div class="mt-2 text-sm text-warning-700">
                @if($duplicateAction === 'skip')
                    <p>Sistem akan <strong>Melewati (Skip)</strong> pembuatan tagihan untuk siswa yang sudah memiliki tagihan ini.</p>
                @elseif($duplicateAction === 'overwrite')
                    <p>Sistem akan <strong>Menimpa (Overwrite)</strong> tagihan lama (yang masih Unpaid) untuk siswa tersebut.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
    <div class="px-4 py-5 sm:px-6 border-b border-surface-200 grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <span class="text-sm text-surface-500 block">Kategori/Template</span>
            <span class="font-semibold text-surface-900">{{ $category->name }}</span>
        </div>
        <div>
            <span class="text-sm text-surface-500 block">Judul Tagihan</span>
            <span class="font-semibold text-surface-900">{{ $request_data['title'] }}</span>
        </div>
        <div>
            <span class="text-sm text-surface-500 block">Nominal (per siswa)</span>
            <span class="font-semibold text-surface-900">Rp {{ number_format($category->default_amount, 0, ',', '.') }}</span>
        </div>
        <div>
            <span class="text-sm text-surface-500 block">Jatuh Tempo</span>
            <span class="font-semibold text-surface-900">{{ \Carbon\Carbon::parse($request_data['due_date'])->format('d M Y') }}</span>
        </div>
    </div>
    
    <div class="overflow-y-auto max-h-96">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50 sticky top-0 shadow-sm">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900">Siswa</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Nominal</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Status Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($previewData as $data)
                <tr class="{{ $data['is_duplicate'] && $duplicateAction === 'skip' ? 'bg-surface-50 opacity-60' : '' }}">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900">
                        {{ $data['student_name'] }}
                        <span class="block text-xs font-normal text-surface-500">{{ $data['nisn'] }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">Rp {{ number_format($data['amount'], 0, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        @if($data['is_duplicate'])
                            @if($duplicateAction === 'skip')
                                <span class="inline-flex items-center rounded-md bg-surface-100 px-2 py-1 text-xs font-medium text-surface-600">Dilewati</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-warning-50 px-2 py-1 text-xs font-medium text-warning-700">Ditimpa (Overwrite)</span>
                            @endif
                        @else
                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">Buat Baru</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<form action="{{ route('batch-invoices.store') }}" method="POST" class="flex gap-4">
    @csrf
    <input type="hidden" name="billing_category_id" value="{{ $request_data['billing_category_id'] }}">
    <input type="hidden" name="academic_year_id" value="{{ $request_data['academic_year_id'] }}">
    <input type="hidden" name="due_date" value="{{ $request_data['due_date'] }}">
    <input type="hidden" name="title" value="{{ $request_data['title'] }}">
    <input type="hidden" name="description" value="{{ $request_data['description'] ?? '' }}">
    <input type="hidden" name="duplicate_action" value="{{ $duplicateAction }}">
    <input type="hidden" name="preview_data" value="{{ json_encode($previewData) }}">
    
    <button type="submit" class="inline-flex justify-center rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
        Konfirmasi & Buat Tagihan
    </button>
    
    <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
        Batal
    </a>
</form>
@endsection
