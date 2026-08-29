@extends('layouts.app')
@section('title', 'Buat Tagihan Baru')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Buat Tagihan Baru</h1>
    </div>
    <div>
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Batal
        </a>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg">
    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf
        <div class="px-4 py-5 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="student_id" class="block text-sm font-medium leading-6 text-surface-900">Siswa *</label>
                    <select name="student_id" id="student_id" required readonly class="mt-2 block w-full bg-surface-50 rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="">Pilih Siswa...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }} ({{ $student->nisn }})</option>
                        @endforeach
                    </select>
                    @error('student_id')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium leading-6 text-surface-900">Tahun Ajaran *</label>
                    <select name="academic_year_id" id="academic_year_id" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="">Pilih Tahun Ajaran...</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ old('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                        @endforeach
                    </select>
                    @error('academic_year_id')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="invoice_number" class="block text-sm font-medium leading-6 text-surface-900">Nomor Tagihan *</label>
                    <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number') }}" placeholder="Otomatis" readonly class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6 bg-surface-50">
                    @error('invoice_number')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium leading-6 text-surface-900">Judul Tagihan *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Misal: SPP Bulan Juli" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('title')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium leading-6 text-surface-900">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="amount" class="block text-sm font-medium leading-6 text-surface-900">Nominal Tagihan (Rp) *</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" min="0" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('amount')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium leading-6 text-surface-900">Tenggat Waktu *</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('due_date')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        <div class="bg-surface-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-lg">
            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto">Simpan Tagihan</button>
        </div>
    </form>
</div>
@endsection
