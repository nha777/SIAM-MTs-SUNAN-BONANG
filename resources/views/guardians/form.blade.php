<div class="space-y-6">
    <!-- Informasi Akun (Opsional) -->
    <div class="border-b border-surface-900/10 pb-6">
        <h2 class="text-base font-semibold leading-7 text-surface-900">Informasi Akun (Opsional)</h2>
        <p class="mt-1 text-sm leading-6 text-surface-600">Tautkan wali murid dengan akun pengguna jika mereka memiliki akses login ke sistem.</p>
        
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
            <div class="sm:col-span-4">
                <x-form-input 
                    type="number" 
                    id="user_id" 
                    name="user_id" 
                    label="User ID (Opsional)" 
                    :value="old('user_id', $guardian->user_id ?? '')" 
                    placeholder="Contoh: 12"
                />
                <p class="mt-1 text-xs text-surface-500">Kosongkan jika wali murid belum memiliki akun login.</p>
            </div>
        </div>
    </div>

    <!-- Informasi Biodata -->
    <div class="border-b border-surface-900/10 pb-6">
        <h2 class="text-base font-semibold leading-7 text-surface-900">Biodata Wali Murid</h2>
        <p class="mt-1 text-sm leading-6 text-surface-600">Informasi detail kontak dan alamat wali murid.</p>

        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
            <div class="sm:col-span-4">
                <x-form-input 
                    type="text" 
                    id="guardian_name" 
                    name="guardian_name" 
                    label="Nama Lengkap Wali" 
                    :value="old('guardian_name', $guardian->guardian_name ?? '')" 
                    placeholder="Masukkan nama lengkap wali"
                    required="true"
                />
            </div>

            <div class="sm:col-span-3">
                <x-form-input 
                    type="select" 
                    id="guardian_relation" 
                    name="guardian_relation" 
                    label="Hubungan dengan Siswa" 
                    required="true"
                >
                    <option value="">-- Pilih Hubungan --</option>
                    <option value="ayah" {{ old('guardian_relation', $guardian->guardian_relation ?? '') === 'ayah' ? 'selected' : '' }}>Ayah</option>
                    <option value="ibu" {{ old('guardian_relation', $guardian->guardian_relation ?? '') === 'ibu' ? 'selected' : '' }}>Ibu</option>
                    <option value="paman_bibi" {{ old('guardian_relation', $guardian->guardian_relation ?? '') === 'paman_bibi' ? 'selected' : '' }}>Paman / Bibi</option>
                    <option value="kakek_nenek" {{ old('guardian_relation', $guardian->guardian_relation ?? '') === 'kakek_nenek' ? 'selected' : '' }}>Kakek / Nenek</option>
                    <option value="lainnya" {{ old('guardian_relation', $guardian->guardian_relation ?? '') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </x-form-input>
            </div>

            <div class="sm:col-span-3">
                <x-form-input 
                    type="text" 
                    id="phone_number" 
                    name="phone_number" 
                    label="Nomor Telepon" 
                    :value="old('phone_number', $guardian->phone_number ?? '')" 
                    placeholder="Contoh: 08123456789"
                    required="true"
                />
            </div>

            <div class="col-span-full">
                <label for="address" class="block text-sm font-medium leading-6 text-surface-900">
                    Alamat Lengkap <span class="text-danger-500">*</span>
                </label>
                <div class="mt-2">
                    <textarea 
                        id="address" 
                        name="address" 
                        rows="3" 
                        class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 placeholder:text-surface-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('address') ring-danger-300 focus:ring-danger-500 @enderror"
                        required
                    >{{ old('address', $guardian->address ?? '') }}</textarea>
                </div>
                @error('address')
                    <p class="mt-2 text-sm text-danger-600" id="address-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>
