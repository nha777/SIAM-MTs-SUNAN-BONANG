@extends('layouts.app')
@section('title', 'Kategori Tagihan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Kategori Tagihan</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola template/kategori tagihan untuk mempermudah pembuatan tagihan.</p>
    </div>
    @can('finance.create')
    <div>
        <a href="{{ route('billing-categories.create') }}" class="inline-flex items-center justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            Tambah Kategori Baru
        </a>
    </div>
    @endcan
</div>

<x-alert />

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-surface-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-surface-900">Nama Kategori</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Deskripsi</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Nominal Default</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-surface-900">Frekuensi</th>
                    <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($categories as $category)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-surface-900">{{ $category->name }}</td>
                    <td class="px-3 py-4 text-sm text-surface-500">{{ Str::limit($category->description, 50) }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-surface-900">Rp {{ number_format($category->default_amount, 0, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        <span class="inline-flex items-center rounded-md bg-surface-100 px-2 py-1 text-xs font-medium text-surface-600">
                            {{ $category->frequency == 'One-time' ? 'Sekali' : ($category->frequency == 'Monthly' ? 'Bulanan' : 'Tahunan') }}
                        </span>
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex justify-end gap-2">
                            @can('finance.update')
                            <a href="{{ route('billing-categories.edit', $category->id) }}" class="text-warning-600 hover:text-warning-900">Edit</a>
                            @endcan
                            @can('finance.delete')
                            <form action="{{ route('billing-categories.destroy', $category->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-danger-600 hover:text-danger-900">Hapus</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-3 py-8 text-center text-sm text-surface-500">Belum ada data kategori tagihan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="border-t border-surface-200 px-4 py-3 sm:px-6">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
