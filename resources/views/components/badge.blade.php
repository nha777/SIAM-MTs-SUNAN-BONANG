{{-- 
Component: Badge
Description: A small label for statuses or tags.
Props:
  - variant: 'primary', 'success', 'warning', 'danger', 'neutral' (default: 'neutral')
--}}

@props([
    'variant' => 'neutral',
])

@php
    $baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    
    $variantClasses = match($variant) {
        'primary', 'success' => 'bg-primary-100 text-primary-800',
        'warning' => 'bg-warning-100 text-warning-800',
        'danger' => 'bg-danger-100 text-danger-800',
        'neutral' => 'bg-surface-100 text-surface-800',
        default => 'bg-surface-100 text-surface-800',
    };
@endphp

<span {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses}"]) }}>
    {{ $slot }}
</span>
