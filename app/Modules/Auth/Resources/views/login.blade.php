@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto mt-12">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">Login</h2>
        @if($errors->any())
            <div class="text-red-600 mb-3">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ url('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="block text-sm font-medium">Email</label>
                <input id="email" name="email" type="email" required class="mt-1 block w-full">
            </div>
            <div class="mb-3">
                <label for="password" class="block text-sm font-medium">Password</label>
                <input id="password" name="password" type="password" required class="mt-1 block w-full">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Masuk</button>
            </div>
        </form>
    </div>
</div>
@endsection
