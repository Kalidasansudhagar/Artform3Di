@extends('layouts.app')

@section('title', 'Sign in')
@section('description', 'Sign in to your ARTFORM 3Di account.')
@section('header_theme', 'dark')
@section('header_solid', true)

@section('content')
<x-auth-shell image="assets/images/collections/divine.svg" caption="Every piece begins with a conversation.">
    <p class="eyebrow text-smoke" data-reveal>My Account</p>
    <h1 class="display mt-6 text-display-3" data-split data-split-immediate data-delay="0.2">Welcome <em>back.</em></h1>
    <p class="mt-5 leading-relaxed text-ink/65" data-reveal data-delay="0.4">Sign in to view your orders, saved pieces and commissions.</p>

    <x-login-form class="mt-12" data-reveal data-delay="0.5" />

    <div class="mt-10 border-t border-ink/10 pt-8 text-sm text-ink/70" data-reveal data-delay="0.6">
        New to ARTFORM 3Di?
        <a href="{{ route('register') }}" class="link-line is-static ml-1 font-semibold text-ink">Create an account</a>
    </div>

    <x-preview-note class="mt-10" />
</x-auth-shell>
@endsection
