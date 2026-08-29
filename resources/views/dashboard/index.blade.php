@extends('layouts.app')

@section('title', 'Dashboard - SIAM')
@section('header_title', 'Dashboard Overview')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome Banner -->
    <div class="bg-white rounded-lg border border-surface-200 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-surface-800">Selamat Datang, {{ auth()->user()->name ?? 'Pengguna' }}!</h3>
        <p class="mt-1 text-sm text-surface-500">Ini adalah ringkasan data Sistem Informasi Akademik Madrasah hari ini.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Stat Card 1 -->
        <div class="rounded-lg bg-white p-6 border border-surface-200 shadow-sm flex items-center gap-4">
            <div class="rounded-full bg-primary-50 p-3 text-primary-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-surface-500">Total Siswa</p>
                <p class="text-2xl font-bold text-surface-900">0</p>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="rounded-lg bg-white p-6 border border-surface-200 shadow-sm flex items-center gap-4">
            <div class="rounded-full bg-primary-50 p-3 text-primary-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-surface-500">Total Wali Murid</p>
                <p class="text-2xl font-bold text-surface-900">0</p>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="rounded-lg bg-white p-6 border border-surface-200 shadow-sm flex items-center gap-4">
            <div class="rounded-full bg-purple-50 p-3 text-purple-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-surface-500">Total Kelas</p>
                <p class="text-2xl font-bold text-surface-900">0</p>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="rounded-lg bg-white p-6 border border-surface-200 shadow-sm flex items-center gap-4">
            <div class="rounded-full bg-orange-50 p-3 text-orange-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-surface-500">Tahun Ajaran Aktif</p>
                <p class="text-lg font-bold text-surface-900">-</p>
            </div>
        </div>
    </div>

    <!-- Additional sections like recent activities could go here -->
    
</div>
@endsection
