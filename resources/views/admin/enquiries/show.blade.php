@extends('layouts.admin')

@section('title', 'Enquiry from '.$enquiry->name)

@php
    $phoneDigits = \App\Support\Contact::phoneDigits($enquiry->phone ?? '');
    $whatsapp = $enquiry->phone ? \App\Support\Contact::whatsappUrl($enquiry->phone) : null;
@endphp

@section('content')
<a href="{{ route('admin.enquiries.index') }}" class="inline-flex items-center gap-2 text-sm text-ink/60 hover:text-ink"><x-icon name="arrow-left" /> All enquiries</a>

<div class="mt-6 grid gap-4 xl:grid-cols-12">
    <section class="admin-card xl:col-span-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="mono text-smoke">{{ $enquiry->subject }}</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight">{{ $enquiry->name }}</h2>
                <p class="mt-1 text-sm text-smoke">Received {{ $enquiry->created_at->format('j M Y, g:i a') }} · {{ $enquiry->created_at->diffForHumans() }}</p>
            </div>
            <x-admin.status-badge :tone="$enquiry->status_tone" :label="$enquiry->status_label" />
        </div>

        <div class="mt-8 rounded-xl bg-[#faf8f4] p-5 text-[15px] leading-relaxed whitespace-pre-line">{{ $enquiry->message }}</div>

        <dl class="mt-8 grid gap-6 sm:grid-cols-3">
            <div>
                <dt class="text-xs text-smoke">Email</dt>
                <dd class="mt-1 text-sm break-all"><a href="mailto:{{ $enquiry->email }}" class="hover:underline">{{ $enquiry->email }}</a></dd>
            </div>
            <div>
                <dt class="text-xs text-smoke">Phone</dt>
                <dd class="mt-1 text-sm">{!! $enquiry->phone ? '<a href="tel:+'.e($phoneDigits).'" class="hover:underline">'.e($enquiry->phone).'</a>' : '<span class="text-smoke">Not given</span>' !!}</dd>
            </div>
            <div>
                <dt class="text-xs text-smoke">Service</dt>
                <dd class="mt-1 text-sm">{{ $enquiry->subject }}</dd>
            </div>
        </dl>

        <div class="mt-8 flex flex-wrap gap-2 border-t border-ink/5 pt-6">
            <x-button href="mailto:{{ $enquiry->email }}?subject={{ rawurlencode('Re: your '.$enquiry->subject.' enquiry — Artform 3Di') }}" variant="dark" size="sm" icon="mail">Reply by email</x-button>
            @if ($whatsapp)
                <x-button :href="$whatsapp" target="_blank" rel="noopener" variant="outline-dark" size="sm" icon="send">WhatsApp</x-button>
                <x-button href="tel:+{{ $phoneDigits }}" variant="outline-dark" size="sm" icon="phone">Call</x-button>
            @endif
        </div>
    </section>

    <aside class="space-y-4 xl:col-span-4">
        <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}" class="admin-card space-y-5">
            @csrf
            @method('PUT')
            <h3 class="font-semibold">Manage</h3>
            <div>
                <label for="status" class="text-sm font-medium">Status</label>
                <select id="status" name="status" class="admin-input mt-2">
                    @foreach (\App\Models\Enquiry::STATUSES as $key => [$label])
                        <option value="{{ $key }}" @selected(old('status', $enquiry->status) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <x-admin.input name="admin_notes" label="Internal notes" type="textarea" rows="6" :value="old('admin_notes', $enquiry->admin_notes)" hint="Only visible to admins." />
            <button type="submit" class="btn btn-dark w-full"><span>Save changes</span><span class="btn-icon"><x-icon name="check" /></span></button>
        </form>

        <form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry) }}" class="admin-card" x-data @submit="if (!confirm('Delete this enquiry permanently?')) $event.preventDefault()">
            @csrf
            @method('DELETE')
            <h3 class="font-semibold">Delete enquiry</h3>
            <p class="mt-1 text-sm text-smoke">This removes it permanently.</p>
            <button type="submit" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#b5432f] hover:underline"><x-icon name="circle-x" /> Delete</button>
        </form>
    </aside>
</div>
@endsection
