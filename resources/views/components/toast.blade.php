{{-- 
Component: Toast
Description: A standardized toast notification. Supports Alpine.js auto-dismiss if used dynamically.
Props:
  - type: 'success', 'error', 'info', 'warning' (default: 'info')
  - message: string
  - show: boolean (default: true for static display)
  - timeout: number (milliseconds to auto-hide, default: 0 meaning no auto-hide)
--}}
@props([
    'type' => 'info',
    'message' => '',
    'show' => true,
    'timeout' => 0,
])

@php
$iconClass = match ($type) {
    'success' => 'text-primary-400',
    'error' => 'text-danger-400',
    'warning' => 'text-warning-400',
    default => 'text-surface-400',
};

$iconName = match ($type) {
    'success' => 'check-circle',
    'error' => 'exclamation-circle',
    'warning' => 'exclamation-triangle',
    default => 'information-circle',
};
@endphp

<div 
    x-data="{ 
        show: {{ $show ? 'true' : 'false' }},
        timeout: {{ $timeout }},
        init() {
            if (this.show && this.timeout > 0) {
                setTimeout(() => this.show = false, this.timeout);
            }
        }
    }"
    x-show="show"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    {{ $attributes->merge(['class' => 'max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden']) }}
>
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <x-dynamic-component :component="'heroicon-o-'.$iconName" class="h-6 w-6 {{ $iconClass }}" aria-hidden="true" />
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-surface-900">
                    {{ $message }}
                </p>
                @if(isset($description))
                <p class="mt-1 text-sm text-surface-500">
                    {{ $description }}
                </p>
                @endif
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button type="button" @click="show = false" class="bg-white rounded-md inline-flex text-surface-400 hover:text-surface-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <span class="sr-only">Close</span>
                    <x-heroicon-o-x-mark class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</div>
