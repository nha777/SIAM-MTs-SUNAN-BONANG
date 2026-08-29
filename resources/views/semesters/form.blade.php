@csrf

<div class="space-y-12">
    <div class="border-b border-surface-900/10 pb-12">
        <h2 class="text-base font-semibold leading-7 text-surface-900">Informasi Semester</h2>
        <p class="mt-1 text-sm leading-6 text-surface-600">Lengkapi form berikut untuk mengatur semester. Pastikan format penulisan benar.</p>

        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
            <div class="sm:col-span-4">
                <label for="academic_year_id" class="block text-sm font-medium leading-6 text-surface-900">Tahun Ajaran <span class="text-danger-500">*</span></label>
                <div class="mt-2">
                    <select id="academic_year_id" name="academic_year_id" required class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" @selected(old('academic_year_id', $semester->academic_year_id ?? '') == $year->id)>
                                {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('academic_year_id')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-4">
                <label for="semester" class="block text-sm font-medium leading-6 text-surface-900">Semester <span class="text-danger-500">*</span></label>
                <div class="mt-2">
                    <select id="semester" name="semester" required class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                        <option value="">Pilih Semester</option>
                        <option value="ganjil" @selected(old('semester', $semester->semester ?? '') === 'ganjil')>Ganjil</option>
                        <option value="genap" @selected(old('semester', $semester->semester ?? '') === 'genap')>Genap</option>
                    </select>
                </div>
                @error('semester')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-x-6">
    <a href="{{ route('semesters.index') }}" class="text-sm font-semibold leading-6 text-surface-900">Batal</a>
    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        Simpan
    </button>
</div>
