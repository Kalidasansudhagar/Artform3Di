@extends('layouts.app')

@section('title', 'My Account')
@section('description', 'Your ARTFORM 3Di account — orders, wishlist, addresses and details.')
@section('header_theme', 'light')

{{-- Preview dashboard: every panel shows its empty state until accounts and orders are connected. --}}

@php
    $tabs = [
        'overview' => ['Overview', 'layout-dashboard'],
        'orders' => ['Orders', 'package'],
        'wishlist' => ['Wishlist', 'heart'],
        'addresses' => ['Addresses', 'map-pin'],
        'details' => ['Account details', 'user-round'],
    ];
@endphp

@section('content')
<section class="bg-paper pt-[calc(var(--header-h)+4rem)] pb-24 lg:pb-32"
         x-data="{ tab: 'overview', open(t) { this.tab = t; history.replaceState(null, '', '#' + t); } }"
         x-init="const h = location.hash.slice(1); if (@js(array_keys($tabs)).includes(h)) tab = h">
    <div class="container-x">
        <div class="flex flex-col gap-8 border-b border-ink/10 pb-12 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="eyebrow text-smoke" data-reveal>My Account</p>
                <h1 class="display mt-6 text-display-2" data-split data-split-immediate data-delay="0.2">Welcome <em>back.</em></h1>
            </div>
            <x-preview-note class="max-w-md" data-reveal data-delay="0.4" />
        </div>

        <div class="mt-12 grid gap-12 lg:grid-cols-12 lg:gap-16">
            {{-- Navigation --}}
            <nav class="lg:col-span-3" aria-label="Account">
                <ul class="-mx-5 flex gap-1 overflow-x-auto px-5 [scrollbar-width:none] sm:-mx-8 sm:px-8 lg:mx-0 lg:flex-col lg:px-0 lg:sticky lg:top-[calc(var(--header-h)+2rem)]" role="tablist">
                    @foreach ($tabs as $key => [$label, $icon])
                        <li class="shrink-0">
                            <button type="button" role="tab" @click="open('{{ $key }}')" :aria-selected="tab === '{{ $key }}'"
                                    :class="tab === '{{ $key }}' ? 'bg-ink text-paper' : 'text-ink/70 hover:bg-ink/5 hover:text-ink'"
                                    class="flex w-full items-center gap-3 rounded-full px-5 py-3 text-sm font-medium whitespace-nowrap transition lg:rounded-none lg:py-4">
                                <x-icon :name="$icon" /> {{ $label }}
                            </button>
                        </li>
                    @endforeach
                    <li class="shrink-0 lg:mt-4 lg:border-t lg:border-ink/10 lg:pt-4">
                        <a href="{{ route('login') }}" class="flex items-center gap-3 rounded-full px-5 py-3 text-sm font-medium whitespace-nowrap text-ink/70 transition hover:text-[#a5503f] lg:py-4">
                            <x-icon name="log-out" /> Sign out
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="min-w-0 lg:col-span-9">
                {{-- Overview --}}
                <div x-show="tab === 'overview'" role="tabpanel">
                    <div class="grid gap-px bg-ink/10 sm:grid-cols-3">
                        @foreach ([['Orders', '0', 'package', 'orders'], ['Saved pieces', null, 'heart', 'wishlist'], ['Addresses', '0', 'map-pin', 'addresses']] as [$label, $value, $icon, $target])
                            <button type="button" @click="open('{{ $target }}')" class="group bg-paper p-7 text-left transition hover:bg-bone">
                                <x-icon :name="$icon" class="text-xl text-brass" />
                                <p class="display mt-6 text-5xl tabular-nums" @if (! $value) x-text="$store.ui.wishlist.length" @endif>{{ $value ?? '0' }}</p>
                                <p class="mt-2 flex items-center justify-between text-sm text-ink/60">{{ $label }} <x-icon name="arrow-right" class="transition group-hover:translate-x-1" /></p>
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-16">
                        <div class="flex items-end justify-between gap-6">
                            <h2 class="display text-3xl">Recent orders</h2>
                            <button type="button" @click="open('orders')" class="link-line text-xs font-semibold tracking-[0.14em] uppercase">View all</button>
                        </div>
                        <div class="mt-6 border border-dashed border-ink/15 px-6 py-10 text-center text-sm text-ink/60">You haven’t placed an order yet.</div>
                    </div>

                    <div class="mt-16">
                        <h2 class="display text-3xl">You may like</h2>
                        <div class="mt-8 grid gap-x-6 gap-y-12 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($suggestions as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Orders --}}
                <div x-cloak x-show="tab === 'orders'" role="tabpanel">
                    <h2 class="display text-4xl">Orders</h2>
                    <div class="mt-8 flex flex-col items-center border border-dashed border-ink/15 px-6 py-20 text-center">
                        <x-icon name="package" class="text-3xl text-brass" />
                        <p class="display mt-6 text-3xl">No orders yet</p>
                        <p class="mt-3 max-w-sm text-sm leading-relaxed text-ink/60">When you order a piece or commission a project, you’ll be able to follow it here — from printing to delivery.</p>
                        <x-button :href="route('shop.index')" variant="dark" class="mt-8">Explore the collection</x-button>
                    </div>
                </div>

                {{-- Wishlist --}}
                <div x-cloak x-show="tab === 'wishlist'" role="tabpanel">
                    <h2 class="display text-4xl">Wishlist</h2>
                    <div class="mt-8 flex flex-col items-center border border-dashed border-ink/15 px-6 py-20 text-center">
                        <x-icon name="heart" class="text-3xl text-brass" />
                        <p class="display mt-6 text-3xl">Your wishlist is empty</p>
                        <p class="mt-3 max-w-sm text-sm leading-relaxed text-ink/60">Tap the heart on any piece to save it here for later.</p>
                        <x-button :href="route('shop.index')" variant="dark" class="mt-8">Browse pieces</x-button>
                    </div>
                </div>

                {{-- Addresses --}}
                <div x-cloak x-show="tab === 'addresses'" role="tabpanel" x-data="{ adding: false }">
                    <div class="flex items-end justify-between gap-6">
                        <h2 class="display text-4xl">Addresses</h2>
                        <x-button variant="outline-dark" size="sm" icon="plus" x-show="!adding" @click="adding = true">Add address</x-button>
                    </div>

                    <div x-show="!adding" class="mt-8 flex flex-col items-center border border-dashed border-ink/15 px-6 py-20 text-center">
                        <x-icon name="map-pin" class="text-3xl text-brass" />
                        <p class="display mt-6 text-3xl">No saved addresses</p>
                        <p class="mt-3 max-w-sm text-sm leading-relaxed text-ink/60">Save a delivery address to make checkout quicker.</p>
                    </div>

                    <form x-cloak x-show="adding" x-collapse @submit.prevent="adding = false; $store.ui.notify('Address saving will be available when accounts launch.')"
                          x-data="{ form: { full_name: '', phone: '', line1: '', line2: '', city: '', state: '', postcode: '' }, errors: {}, showPassword: false }"
                          class="mt-8 grid gap-x-10 gap-y-8 border border-ink/10 p-6 sm:grid-cols-2 sm:p-10" novalidate>
                        <x-form-field name="full_name" label="Full name" autocomplete="name" id-prefix="addr-" />
                        <x-form-field name="phone" label="Phone" type="tel" autocomplete="tel" id-prefix="addr-" />
                        <x-form-field name="line1" label="Address line 1" autocomplete="address-line1" id-prefix="addr-" class="sm:col-span-2" />
                        <x-form-field name="line2" label="Address line 2 (optional)" autocomplete="address-line2" id-prefix="addr-" class="sm:col-span-2" />
                        <x-form-field name="city" label="City" autocomplete="address-level2" id-prefix="addr-" />
                        <x-form-field name="state" label="State" autocomplete="address-level1" id-prefix="addr-" />
                        <x-form-field name="postcode" label="PIN code" autocomplete="postal-code" id-prefix="addr-" />
                        <div class="flex items-end gap-3 sm:col-span-2">
                            <x-button type="submit" variant="dark" icon="check">Save address</x-button>
                            <button type="button" class="link-line text-xs font-semibold tracking-[0.14em] uppercase" @click="adding = false">Cancel</button>
                        </div>
                    </form>
                </div>

                {{-- Account details --}}
                <div x-cloak x-show="tab === 'details'" role="tabpanel">
                    <h2 class="display text-4xl">Account details</h2>

                    <form x-data="{ form: { name: '', email: '', phone: '' }, errors: {}, showPassword: false }" novalidate
                          @submit.prevent="$store.ui.notify('Profile updates will be available when accounts launch.')"
                          class="mt-8 grid gap-x-10 gap-y-8 border border-ink/10 p-6 sm:grid-cols-2 sm:p-10">
                        <x-form-field name="name" label="Full name" autocomplete="name" id-prefix="profile-" class="sm:col-span-2" />
                        <x-form-field name="email" label="Email address" type="email" autocomplete="email" id-prefix="profile-" />
                        <x-form-field name="phone" label="Phone" type="tel" autocomplete="tel" id-prefix="profile-" />
                        <div class="sm:col-span-2"><x-button type="submit" variant="dark" icon="check">Save changes</x-button></div>
                    </form>

                    <h3 class="display mt-16 text-3xl">Change password</h3>
                    <div x-data="authForm('reset')" class="mt-8 border border-ink/10 p-6 sm:p-10">
                        <form x-show="!done" @submit.prevent="submit" novalidate class="grid gap-x-10 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-2 sm:max-w-sm">
                                <label class="field" for="current-password">
                                    <input id="current-password" type="password" :type="showPassword ? 'text' : 'password'" placeholder=" " autocomplete="current-password" class="field-input peer">
                                    <span class="field-label">Current password</span>
                                </label>
                            </div>
                            <div>
                                <x-form-field name="password" label="New password" type="password" autocomplete="new-password" id-prefix="change-" />
                                <x-password-strength />
                            </div>
                            <x-form-field name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" id-prefix="change-" />
                            <div class="sm:col-span-2">
                                <button type="submit" class="btn btn-dark" :disabled="sending">
                                    <span x-text="sending ? 'Updating…' : 'Update password'">Update password</span>
                                    <span class="btn-icon"><x-icon name="key-round" /></span>
                                </button>
                            </div>
                        </form>
                        <p x-cloak x-show="done" class="flex items-center gap-3 text-sm"><x-icon name="check" class="text-[#5f7a55]" /> Password change previewed — this will update your account once sign-in is connected.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
