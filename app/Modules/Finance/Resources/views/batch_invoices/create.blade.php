@extends('layouts.app')
@section('title', 'Buat Tagihan Massal')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Buat Tagihan Massal</h1>
        <p class="text-sm text-surface-500 mt-1">Generasi tagihan massal dengan filter spesifik.</p>
    </div>
    <div>
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Kembali
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="rounded-md bg-danger-50 p-4 mb-6 border border-danger-200">
        <div class="flex">
            <div class="ml-3">
                <h3 class="text-sm font-medium text-danger-800">Terdapat kesalahan:</h3>
                <div class="mt-2 text-sm text-danger-700">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="bg-white shadow sm:rounded-lg">
    <form action="{{ route('batch-invoices.preview') }}" method="POST">
        @csrf
        <div class="px-4 py-5 sm:p-6 space-y-8">
            <!-- 1. PENGATURAN DASAR -->
            <div>
                <h3 class="text-base font-semibold leading-7 text-surface-900">1. Pengaturan Dasar</h3>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="billing_category_id" class="block text-sm font-medium leading-6 text-surface-900">Template / Kategori *</label>
                        <select name="billing_category_id" id="billing_category_id" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <option value="">-- Pilih Template --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }} (Rp {{ number_format($cat->default_amount, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium leading-6 text-surface-900">Tahun Ajaran *</label>
                        <select name="academic_year_id" id="academic_year_id" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ ($activeYear && $activeYear->id == $ay->id) ? 'selected' : '' }}>
                                    {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium leading-6 text-surface-900">Jatuh Tempo *</label>
                        <input type="date" name="due_date" id="due_date" required value="{{ date('Y-m-d', strtotime('+7 days')) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>
                    
                    <div>
                        <label for="title" class="block text-sm font-medium leading-6 text-surface-900">Judul Tagihan Spesifik *</label>
                        <input type="text" name="title" id="title" required placeholder="Cth: SPP Bulan Agustus 2026" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <p class="mt-1 text-xs text-surface-500">Judul ini digunakan untuk pengecekan duplikasi.</p>
                    </div>
                </div>
            </div>

            <hr class="border-surface-200">

            <!-- 2. FILTER TARGET -->
            <div>
                <h3 class="text-base font-semibold leading-7 text-surface-900">2. Filter Target Siswa</h3>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="target_type" class="block text-sm font-medium leading-6 text-surface-900">Target *</label>
                        <select name="target_type" id="target_type" onchange="toggleFilterParams()" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <option value="all">☑ Semua Siswa Aktif</option>
                            <option value="class">☐ Per Kelas</option>
                            <option value="selected">☐ Pilih Siswa Manual</option>
                            <option value="gender">☐ Berdasarkan Jenis Kelamin</option>
                            <option value="status">☐ Berdasarkan Status Khusus</option>
                            <option value="alumni">☐ Alumni (Lulus)</option>
                            <option value="scholarship">☐ Penerima Beasiswa</option>
                        </select>
                    </div>

                    <!-- Filter Params Contextual -->
                    <div id="filter_class" class="hidden filter-param">
                        <label class="block text-sm font-medium leading-6 text-surface-900">Pilih Kelas</label>
                        <select name="class_id" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="filter_selected" class="hidden filter-param col-span-2">
                        <label class="block text-sm font-medium leading-6 text-surface-900">Pilih Siswa (Gunakan Ctrl/Cmd untuk multi-select)</label>
                        <select name="selected_students[]" multiple size="5" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nisn }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="filter_gender" class="hidden filter-param">
                        <label class="block text-sm font-medium leading-6 text-surface-900">Pilih Jenis Kelamin</label>
                        <select name="gender" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    
                    <div id="filter_status" class="hidden filter-param">
                        <label class="block text-sm font-medium leading-6 text-surface-900">Pilih Status</label>
                        <select name="status" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <option value="aktif">Aktif</option>
                            <option value="skorsing">Skorsing</option>
                            <option value="keluar">Keluar</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="border-surface-200">

            <!-- 3. DUPLIKASI -->
            <div>
                <h3 class="text-base font-semibold leading-7 text-surface-900">3. Perlindungan Duplikasi</h3>
                <p class="text-sm text-surface-500 mb-4">Aksi jika ditemukan siswa yang sudah memiliki tagihan dengan judul yang sama.</p>
                <div class="mt-4 space-y-4">
                    <div class="flex items-center">
                        <input id="dup_skip" name="duplicate_action" type="radio" value="skip" checked class="h-4 w-4 border-surface-300 text-primary-600 focus:ring-primary-600">
                        <label for="dup_skip" class="ml-3 block text-sm font-medium leading-6 text-surface-900">○ Lewati (Skip) - Jangan buat tagihan baru</label>
                    </div>
                    <div class="flex items-center">
                        <input id="dup_overwrite" name="duplicate_action" type="radio" value="overwrite" class="h-4 w-4 border-surface-300 text-primary-600 focus:ring-primary-600">
                        <label for="dup_overwrite" class="ml-3 block text-sm font-medium leading-6 text-surface-900">○ Timpa (Overwrite) - Hapus yang lama (hanya jika Unpaid), buat baru</label>
                    </div>
                    <div class="flex items-center">
                        <input id="dup_abort" name="duplicate_action" type="radio" value="abort" class="h-4 w-4 border-surface-300 text-primary-600 focus:ring-primary-600">
                        <label for="dup_abort" class="ml-3 block text-sm font-medium leading-6 text-surface-900">○ Batalkan (Abort) - Batalkan seluruh proses batch jika ada 1 saja duplikat</label>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 mt-6">
                <label for="description" class="block text-sm font-medium leading-6 text-surface-900">Deskripsi Tambahan</label>
                <textarea name="description" id="description" rows="2" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6"></textarea>
            </div>
        </div>
        
        <div class="bg-surface-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-lg">
            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto">Preview Tagihan</button>
        </div>
    </form>
</div>

<script>
    function toggleFilterParams() {
        const val = document.getElementById('target_type').value;
        document.querySelectorAll('.filter-param').forEach(el => el.classList.add('hidden'));
        
        const target = document.getElementById('filter_' + val);
        if (target) target.classList.remove('hidden');
    }
    // init
    toggleFilterParams();
</script>
@endsection
