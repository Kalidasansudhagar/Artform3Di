{{-- Admin form field: label, input (or textarea) and validation error. --}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])

@php $id = $attributes->get('id', $name); @endphp

<div>
    <label for="{{ $id }}" class="text-sm font-medium">{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $attributes->get('rows', 4) }}" {{ $attributes->except(['id', 'rows'])->class(['admin-input mt-2']) }} @error($name) aria-invalid="true" @enderror>{{ $value }}</textarea>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}" {{ $attributes->except('id')->class(['admin-input mt-2']) }} @error($name) aria-invalid="true" @enderror>
    @endif
    @if ($hint)<p class="mt-1.5 text-xs text-smoke">{{ $hint }}</p>@endif
    @error($name)<p class="mt-2 text-xs text-[#b5432f]">{{ $message }}</p>@enderror
</div>
