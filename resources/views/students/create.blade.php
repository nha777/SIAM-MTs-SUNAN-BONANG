@extends('layouts.app')

@section('title', 'Tambah Siswa - SIAM')
@section('header_title', 'Tambah Siswa')

@section('content')
<div class="space-y-6">

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h3 class="text-base font-semibold leading-6 text-surface-900">Tambah Data Siswa</h3>
            <p class="mt-2 text-sm text-surface-700">Masukkan informasi siswa baru ke dalam sistem.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <a href="{{ route('students.index') }}" class="block rounded-md bg-white px-3 py-2 text-center text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white border border-surface-200 rounded-lg shadow-sm">
        <form action="{{ route('students.store') }}" method="POST" class="p-6">
            @csrf
            
            @include('students.form', ['student' => new \App\Modules\Student\Models\Student()])

            <div class="mt-6 flex items-center justify-end gap-x-6 border-t border-surface-200 pt-6">
                <a href="{{ route('students.index') }}" class="text-sm font-semibold leading-6 text-surface-900">Batal</a>
                <button type="submit" class="rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
