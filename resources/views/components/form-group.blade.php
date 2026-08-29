{{-- 
Component: Form Group
Description: A wrapper for form fields that includes labels, help text, and validation errors.
Props:
  - name: string
  - label: string
  - required: boolean (default: false)
  - helpText: string (optional)
  - error: boolean/string (optional)
--}}
@props([
    'name', 
    'label', 
    'required' => false, 
    'helpText' => null,
])

@php
    $hasError = $errors->has($name);
    $errorMessage = $errors->first($name);
@endphp

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-surface-700 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-danger-500" aria-hidden="true">*</span>
        @endif
    </label>
    
    {{ $slot }}
    
    @if($helpText && !$hasError)
        <p class="mt-1 text-sm text-surface-500" id="{{ $name }}-description">{{ $helpText }}</p>
    @endif
    
    @if($hasError)
        <p class="mt-1 text-sm text-danger-600" id="{{ $name }}-error">{{ $errorMessage }}</p>
    @endif
</div>
