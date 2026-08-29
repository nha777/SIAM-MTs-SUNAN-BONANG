@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-surface-900">Profil Saya</h1>
    <p class="text-sm text-surface-500 mt-1">Kelola informasi akun dan pengaturan keamanan.</p>
</div>

<x-alert />

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Informasi Profil -->
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-surface-900 mb-4">Informasi Pribadi</h3>
                
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                        <div class="sm:col-span-4">
                            <label for="name" class="block text-sm font-medium leading-6 text-surface-900">Nama Lengkap</label>
                            <div class="mt-2">
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('name') ring-danger-300 focus:ring-danger-500 @enderror">
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-4">
                            <label for="email" class="block text-sm font-medium leading-6 text-surface-900">Email</label>
                            <div class="mt-2">
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('email') ring-danger-300 focus:ring-danger-500 @enderror">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ubah Password -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-surface-900 mb-4">Ubah Password</h3>
                
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                        <div class="sm:col-span-4">
                            <label for="current_password" class="block text-sm font-medium leading-6 text-surface-900">Password Saat Ini</label>
                            <div class="mt-2">
                                <input type="password" name="current_password" id="current_password" required class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('current_password') ring-danger-300 focus:ring-danger-500 @enderror">
                            </div>
                            @error('current_password')
                                <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-4">
                            <label for="password" class="block text-sm font-medium leading-6 text-surface-900">Password Baru</label>
                            <div class="mt-2">
                                <input type="password" name="password" id="password" required class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('password') ring-danger-300 focus:ring-danger-500 @enderror">
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-4">
                            <label for="password_confirmation" class="block text-sm font-medium leading-6 text-surface-900">Konfirmasi Password Baru</label>
                            <div class="mt-2">
                                <input type="password" name="password_confirmation" id="password_confirmation" required class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <button type="submit" class="rounded-md bg-surface-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-surface-800">Perbarui Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Detail -->
    <div class="md:col-span-1 space-y-6">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-surface-900 mb-4">Detail Akun</h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="block text-sm font-medium text-surface-500">Peran (Roles)</span>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center rounded-md bg-surface-100 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div>
                        <span class="block text-sm font-medium text-surface-500">Status Akun</span>
                        <div class="mt-1">
                            @if($user->is_active)
                                <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-surface-50 px-2 py-1 text-xs font-medium text-surface-600 ring-1 ring-inset ring-surface-500/10">Non-Aktif</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <span class="block text-sm font-medium text-surface-500">Bergabung Sejak</span>
                        <span class="block mt-1 text-sm text-surface-900">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
