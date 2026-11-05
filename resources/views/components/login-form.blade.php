{{-- Sign-in form shared by the login page and the account drawer. --}}
@props(['idPrefix' => ''])

<form x-data="authForm('login', { redirect: @js(route('account')) })" @submit.prevent="submit" novalidate
      method="POST" action="{{ route('login') }}" {{ $attributes->class('space-y-8') }}>
    @csrf
    <x-form-field name="email" label="Email address" type="email" autocomplete="email" :id-prefix="$idPrefix" />
    <x-form-field name="password" label="Password" type="password" autocomplete="current-password" :id-prefix="$idPrefix" />

    <div class="flex items-center justify-between gap-4 text-sm">
        <label class="flex cursor-pointer items-center gap-2.5">
            <input type="checkbox" x-model="form.remember" class="h-4 w-4 accent-ink">
            <span class="text-ink/70">Remember me</span>
        </label>
        <a href="{{ route('password.request') }}" class="link-line is-static text-ink">Forgot password?</a>
    </div>

    <button type="submit" class="btn btn-dark btn-lg w-full" :disabled="sending">
        <span x-text="sending ? 'Signing in…' : 'Sign in'">Sign in</span>
        <span class="btn-icon"><x-icon name="arrow-right" /></span>
    </button>
</form>
