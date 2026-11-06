@extends('layouts.app')

@section('title', 'Create account')
@section('description', 'Create your ARTFORM 3Di account.')
@section('header_theme', 'dark')
@section('header_solid', true)

@section('content')
<x-auth-shell image="assets/images/products/helix-form-iii-1.svg" caption="Save the pieces you love. Follow every commission.">
    <p class="eyebrow text-smoke" data-reveal>Join ARTFORM 3Di</p>
    <h1 class="display mt-6 text-display-3" data-split data-split-immediate data-delay="0.2">Create your <em>account.</em></h1>
    <p class="mt-5 leading-relaxed text-ink/65" data-reveal data-delay="0.4">One account for orders, wishlists and custom projects.</p>

    <form x-data="authForm('register')" @submit.prevent="submit" novalidate
          method="POST" action="{{ route('register.store') }}" class="mt-12 space-y-8" data-reveal data-delay="0.5">
        @csrf
        <x-form-field name="name" label="Full name" autocomplete="name" />
        <div class="grid gap-8 sm:grid-cols-2">
            <x-form-field name="email" label="Email address" type="email" autocomplete="email" />
            <x-form-field name="phone" label="Phone (optional)" type="tel" autocomplete="tel" />
        </div>
        <div>
            <x-form-field name="password" label="Password" type="password" autocomplete="new-password" hint="At least 8 characters, with letters and numbers." />
            <x-password-strength />
        </div>
        <x-form-field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" />

        <div>
            <label class="flex cursor-pointer items-start gap-3 text-sm leading-relaxed text-ink/70">
                <input type="checkbox" x-model="form.terms" class="mt-1 h-4 w-4 shrink-0 accent-ink" :aria-invalid="!!errors.terms" aria-describedby="terms-error">
                <span>I agree to the Terms of Service and Privacy Policy, and to receive updates about my orders.</span>
            </label>
            <p id="terms-error" class="field-error" x-show="errors.terms" x-text="errors.terms" x-cloak></p>
        </div>

        <button type="submit" class="btn btn-dark btn-lg w-full" :disabled="sending">
            <span x-text="sending ? 'Creating account…' : 'Create account'">Create account</span>
            <span class="btn-icon"><x-icon name="arrow-right" /></span>
        </button>
    </form>

    <div class="mt-10 border-t border-ink/10 pt-8 text-sm text-ink/70">
        Already have an account?
        <a href="{{ route('login') }}" class="link-line is-static ml-1 font-semibold text-ink">Sign in</a>
    </div>

</x-auth-shell>
@endsection
