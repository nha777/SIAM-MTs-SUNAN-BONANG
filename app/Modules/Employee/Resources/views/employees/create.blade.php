@extends('layouts.app')
@section('title', 'Tambah Pegawai')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Tambah Pegawai Baru</h1>
        <p class="text-sm text-surface-500 mt-1">Masukkan informasi detail pegawai atau guru.</p>
    </div>
    <div>
        <a href="{{ route('employees.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50">
            Batal
        </a>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg">
    <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        <div class="px-4 py-5 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-surface-900">Nama Lengkap *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('name')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="gender" class="block text-sm font-medium leading-6 text-surface-900">Jenis Kelamin *</label>
                    <select name="gender" id="gender" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="">Pilih...</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="nip" class="block text-sm font-medium leading-6 text-surface-900">NIP</label>
                    <input type="text" name="nip" id="nip" value="{{ old('nip') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('nip')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="nuptk" class="block text-sm font-medium leading-6 text-surface-900">NUPTK</label>
                    <input type="text" name="nuptk" id="nuptk" value="{{ old('nuptk') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('nuptk')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="birth_place" class="block text-sm font-medium leading-6 text-surface-900">Tempat Lahir</label>
                    <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                </div>

                <div>
                    <label for="birth_date" class="block text-sm font-medium leading-6 text-surface-900">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-surface-900">No. HP</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-surface-900">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                    @error('email')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium leading-6 text-surface-900">Alamat</label>
                    <textarea name="address" id="address" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium leading-6 text-surface-900">Posisi *</label>
                    <select name="position" id="position" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="Guru" {{ old('position') == 'Guru' ? 'selected' : '' }}>Guru</option>
                        <option value="Staff" {{ old('position') == 'Staff' ? 'selected' : '' }}>Staff / TU</option>
                    </select>
                </div>

                <div>
                    <label for="join_date" class="block text-sm font-medium leading-6 text-surface-900">Tanggal Bergabung</label>
                    <input type="date" name="join_date" id="join_date" value="{{ old('join_date') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-surface-900 ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                </div>

                <div class="md:col-span-2 mt-4 pt-4 border-t border-surface-200">
                    <div class="flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="create_user" name="create_user" type="checkbox" value="1" {{ old('create_user') ? 'checked' : '' }} class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="create_user" class="font-medium text-surface-900">Buat Akun Pengguna</label>
                            <p class="text-surface-500">Centang untuk membuatkan akun login secara otomatis. Password default adalah NIP atau 'password123'. Email wajib diisi.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="bg-surface-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-lg">
            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto">Simpan</button>
            <a href="{{ route('employees.index') }}" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50 sm:mt-0 sm:w-auto">Batal</a>
        </div>
    </form>
</div>
@endsection
