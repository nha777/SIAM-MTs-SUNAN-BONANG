{{-- 
Component: Input
Description: A standardized text input field.
Props:
  - type: 'text', 'email', 'password', 'number' (default: 'text')
  - disabled: boolean (default: false)
  - error: boolean (default: false)
--}}

@props([
    'type' => 'text',
    'disabled' => false,
    'error' => false,
])

@php
    $baseClasses = 'block w-full rounded-md shadow-sm sm:text-sm px-3 py-2 border focus:outline-none transition-colors duration-100';
    
    $stateClasses = $error 
        ? 'border-danger-300 text-danger-900 placeholder-danger-300 focus:border-danger-500 focus:ring-danger-500' 
        : 'border-surface-300 placeholder-surface-400 focus:border-primary-500 focus:ring-primary-500';

    if ($disabled) {
        $stateClasses .= ' bg-surface-50 text-surface-500 cursor-not-allowed opacity-50';
    }
@endphp

<input 
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => "{$baseClasses} {$stateClasses}"]) }}
>
