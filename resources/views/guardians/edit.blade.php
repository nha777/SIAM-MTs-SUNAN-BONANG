@extends('layouts.app')

@section('header')
<div class="flex items-center gap-4">
    <a href="{{ route('guardians.index') }}" class="inline-flex items-center justify-center rounded-md bg-white p-2 text-surface-400 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50 hover:text-surface-500">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        <span class="sr-only">Kembali</span>
    </a>
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-surface-900">Ubah Data Wali Murid</h1>
        <p class="mt-1 text-sm text-surface-500">Memperbarui informasi wali murid {{ $guardian->guardian_name }}.</p>
    </div>
</div>
@endsection

@section('content')
<div class="bg-white border border-surface-200 rounded-lg shadow-sm overflow-hidden">
    <form action="{{ route('guardians.update', $guardian->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="p-6 sm:p-8">
            @include('guardians.form')
        </div>

        <div class="bg-surface-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-surface-200">
            <a href="{{ route('guardians.index') }}" class="text-sm font-semibold leading-6 text-surface-900 hover:text-surface-700">Batal</a>
            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
