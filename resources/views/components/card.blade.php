{{-- 
Component: Card
Description: A container for grouping related content.
Props:
  - noPadding: boolean (default: false, removes padding from body)
  - title: string (optional)
  - subtitle: string (optional)
--}}

@props([
    'noPadding' => false,
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm rounded-lg border border-surface-200']) }}>
    @if(isset($header))
        <div class="px-4 py-4 sm:px-6 border-b border-surface-200 bg-surface-50">
            {{ $header }}
        </div>
    @elseif($title || $subtitle)
        <div class="px-4 py-4 sm:px-6 border-b border-surface-200 bg-surface-50">
            @if($title)
                <h3 class="text-lg leading-6 font-medium text-surface-900">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="mt-1 max-w-2xl text-sm text-surface-500">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="{{ $noPadding ? '' : 'px-4 py-5 sm:p-6' }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-4 py-4 sm:px-6 bg-surface-50 border-t border-surface-200 flex items-center justify-end gap-3">
            {{ $footer }}
        </div>
    @endif
</div>
