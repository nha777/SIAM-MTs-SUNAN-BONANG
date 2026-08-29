@extends('layouts.app')
@section('title', 'Tambah Rombel Siswa')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Masukkan Siswa ke Rombel</h1>
    </div>
    <div>
        <a href="{{ route('enrollments.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Batal
        </a>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg">
    <form action="{{ route('enrollments.store') }}" method="POST">
        @csrf
        <div class="px-4 py-5 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="student_id" class="block text-sm font-medium leading-6 text-surface-900">Siswa *</label>
                    <select name="student_id" id="student_id" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="">Pilih Siswa...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->nisn }} - {{ $student->name }}</option>
                        @endforeach
                    </select>
                    @error('student_id')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="academic_class_id" class="block text-sm font-medium leading-6 text-surface-900">Kelas *</label>
                    <select name="academic_class_id" id="academic_class_id" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="">Pilih Kelas...</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('academic_class_id') == $class->id ? 'selected' : '' }}>{{ $class->level }} - {{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('academic_class_id')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
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
                    <label for="semester_id" class="block text-sm font-medium leading-6 text-surface-900">Semester *</label>
                    <select name="semester_id" id="semester_id" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="">Pilih Semester...</option>
                        @foreach($semesters as $sm)
                            <option value="{{ $sm->id }}" {{ old('semester_id') == $sm->id ? 'selected' : '' }}>{{ $sm->name }}</option>
                        @endforeach
                    </select>
                    @error('semester_id')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="enrollment_date" class="block text-sm font-medium leading-6 text-surface-900">Tanggal Masuk *</label>
                    <input type="date" name="enrollment_date" id="enrollment_date" value="{{ old('enrollment_date', date('Y-m-d')) }}" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('enrollment_date')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium leading-6 text-surface-900">Status *</label>
                    <select name="status" id="status" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Pindah" {{ old('status') == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                        <option value="Lulus" {{ old('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="Keluar" {{ old('status') == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        <div class="bg-surface-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-lg">
            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto">Simpan</button>
        </div>
    </form>
</div>
@endsection
