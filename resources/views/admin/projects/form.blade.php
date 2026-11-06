@extends('layouts.admin')

@section('title', $project->exists ? 'Edit project' : 'Add project')

@section('content')
<a href="{{ route('admin.projects.index') }}" class="inline-flex items-center gap-2 text-sm text-ink/60 hover:text-ink"><x-icon name="arrow-left" /> All projects</a>

<form method="POST" enctype="multipart/form-data"
      action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
      class="mt-6 grid gap-4 xl:grid-cols-12">
    @csrf
    @if ($project->exists) @method('PUT') @endif

    <section class="admin-card space-y-5 xl:col-span-8">
        <h2 class="font-semibold">Project</h2>
        <x-admin.input name="title" label="Title" :value="old('title', $project->title)" required maxlength="160" />
        <x-admin.input name="category" label="Category" :value="old('category', $project->category)" required maxlength="80" placeholder="Automotive & Restoration" />
        <x-admin.input name="summary" label="Short description" type="textarea" rows="3" :value="old('summary', $project->summary)" required maxlength="400" hint="One or two sentences: the requirement and what was delivered." />

        <div x-data="{ name: '' }">
            <p class="text-sm font-medium">Image</p>
            <div class="mt-2 grid gap-4 sm:grid-cols-[12rem_1fr] sm:items-center">
                <img src="{{ asset($project->toCatalogArray()['image']) }}" alt="" class="aspect-[7/5] w-full rounded-lg object-cover">
                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-dashed border-ink/20 px-4 py-4 text-sm transition hover:border-ink/50">
                    <x-icon name="upload" class="text-lg text-brass" />
                    <span x-text="name || '{{ $project->image ? 'Replace image' : 'Choose an image' }}'"></span>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="name = $event.target.files[0]?.name ?? ''">
                </label>
            </div>
            <p class="mt-1.5 text-xs text-smoke">JPG, PNG or WebP, max 4 MB. A wide (landscape) photo works best.</p>
            @error('image')<p class="mt-2 text-xs text-[#b5432f]">{{ $message }}</p>@enderror
        </div>
    </section>

    <aside class="space-y-4 xl:col-span-4">
        <section class="admin-card space-y-5">
            <h2 class="font-semibold">Visibility</h2>
            <x-admin.input name="sort_order" label="Sort order" type="number" min="0" :value="old('sort_order', $project->sort_order)" hint="Lower numbers appear first." />
            <label class="flex cursor-pointer items-center gap-3 text-sm">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 accent-ink" @checked(old('is_active', $project->is_active))>
                Show on the website
            </label>
        </section>
        <button type="submit" class="btn btn-dark btn-lg w-full"><span>{{ $project->exists ? 'Save project' : 'Add project' }}</span><span class="btn-icon"><x-icon name="check" /></span></button>
    </aside>
</form>
@endsection
