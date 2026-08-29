@props([
    'active' => false,
    'href' => '#',
    'icon' => null,
])

@php
    $baseClasses = 'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors';
    $stateClasses = $active 
        ? 'bg-primary-50 text-primary-700' 
        : 'text-surface-700 hover:bg-surface-100 hover:text-surface-900';
    $paddingClasses = $icon ? '' : 'pl-11';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "{$baseClasses} {$stateClasses} {$paddingClasses}"]) }}>
    @if($icon)
        {{ $icon }}
    @endif
    {{ $slot }}
</a>
