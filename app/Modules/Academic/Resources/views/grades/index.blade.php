@extends('layouts.app')
@section('title', 'Nilai Siswa')
@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div><h1 class="text-2xl font-bold text-surface-900">Nilai Siswa</h1></div>
</div>
<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900">Siswa</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Mata Pelajaran</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Tugas</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">UTS</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">UAS</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Akhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($grades as $item)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900">{{ $item->student->name ?? '-' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">{{ $item->subject->name ?? '-' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $item->assignment_score }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $item->mid_exam_score }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-500">{{ $item->final_exam_score }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-primary-600">{{ $item->final_score }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-3 py-8 text-center text-sm text-surface-500">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
