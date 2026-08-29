@extends('layouts.app')
@section('title', 'Tambah Kategori Tagihan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Tambah Kategori Tagihan</h1>
    </div>
    <div>
        <a href="{{ route('billing-categories.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Batal
        </a>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg">
    <form action="{{ route('billing-categories.store') }}" method="POST">
        @csrf
        <div class="px-4 py-5 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium leading-6 text-surface-900">Nama Kategori *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Misal: SPP, Uang Gedung, Seragam" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('name')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium leading-6 text-surface-900">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="default_amount" class="block text-sm font-medium leading-6 text-surface-900">Nominal Default (Rp) *</label>
                    <input type="number" name="default_amount" id="default_amount" value="{{ old('default_amount', 0) }}" min="0" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('default_amount')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="frequency" class="block text-sm font-medium leading-6 text-surface-900">Frekuensi *</label>
                    <select name="frequency" id="frequency" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="One-time" {{ old('frequency') == 'One-time' ? 'selected' : '' }}>Sekali Bayar (One-time)</option>
                        <option value="Monthly" {{ old('frequency') == 'Monthly' ? 'selected' : '' }}>Bulanan (Monthly)</option>
                        <option value="Yearly" {{ old('frequency') == 'Yearly' ? 'selected' : '' }}>Tahunan (Yearly)</option>
                    </select>
                    @error('frequency')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        <div class="bg-surface-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-lg">
            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto">Simpan Kategori</button>
        </div>
    </form>
</div>
@endsection
