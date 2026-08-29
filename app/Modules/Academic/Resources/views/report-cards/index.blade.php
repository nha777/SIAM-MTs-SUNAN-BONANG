@extends('layouts.app')
@section('title', 'Rapor Siswa')
@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div><h1 class="text-2xl font-bold text-surface-900">Rapor Siswa</h1></div>
</div>
<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900">Siswa</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Kelas</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Semester</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Absensi (S/I/A)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($reportCards as $item)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900">{{ $item->student->name ?? '-' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">{{ $item->academicClass->name ?? '-' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">{{ $item->semester->name ?? '-' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $item->total_sick }}/{{ $item->total_permission }}/{{ $item->total_absent }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-3 py-8 text-center text-sm text-surface-500">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
