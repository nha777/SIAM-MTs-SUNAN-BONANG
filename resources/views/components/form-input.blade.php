{{-- 
Component: Form Input Wrapper (Legacy/Convenience)
Description: A wrapper for form fields that includes labels and validation errors. 
             Internally uses x-form-group and x-input/x-select/x-textarea.
Props:
  - name: string
  - label: string
  - type: 'text', 'email', 'textarea', 'select', etc (default: 'text')
  - value: string (default: '')
  - required: boolean (default: false)
  - placeholder: string (default: '')
--}}
@props([
    'name', 
    'label', 
    'type' => 'text', 
    'value' => '', 
    'required' => false, 
    'placeholder' => ''
])

<x-form-group :name="$name" :label="$label" :required="$required">
    @if($type === 'textarea')
        <x-textarea 
            id="{{ $name }}" 
            name="{{ $name }}" 
            rows="3" 
            placeholder="{{ $placeholder }}"
            :error="$errors->has($name)"
            {{ $required ? 'required' : '' }}
        >{{ old($name, $value) }}</x-textarea>
    @elseif($type === 'select')
        <x-select 
            id="{{ $name }}" 
            name="{{ $name }}" 
            :error="$errors->has($name)"
            {{ $required ? 'required' : '' }}
        >
            {{ $slot }}
        </x-select>
    @else
        <x-input 
            type="{{ $type }}" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            :error="$errors->has($name)"
            {{ $required ? 'required' : '' }}
        />
    @endif
</x-form-group>
