@extends('layouts.app')
@section('title', 'Tambah Kelas')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('classes.index') }}" class="text-surface-500 hover:text-surface-900">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold text-surface-900">Tambah Kelas</h1>
            <p class="text-sm text-surface-500 mt-1">Tambahkan data kelas baru ke dalam sistem.</p>
        </div>
    </div>
</div>

<x-alert />

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('classes.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                
                <x-form-group name="academic_year_id" label="Tahun Ajaran" required>
                    <x-select id="academic_year_id" name="academic_year_id" :error="$errors->has('academic_year_id')" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </x-select>
                </x-form-group>

                <x-form-input name="grade" label="Tingkat" type="select" required>
                    <option value="">-- Pilih Tingkat --</option>
                    <option value="7" {{ old('grade') == '7' ? 'selected' : '' }}>VII (7)</option>
                    <option value="8" {{ old('grade') == '8' ? 'selected' : '' }}>VIII (8)</option>
                    <option value="9" {{ old('grade') == '9' ? 'selected' : '' }}>IX (9)</option>
                </x-form-input>

                <x-form-input name="name" label="Nama Kelas (Tanpa Tingkat)" value="{{ old('name') }}" required placeholder="Cth: A, B, C, atau IPA 1" />
                
                <x-form-input name="capacity" label="Kapasitas Siswa" type="number" value="{{ old('capacity', 32) }}" required />
                
                <x-form-input name="display_order" label="Urutan Tampilan" type="number" value="{{ old('display_order', 0) }}" required />
                
            </div>
            
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('classes.index') }}" class="text-sm font-semibold leading-6 text-surface-900 hover:text-surface-700">Batal</a>
                <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
