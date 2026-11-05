{{--
    Floating-label field bound to an Alpine `form` / `errors` scope (see authForm in alpine.js).
    Password fields get a show/hide toggle driven by `showPassword`.
--}}
@props(['name', 'label', 'type' => 'text', 'autocomplete' => null, 'hint' => null, 'idPrefix' => ''])

@php
    $id = $idPrefix.$name;
    $password = $type === 'password';
@endphp

<div {{ $attributes }}>
    <div class="relative">
        <label class="field" for="{{ $id }}">
            <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" @if ($password) :type="showPassword ? 'text' : 'password'" @endif
                   x-model="form.{{ $name }}" placeholder=" " @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                   @class(['field-input peer', 'pr-12' => $password])
                   :aria-invalid="!!errors.{{ $name }}" aria-describedby="{{ $id }}-error">
            <span class="field-label">{{ $label }}</span>
        </label>

        @if ($password)
            <button type="button" @click="showPassword = !showPassword"
                    class="absolute right-0 bottom-1.5 flex h-9 w-9 items-center justify-center text-smoke transition hover:text-ink"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'">
                <x-icon name="eye" x-show="!showPassword" />
                <x-icon name="eye-off" x-show="showPassword" x-cloak />
            </button>
        @endif
    </div>

    <p id="{{ $id }}-error" class="field-error" x-show="errors.{{ $name }}" x-text="errors.{{ $name }}" x-cloak></p>
    @if ($hint)
        <p class="mt-2 text-xs text-smoke" x-show="!errors.{{ $name }}">{{ $hint }}</p>
    @endif
</div>
