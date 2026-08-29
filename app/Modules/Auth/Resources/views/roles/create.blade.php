@extends('layouts.app')
@section('title', 'Tambah Role')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-surface-900">Tambah Role Baru</h1>
    <p class="text-sm text-surface-500 mt-1">Buat peran baru dan atur izin aksesnya.</p>
</div>

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-surface-900">Nama Role</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="block w-full rounded-md border-0 py-1.5 text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 placeholder:text-surface-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('name') ring-danger-300 focus:ring-danger-500 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-6 border-t border-surface-200 pt-6">
                    <h3 class="text-lg font-medium leading-6 text-surface-900 mb-4">Izin Akses (Permissions)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($permissions as $group => $perms)
                        <div class="bg-surface-50 rounded-lg p-4 border border-surface-200">
                            <h4 class="font-medium text-surface-900 mb-3 capitalize">{{ str_replace('_', ' ', $group) }}</h4>
                            <div class="space-y-3">
                                @foreach($perms as $permission)
                                <div class="relative flex items-start">
                                    <div class="flex h-6 items-center">
                                        <input id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" type="checkbox" @checked(is_array(old('permissions')) && in_array($permission->name, old('permissions'))) class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-600">
                                    </div>
                                    <div class="ml-3 text-sm leading-6">
                                        <label for="perm_{{ $permission->id }}" class="font-medium text-surface-700">{{ explode('.', $permission->name)[1] ?? $permission->name }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @error('permissions')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-x-6 border-t border-surface-200 pt-6">
                <a href="{{ route('roles.index') }}" class="text-sm font-semibold leading-6 text-surface-900 hover:text-surface-700">Batal</a>
                <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Simpan Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
