@extends('layouts.admin')

@section('title', 'Projects')

@section('content')
<x-admin.page-header title="Projects" description="Project tiles shown on the Home and 3D Printing pages. The homepage shows the first six visible projects.">
    <x-button :href="route('admin.projects.create')" variant="dark" size="sm" icon="plus">Add project</x-button>
</x-admin.page-header>

@if ($projects->isEmpty())
    <div class="admin-card mt-8 py-16 text-center">
        <x-icon name="folder-kanban" class="text-3xl text-brass" />
        <p class="mt-4 font-semibold">No projects yet.</p>
        <x-button :href="route('admin.projects.create')" variant="dark" size="sm" icon="plus" class="mt-6">Add your first project</x-button>
    </div>
@else
    <ul class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($projects as $project)
            @php $card = $project->toCatalogArray(); @endphp
            <li class="overflow-hidden rounded-2xl bg-white ring-1 ring-ink/[0.06]">
                <div class="relative">
                    <img src="{{ asset($card['image']) }}" alt="" class="aspect-[7/5] w-full object-cover" loading="lazy">
                    <span class="absolute top-3 left-3"><x-admin.status-badge :tone="$project->is_active ? 'good' : 'neutral'" :label="$project->is_active ? 'Visible' : 'Hidden'" class="bg-white/90" /></span>
                </div>
                <div class="p-5">
                    <p class="mono text-smoke">{{ $project->category }}</p>
                    <h3 class="mt-2 font-semibold">{{ $project->title }}</h3>
                    <p class="mt-1 line-clamp-2 text-sm text-ink/60">{{ $project->summary }}</p>
                    <div class="mt-4 flex items-center justify-between border-t border-ink/5 pt-4">
                        <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center gap-2 text-sm font-semibold hover:underline"><x-icon name="pen-tool" /> Edit</a>
                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" x-data @submit="if (!confirm('Delete “{{ addslashes($project->title) }}”?')) $event.preventDefault()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-2 text-sm text-[#b5432f] hover:underline"><x-icon name="circle-x" /> Delete</button>
                        </form>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
    {{ $projects->links('admin.partials.pagination') }}
@endif
@endsection
