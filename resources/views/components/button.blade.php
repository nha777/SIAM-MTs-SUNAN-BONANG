{{-- 
Component: Button
Description: A standardized button component for user actions.
Props:
  - variant: 'primary', 'secondary', 'danger', 'ghost' (default: 'primary')
  - size: 'sm', 'md', 'lg', 'icon' (default: 'md')
  - disabled: boolean (default: false)
  - icon: string (optional SVG icon name, e.g., 'plus')
  - loading: boolean (default: false)
  - type: 'button', 'submit', 'reset' (default: 'button')
--}}

@props([
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'icon' => null,
    'loading' => false,
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200';
    
    $variantClasses = match($variant) {
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 border border-transparent',
        'secondary' => 'bg-white text-surface-700 hover:bg-surface-50 focus:ring-primary-500 border border-surface-300 shadow-sm',
        'danger' => 'bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-500 border border-transparent',
        'ghost' => 'bg-transparent text-surface-600 hover:text-surface-900 hover:bg-surface-100 focus:ring-surface-300 border border-transparent',
        default => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
    };

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
        'icon' => 'p-2', // Icon only size
        default => 'px-4 py-2 text-sm',
    };

    if ($disabled || $loading) {
        $variantClasses .= ' opacity-50 cursor-not-allowed';
    }
    
    $hasSlot = $slot->isNotEmpty();
    $iconClasses = 'h-5 w-5 flex-shrink-0';
    if ($hasSlot) {
        $iconClasses .= ' mr-2 -ml-1';
    }
@endphp

<button 
    type="{{ $type }}"
    {{ $disabled || $loading ? 'disabled aria-disabled="true"' : '' }}
    {!! $loading ? 'aria-busy="true"' : '' !!}
    {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses} {$sizeClasses}"]) }}
>
    @if($loading)
        <svg class="animate-spin {{ $hasSlot ? 'mr-2 -ml-1' : '' }} h-5 w-5 text-current flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @elseif($icon)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="{{ $iconClasses }}" aria-hidden="true" />
    @endif
    
    {{ $slot }}
</button>
