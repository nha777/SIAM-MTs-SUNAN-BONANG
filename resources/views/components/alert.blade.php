@props(['type' => 'info', 'message' => null])

@php
    $colors = [
        'success' => 'bg-primary-50 text-primary-800 border-primary-200',
        'error' => 'bg-danger-50 text-danger-800 border-danger-200',
        'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
        'info' => 'bg-primary-50 text-primary-800 border-primary-200',
    ];
    $colorClass = $colors[$type] ?? $colors['info'];
@endphp

@if (!blank($message))
<div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-4 flex items-center justify-between rounded-lg border p-4 {{ $colorClass }}">
    <div class="flex items-center gap-3">
        @if ($type === 'success')
            <svg class="h-5 w-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        @elseif ($type === 'error')
            <svg class="h-5 w-5 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        @endif
        <span class="text-sm font-medium">{{ $message }}</span>
    </div>
    <button @click="show = false" class="text-current opacity-70 hover:opacity-100 focus:outline-none">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>
@endif
