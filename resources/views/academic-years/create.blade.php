@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Tambah Tahun Ajaran</h1>
        <p class="text-sm text-surface-500 mt-1">Tambahkan data tahun ajaran baru ke dalam sistem.</p>
    </div>
    
    <a href="{{ route('academic-years.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
        Kembali
    </a>
</div>

<x-alert />

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('academic-years.store') }}" method="POST">
            @include('academic-years.form')
        </form>
    </div>
</div>
@endsection
