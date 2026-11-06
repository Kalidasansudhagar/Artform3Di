@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<x-admin.page-header title="Settings" description="Contact details and social links shown in the website’s header, footer and Contact page. Empty fields show “to be confirmed”." />

<form method="POST" action="{{ route('admin.settings.update') }}" class="mt-8 grid gap-4 xl:grid-cols-12">
    @csrf
    @method('PUT')

    <section class="admin-card space-y-5 xl:col-span-7">
        <h2 class="font-semibold">Contact details</h2>
        @foreach ($fields as $key => [$label, $type])
            @continue(! str_starts_with($key, 'contact.'))
            @php $name = str_replace('.', '_', $key); @endphp
            <x-admin.input :name="$name" :label="$label" :type="$type" :value="old($name, $values[$key] ?? null)" :rows="$type === 'textarea' ? 3 : null" />
        @endforeach
    </section>

    <aside class="space-y-4 xl:col-span-5">
        <section class="admin-card space-y-5">
            <h2 class="font-semibold">Social links</h2>
            @foreach ($fields as $key => [$label, $type])
                @continue(! str_starts_with($key, 'social.'))
                @php $name = str_replace('.', '_', $key); @endphp
                <x-admin.input :name="$name" :label="$label" :type="$type" :value="old($name, $values[$key] ?? null)" placeholder="https://" />
            @endforeach
        </section>
        <button type="submit" class="btn btn-dark btn-lg w-full"><span>Save settings</span><span class="btn-icon"><x-icon name="check" /></span></button>
    </aside>
</form>
@endsection
