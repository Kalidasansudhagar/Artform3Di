@extends('layouts.admin')

@section('title', $product->exists ? 'Edit product' : 'Add product')

@section('content')
<a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 text-sm text-ink/60 hover:text-ink"><x-icon name="arrow-left" /> All products</a>

<form method="POST" enctype="multipart/form-data"
      action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
      class="mt-6 grid gap-4 xl:grid-cols-12">
    @csrf
    @if ($product->exists) @method('PUT') @endif

    <div class="space-y-4 xl:col-span-8">
        <section class="admin-card space-y-5">
            <h2 class="font-semibold">Details</h2>
            <x-admin.input name="name" label="Product name" :value="old('name', $product->name)" required maxlength="160" />
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="category" class="text-sm font-medium">Category</label>
                    <select id="category" name="category" class="admin-input mt-2" required>
                        @foreach (\App\Support\Catalog::CATEGORIES as $key => $label)
                            <option value="{{ $key }}" @selected(old('category', $product->category) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="mt-2 text-xs text-[#b5432f]">{{ $message }}</p>@enderror
                </div>
                <x-admin.input name="slug" label="Web address (optional)" :value="old('slug', $product->slug)" hint="Leave empty to create it from the name." />
            </div>
            <x-admin.input name="summary" label="Short summary" type="textarea" rows="2" :value="old('summary', $product->summary)" hint="Shown under the product name on its page." />
            <x-admin.input name="description" label="Description" type="textarea" rows="5" :value="old('description', $product->description)" />
        </section>

        <section class="admin-card space-y-5">
            <h2 class="font-semibold">Specifications</h2>
            <div class="grid gap-5 sm:grid-cols-3">
                <x-admin.input name="material" label="Material & finish" :value="old('material', $product->material)" />
                <x-admin.input name="dimensions" label="Dimensions" :value="old('dimensions', $product->dimensions)" placeholder="H 24 × W 18 × D 14 cm" />
                <x-admin.input name="weight" label="Weight" :value="old('weight', $product->weight)" placeholder="0.9 kg" />
            </div>
        </section>

        <section class="admin-card space-y-5" x-data="{ files: [] }">
            <div>
                <h2 class="font-semibold">Images</h2>
                <p class="mt-1 text-sm text-smoke">Up to 3 images (JPG, PNG or WebP, max 4 MB each). The first image is the main photo.</p>
            </div>
            @if ($product->images)
                <ul class="grid grid-cols-3 gap-3">
                    @foreach ($product->images as $image)
                        <li x-data="{ remove: false }" class="relative">
                            <img src="{{ asset($image) }}" alt="" class="aspect-[4/5] w-full rounded-lg object-cover transition" :class="remove && 'opacity-30'">
                            <label class="mt-2 flex cursor-pointer items-center gap-2 text-xs">
                                <input type="checkbox" name="remove_images[]" value="{{ $image }}" x-model="remove" class="accent-[#b5432f]"> Remove
                            </label>
                        </li>
                    @endforeach
                </ul>
            @endif
            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-dashed border-ink/20 px-4 py-4 text-sm transition hover:border-ink/50">
                <x-icon name="upload" class="text-lg text-brass" />
                <span x-text="files.length ? files.length + ' image(s) selected' : 'Choose images to upload'">Choose images to upload</span>
                <input type="file" name="new_images[]" accept="image/jpeg,image/png,image/webp" multiple class="sr-only" @change="files = [...$event.target.files]">
            </label>
            @error('new_images')<p class="text-xs text-[#b5432f]">{{ $message }}</p>@enderror
            @error('new_images.*')<p class="text-xs text-[#b5432f]">{{ $message }}</p>@enderror
        </section>
    </div>

    <aside class="space-y-4 xl:col-span-4">
        <section class="admin-card space-y-5">
            <h2 class="font-semibold">Price & visibility</h2>
            <x-admin.input name="price" label="Price (₹)" type="number" min="0" step="1" :value="old('price', $product->price)" hint="Leave empty to show “Price on request”." />
            <x-admin.input name="badge" label="Badge (optional)" :value="old('badge', $product->badge)" placeholder="New, Limited…" />
            <x-admin.input name="sort_order" label="Sort order" type="number" min="0" :value="old('sort_order', $product->sort_order)" hint="Lower numbers appear first." />
            <label class="flex cursor-pointer items-center gap-3 text-sm">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 accent-ink" @checked(old('is_active', $product->is_active))>
                Show in the shop
            </label>
        </section>

        <div class="flex flex-col gap-3">
            <button type="submit" class="btn btn-dark btn-lg w-full"><span>{{ $product->exists ? 'Save product' : 'Add product' }}</span><span class="btn-icon"><x-icon name="check" /></span></button>
            @if ($product->exists && $product->is_active)
                <x-button :href="route('shop.show', $product->slug)" target="_blank" rel="noopener" variant="outline-dark" icon="external-link" class="w-full">View on website</x-button>
            @endif
        </div>
    </aside>
</form>
@endsection
