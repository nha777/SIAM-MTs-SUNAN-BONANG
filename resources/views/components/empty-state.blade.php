{{-- 
Component: Empty State
Description: A placeholder when no data is available, for search results, or errors.
Props:
  - type: 'empty', 'search', 'error' (default: 'empty')
  - title: string
  - description: string
  - icon: string (optional override)
--}}

@props([
    'type' => 'empty',
    'title' => 'No data available',
    'description' => '',
    'icon' => null,
])

@php
    $defaultIcon = match($type) {
        'search' => 'search',
        'error' => 'exclamation-circle',
        default => 'document-text',
    };
    
    $iconName = $icon ?? $defaultIcon;
@endphp

<div {{ $attributes->merge(['class' => 'text-center py-12 px-4 bg-surface-50 rounded-lg border-2 border-dashed border-surface-300']) }}>
    <x-dynamic-component :component="'heroicon-o-'.$iconName" class="mx-auto h-12 w-12 text-surface-400" aria-hidden="true" />
    
    <h3 class="mt-4 text-sm font-medium text-surface-900">{{ $title }}</h3>
    
    @if($description)
        <p class="mt-1 text-sm text-surface-500 max-w-sm mx-auto">{{ $description }}</p>
    @endif
    
    @if(isset($action))
        <div class="mt-6 flex justify-center">
            {{ $action }}
        </div>
    @endif
</div>
