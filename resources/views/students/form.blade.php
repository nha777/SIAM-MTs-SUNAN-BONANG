<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <div>
        <x-form-input 
            type="select"
            name="guardian_id"
            label="Wali Murid" 
            required="true"
        >
            <option value="">-- Pilih Wali Murid --</option>
            @foreach($guardians ?? [] as $g)
                <option value="{{ $g->id }}" {{ old('guardian_id', $student->guardian_id ?? '') == $g->id ? 'selected' : '' }}>{{ $g->guardian_name }}</option>
            @endforeach
        </x-form-input>
    </div>

    <div>
        <x-form-input 
            type="select"
            name="class_id"
            label="Kelas" 
        >
            <option value="">-- Pilih Kelas (opsional) --</option>
            @foreach($classes ?? [] as $c)
                <option value="{{ $c->id }}" {{ old('class_id', $student->class_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>
            @endforeach
        </x-form-input>
    </div>
    <div>
        <x-form-input 
            name="nisn" 
            label="NISN" 
            :value="$student->nisn ?? ''" 
            required="true" 
            placeholder="10 digit NISN" 
        />
    </div>
    
    <div>
        <x-form-input 
            name="name" 
            label="Nama Lengkap" 
            :value="$student->name ?? ''" 
            required="true" 
            placeholder="Nama lengkap siswa" 
        />
    </div>

    <div>
        <x-form-input 
            name="birth_place" 
            label="Tempat Lahir" 
            :value="$student->birth_place ?? ''" 
            placeholder="Kota/Kabupaten kelahiran" 
        />
    </div>

    <div>
        <x-form-input 
            type="date"
            name="birth_date" 
            label="Tanggal Lahir" 
            :value="(isset($student) && $student->birth_date) ? $student->birth_date->format('Y-m-d') : ''" 
        />
    </div>

    <div>
        <x-form-input 
            type="select"
            name="gender" 
            label="Jenis Kelamin" 
            required="true"
        >
            <option value="">-- Pilih Jenis Kelamin --</option>
            <option value="L" {{ old('gender', $student->gender ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('gender', $student->gender ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
        </x-form-input>
    </div>

    <div>
        <x-form-input 
            type="select"
            name="religion" 
            label="Agama" 
        >
            <option value="">-- Pilih Agama --</option>
            @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $religion)
                <option value="{{ $religion }}" {{ old('religion', $student->religion ?? '') === $religion ? 'selected' : '' }}>
                    {{ $religion }}
                </option>
            @endforeach
        </x-form-input>
    </div>

    <div class="sm:col-span-2">
        <x-form-input 
            type="textarea"
            name="address" 
            label="Alamat Lengkap" 
            :value="$student->address ?? ''" 
            placeholder="Jl. Contoh No. 123, RT/RW..." 
        />
    </div>
</div>
