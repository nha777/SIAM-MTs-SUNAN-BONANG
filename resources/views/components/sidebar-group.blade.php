@props([
    'title',
    'active' => false,
])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="pt-4">
    <button @click="open = !open" class="flex w-full items-center justify-between px-3 py-2 text-sm font-medium text-surface-500 hover:text-surface-900 transition-colors">
        <span class="uppercase tracking-wider text-xs font-semibold">{{ $title }}</span>
        <svg :class="{'rotate-180': open}" class="h-4 w-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <div x-show="open" class="mt-1 space-y-1" x-collapse>
        {{ $slot }}
    </div>
</div>
