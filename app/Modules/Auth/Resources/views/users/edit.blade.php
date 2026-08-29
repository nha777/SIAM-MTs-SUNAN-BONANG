@extends('layouts.app')
@section('title', 'Edit Pengguna')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-surface-900">Edit Pengguna: {{ $user->name }}</h1>
    <p class="text-sm text-surface-500 mt-1">Perbarui data pengguna, status aktif, dan rolenya.</p>
</div>

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="name" class="block text-sm font-medium leading-6 text-surface-900">Nama Lengkap</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 placeholder:text-surface-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('name') ring-danger-300 focus:ring-danger-500 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="email" class="block text-sm font-medium leading-6 text-surface-900">Email</label>
                    <div class="mt-2">
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 placeholder:text-surface-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('email') ring-danger-300 focus:ring-danger-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-6">
                    <div class="rounded-md bg-surface-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-surface-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 md:flex md:justify-between">
                                <p class="text-sm text-surface-700">Kosongkan kolom password jika tidak ingin mengubahnya.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="password" class="block text-sm font-medium leading-6 text-surface-900">Password Baru (Opsional)</label>
                    <div class="mt-2">
                        <input type="password" name="password" id="password" class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 placeholder:text-surface-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('password') ring-danger-300 focus:ring-danger-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="password_confirmation" class="block text-sm font-medium leading-6 text-surface-900">Konfirmasi Password Baru</label>
                    <div class="mt-2">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 placeholder:text-surface-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium leading-6 text-surface-900">Role Pengguna</label>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input id="role_{{ $role->id }}" name="roles[]" value="{{ $role->name }}" type="checkbox" @checked(is_array(old('roles', $userRoles)) && in_array($role->name, old('roles', $userRoles))) class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-600">
                            </div>
                            <div class="ml-3 text-sm leading-6">
                                <label for="role_{{ $role->id }}" class="font-medium text-surface-900">{{ $role->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-6 border-t border-surface-200 pt-6">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $user->is_active)) class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="is_active" class="font-medium text-surface-900">Akun Aktif</label>
                            <p class="text-surface-500">Pengguna dapat login jika akun aktif.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-x-6 border-t border-surface-200 pt-6">
                <a href="{{ route('users.index') }}" class="text-sm font-semibold leading-6 text-surface-900 hover:text-surface-700">Batal</a>
                <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
