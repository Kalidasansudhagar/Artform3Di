@extends('layouts.app')

@section('title', 'Checkout')
@section('description', 'Place your ARTFORM 3Di order.')
@section('header_theme', 'light')

@php
    $fields = ['full_name', 'phone', 'line1', 'line2', 'city', 'state', 'postcode'];
    $form = collect($fields)->mapWithKeys(fn ($f) => [$f => old($f, $address[$f] ?? '')])->all();
    $errorList = collect($errors->toArray())->map(fn ($m) => $m[0])->all();
@endphp

@section('content')
<section class="bg-paper pt-[calc(var(--header-h)+4rem)] pb-24 lg:pb-32">
    <div class="container-x">
        <div class="border-b border-ink/10 pb-12">
            <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-sm text-ink/60 transition hover:text-ink"><x-icon name="arrow-left" /> Back to bag</a>
            <h1 class="display mt-6 text-display-2">Check<em>out.</em></h1>
        </div>

        <form method="POST" action="{{ route('checkout.store') }}" novalidate class="mt-12 grid gap-12 lg:grid-cols-12 lg:gap-16"
              x-data="{ form: @js($form), errors: @js($errorList), showPassword: false, payment: @js(old('payment_method', 'cod')), sending: false }"
              @submit="sending = true">
            @csrf

            <div class="space-y-14 lg:col-span-7">
                <fieldset>
                    <legend class="display text-3xl">Delivery address</legend>
                    <div class="mt-8 grid gap-x-10 gap-y-8 sm:grid-cols-2">
                        <x-form-field name="full_name" label="Full name" autocomplete="name" />
                        <x-form-field name="phone" label="Phone" type="tel" autocomplete="tel" />
                        <x-form-field name="line1" label="Address line 1" autocomplete="address-line1" class="sm:col-span-2" />
                        <x-form-field name="line2" label="Address line 2 (optional)" autocomplete="address-line2" class="sm:col-span-2" />
                        <x-form-field name="city" label="City" autocomplete="address-level2" />
                        <x-form-field name="state" label="State" autocomplete="address-level1" />
                        <x-form-field name="postcode" label="PIN code" autocomplete="postal-code" />
                    </div>
                    <p class="mt-6 text-xs text-smoke">Order updates go to {{ auth()->user()->email }}. This address is saved to your account for next time.</p>
                </fieldset>

                <fieldset>
                    <legend class="display text-3xl">Payment</legend>
                    <p class="mt-3 text-sm leading-relaxed text-ink/60">No payment is taken now. We confirm every order by phone or email first — including finish and delivery — then you pay.</p>
                    <div class="mt-6 grid gap-3">
                        @foreach (\App\Models\Order::PAYMENT_METHODS as $key => $label)
                            <label class="flex cursor-pointer items-center gap-4 p-5 ring-1 transition" :class="payment === '{{ $key }}' ? 'ring-ink' : 'ring-ink/15 hover:ring-ink/40'">
                                <input type="radio" name="payment_method" value="{{ $key }}" x-model="payment" class="h-4 w-4 accent-ink">
                                <span class="text-sm font-medium">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method')<p class="field-error">{{ $message }}</p>@enderror
                </fieldset>

                <div>
                    <label class="field" for="notes">
                        <textarea id="notes" name="notes" rows="3" placeholder=" " class="field-input peer resize-none">{{ old('notes') }}</textarea>
                        <span class="field-label">Order notes — finish, colour or delivery instructions (optional)</span>
                    </label>
                </div>
            </div>

            <aside class="lg:col-span-5">
                <div class="bg-bone p-6 sm:p-8 lg:sticky lg:top-[calc(var(--header-h)+2rem)]">
                    <h2 class="display text-3xl">Your order</h2>
                    <ul class="mt-6 divide-y divide-ink/10 border-y border-ink/10">
                        @foreach ($items as $line)
                            <li class="flex items-center gap-4 py-4">
                                <span class="media-frame relative block aspect-[4/5] w-14 shrink-0">
                                    <img src="{{ asset($line['product']['image']) }}" alt="" width="1200" height="1500" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold">{{ $line['product']['name'] }}</p>
                                    <p class="text-xs text-ink/60">Qty {{ $line['quantity'] }} × {{ $line['product']['price_label'] }}</p>
                                </div>
                                <p class="text-sm tabular-nums">{{ \App\Models\Order::money($line['line_total']) }}</p>
                            </li>
                        @endforeach
                    </ul>
                    <dl class="mt-2 text-sm">
                        <div class="flex justify-between gap-6 py-3"><dt class="text-ink/60">Subtotal</dt><dd class="tabular-nums">{{ \App\Models\Order::money($subtotal) }}</dd></div>
                        <div class="flex justify-between gap-6 py-3"><dt class="text-ink/60">Delivery</dt><dd class="text-right">Confirmed with your order</dd></div>
                    </dl>
                    <div class="mt-3 flex items-baseline justify-between gap-6 border-t border-ink/10 pt-5">
                        <p class="font-semibold">Total</p>
                        <p class="display text-3xl tabular-nums">{{ \App\Models\Order::money($subtotal) }}</p>
                    </div>
                    <button type="submit" class="btn btn-dark btn-lg mt-8 w-full" :disabled="sending">
                        <span x-text="sending ? 'Placing order…' : 'Place order'">Place order</span>
                        <span class="btn-icon"><x-icon name="arrow-right" /></span>
                    </button>
                </div>
            </aside>
        </form>
    </div>
</section>
@endsection
