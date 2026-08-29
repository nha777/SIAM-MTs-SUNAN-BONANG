@csrf

<div class="space-y-12">
    <div class="border-b border-surface-900/10 pb-12">
        <h2 class="text-base font-semibold leading-7 text-surface-900">Informasi Tahun Ajaran</h2>
        <p class="mt-1 text-sm leading-6 text-surface-600">Lengkapi form berikut untuk mengatur tahun ajaran. Pastikan format penulisan benar.</p>

        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
            <div class="sm:col-span-4">
                <x-form-input 
                    name="name" 
                    label="Nama Tahun Ajaran" 
                    :value="old('name', $academicYear->name ?? '')" 
                    placeholder="Contoh: 2023/2024" 
                    required="true"
                    helpText="Format harus YYYY/YYYY (misal: 2023/2024)." />
            </div>
        </div>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-x-6">
    <a href="{{ route('academic-years.index') }}" class="text-sm font-semibold leading-6 text-surface-900">Batal</a>
    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        Simpan
    </button>
</div>
