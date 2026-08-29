@extends('layouts.app')

@section('title', 'Edit Semester')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Edit Semester</h1>
        <p class="text-sm text-surface-500 mt-1">Perbarui data semester {{ ucfirst($semester->semester) }} tahun {{ $semester->academicYear->name ?? '' }}.</p>
    </div>
    
    <div class="flex gap-2">
        @can('semester.view')
        <a href="{{ route('semesters.show', $semester->id) }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Lihat Detail
        </a>
        @endcan
        <a href="{{ route('semesters.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
    </div>
</div>

<x-alert />

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('semesters.update', $semester->id) }}" method="POST">
            @method('PUT')
            @include('semesters.form')
        </form>
    </div>
</div>
@endsection
