@extends('layouts.app')

@section('title', 'Reset password')
@section('description', 'Choose a new password for your ARTFORM 3Di account.')
@section('header_theme', 'dark')
@section('header_solid', true)

@section('content')
<x-auth-shell image="assets/images/collections/bespoke.svg" caption="A fresh start, securely.">
    <div x-data="authForm('reset')">
        <div x-show="!done">
            <p class="eyebrow text-smoke">Account security</p>
            <h1 class="display mt-6 text-display-3" data-split data-split-immediate data-delay="0.2">Choose a new <em>password.</em></h1>
            <p class="mt-5 leading-relaxed text-ink/65">Make it something you haven’t used before.</p>

            <form @submit.prevent="submit" novalidate method="POST" action="{{ route('password.reset', $token) }}" class="mt-12 space-y-8">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <x-form-field name="password" label="New password" type="password" autocomplete="new-password" hint="At least 8 characters, with letters and numbers." />
                    <x-password-strength />
                </div>
                <x-form-field name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" />
                <button type="submit" class="btn btn-dark btn-lg w-full" :disabled="sending">
                    <span x-text="sending ? 'Updating…' : 'Update password'">Update password</span>
                    <span class="btn-icon"><x-icon name="key-round" /></span>
                </button>
            </form>
        </div>

        <div x-cloak x-show="done" x-transition.opacity.duration.600ms>
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-ink text-xl text-paper"><x-icon name="check" /></span>
            <h1 class="display mt-8 text-display-3">Password <em>updated.</em></h1>
            <p class="mt-5 leading-relaxed text-ink/65">You can now sign in with your new password.</p>
            <x-button :href="route('login')" variant="dark" size="lg" icon="arrow-right" class="mt-10 w-full">Sign in</x-button>
        </div>

        <x-preview-note class="mt-10" />
    </div>
</x-auth-shell>
@endsection
