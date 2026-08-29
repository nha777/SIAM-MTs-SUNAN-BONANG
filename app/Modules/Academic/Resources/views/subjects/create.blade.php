@extends('layouts.app')
@section('title', 'Tambah Mata Pelajaran')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Tambah Mata Pelajaran</h1>
    </div>
    <div>
        <a href="{{ route('subjects.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Batal
        </a>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg">
    <form action="{{ route('subjects.store') }}" method="POST">
        @csrf
        <div class="px-4 py-5 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium leading-6 text-surface-900">Kode Mapel *</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('code')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-surface-900">Nama Mapel *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('name')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium leading-6 text-surface-900">Jenis *</label>
                    <select name="type" id="type" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="Umum" {{ old('type') == 'Umum' ? 'selected' : '' }}>Umum</option>
                        <option value="Peminatan" {{ old('type') == 'Peminatan' ? 'selected' : '' }}>Peminatan</option>
                        <option value="Muatan Lokal" {{ old('type') == 'Muatan Lokal' ? 'selected' : '' }}>Muatan Lokal</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="credits" class="block text-sm font-medium leading-6 text-surface-900">SKS / Jam Pelajaran *</label>
                    <input type="number" name="credits" id="credits" value="{{ old('credits', 2) }}" min="1" max="10" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('credits')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium leading-6 text-surface-900">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('description') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <div class="flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="is_active" class="font-medium text-surface-900">Aktif</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-surface-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-lg">
            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto">Simpan</button>
        </div>
    </form>
</div>
@endsection
