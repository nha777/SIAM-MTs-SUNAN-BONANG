{{-- 
Component: Modal
Description: A standardized modal dialog.
Props:
  - name: string (Unique identifier for Alpine x-data)
  - title: string
  - maxWidth: 'sm', 'md', 'lg', 'xl', '2xl' (default: '2xl')
--}}

@props([
    'name',
    'title',
    'maxWidth' => '2xl'
])

@php
$maxWidthClass = match ($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    default => 'sm:max-w-2xl',
};
@endphp

<div
    x-data="{ show: false }"
    x-show="show"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Backdrop -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-surface-900/50 transition-opacity" 
        @click="show = false"
    ></div>

    <!-- Modal Panel -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative flex min-h-full items-center justify-center p-4 sm:p-0"
    >
        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 w-full {{ $maxWidthClass }}">
            <!-- Header -->
            <div class="bg-surface-50 px-4 py-3 sm:px-6 border-b border-surface-200 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-surface-900" id="modal-title">
                    {{ $title }}
                </h3>
                <button @click="show = false" class="text-surface-400 hover:text-surface-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <x-heroicon-o-x-mark class="h-6 w-6" />
                        
                    
                </button>
            </div>
            
            <!-- Body -->
            <div class="px-4 py-5 sm:p-6">
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            @if(isset($footer))
            <div class="bg-surface-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-surface-200 gap-2">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>
