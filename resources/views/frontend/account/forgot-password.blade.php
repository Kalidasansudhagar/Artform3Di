@extends('layouts.app')

@section('title', 'Forgot password')
@section('description', 'Reset your ARTFORM 3Di account password.')
@section('header_theme', 'dark')
@section('header_solid', true)

@section('content')
<x-auth-shell image="assets/images/collections/bespoke.svg" caption="We’ll help you get back in.">
    <div x-data="authForm('forgot')">
        <div x-show="!done">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-ink/60 transition hover:text-ink"><x-icon name="arrow-left" /> Back to sign in</a>
            <h1 class="display mt-10 text-display-3" data-split data-split-immediate data-delay="0.2">Forgot your <em>password?</em></h1>
            <p class="mt-5 leading-relaxed text-ink/65">Enter the email linked to your account and we’ll send you a link to choose a new password.</p>

            <form @submit.prevent="submit" novalidate method="POST" action="{{ route('password.request') }}" class="mt-12 space-y-8">
                @csrf
                <x-form-field name="email" label="Email address" type="email" autocomplete="email" />
                <button type="submit" class="btn btn-dark btn-lg w-full" :disabled="sending">
                    <span x-text="sending ? 'Sending…' : 'Send reset link'">Send reset link</span>
                    <span class="btn-icon"><x-icon name="mail" /></span>
                </button>
            </form>
        </div>

        <div x-cloak x-show="done" x-transition.opacity.duration.600ms>
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-ink text-xl text-paper"><x-icon name="mail-check" /></span>
            <h1 class="display mt-8 text-display-3">Check your <em>inbox.</em></h1>
            <p class="mt-5 leading-relaxed text-ink/65">If an account exists for <span class="font-semibold text-ink" x-text="form.email"></span>, a password reset link is on its way. It may take a few minutes to arrive.</p>
            <div class="mt-10 flex flex-col gap-3">
                <x-button :href="route('password.reset', 'preview')" variant="dark" size="lg" icon="key-round">Open reset link (preview)</x-button>
                <x-button :href="route('login')" variant="outline-dark" size="lg" icon="arrow-left">Back to sign in</x-button>
            </div>
            <p class="mt-6 text-xs text-smoke">Didn’t get it? <button type="button" class="link-line is-static text-ink" @click="done = false">Try another email</button></p>
        </div>

        <x-preview-note class="mt-10">Preview — no email is sent yet. Use “Open reset link” to see the next step.</x-preview-note>
    </div>
</x-auth-shell>
@endsection
