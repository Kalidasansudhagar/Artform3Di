@extends('layouts.admin')

@section('title', 'My profile')

@section('content')
<x-admin.page-header title="My profile" description="Your name, sign-in email and password." />

<form method="POST" action="{{ route('admin.profile.update') }}" class="mt-8 grid gap-4 xl:grid-cols-12">
    @csrf
    @method('PUT')

    <section class="admin-card space-y-5 xl:col-span-6">
        <h2 class="font-semibold">Account</h2>
        <x-admin.input name="name" label="Name" :value="old('name', $user->name)" required autocomplete="name" />
        <x-admin.input name="email" label="Sign-in email" type="email" :value="old('email', $user->email)" required autocomplete="email" />
    </section>

    <section class="admin-card space-y-5 xl:col-span-6">
        <div>
            <h2 class="font-semibold">Change password</h2>
            <p class="mt-1 text-sm text-smoke">Leave empty to keep your current password. At least 10 characters, with letters and numbers.</p>
        </div>
        <x-admin.input name="current_password" label="Current password" type="password" autocomplete="current-password" />
        <x-admin.input name="password" label="New password" type="password" autocomplete="new-password" />
        <x-admin.input name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" />
    </section>

    <div class="xl:col-span-12">
        <button type="submit" class="btn btn-dark btn-lg"><span>Save profile</span><span class="btn-icon"><x-icon name="check" /></span></button>
    </div>
</form>
@endsection
