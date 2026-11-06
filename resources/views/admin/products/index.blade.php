@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<x-admin.page-header title="Products" description="Products shown in the website’s Shop. Hidden products are kept but not shown.">
    <x-button :href="route('admin.products.create')" variant="dark" size="sm" icon="plus">Add product</x-button>
</x-admin.page-header>

<form method="GET" class="relative mt-8 w-full sm:w-80">
    <label for="q" class="sr-only">Search products</label>
    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 -translate-y-1/2 text-smoke" />
    <input id="q" name="q" type="search" value="{{ $search }}" placeholder="Search products…" class="admin-input pl-10">
</form>

<section class="mt-6 overflow-hidden rounded-2xl bg-white ring-1 ring-ink/[0.06]">
    @if ($products->isEmpty())
        <div class="px-6 py-20 text-center">
            <x-icon name="box" class="text-3xl text-brass" />
            <p class="mt-4 font-semibold">{{ $search ? 'No products match your search.' : 'No products yet.' }}</p>
            <x-button :href="route('admin.products.create')" variant="dark" size="sm" icon="plus" class="mt-6">Add your first product</x-button>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="border-b border-ink/5 bg-[#faf8f4] text-left text-xs text-smoke">
                    <tr>
                        <th class="px-5 py-3 font-medium lg:px-7">Product</th>
                        <th class="px-3 py-3 font-medium">Category</th>
                        <th class="px-3 py-3 text-right font-medium">Price</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 text-right font-medium lg:px-7"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5">
                    @foreach ($products as $product)
                        @php $card = $product->toCatalogArray(); @endphp
                        <tr class="transition hover:bg-[#faf8f4]">
                            <td class="px-5 py-3 lg:px-7">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset($card['image']) }}" alt="" class="h-12 w-10 shrink-0 rounded object-cover" loading="lazy">
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="font-semibold hover:underline">{{ $product->name }}</a>
                                        @if ($product->badge)<p class="text-xs text-smoke">{{ $product->badge }}</p>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">{{ $product->category_name }}</td>
                            <td class="px-3 py-3 text-right whitespace-nowrap tabular-nums">{{ $card['price_label'] }}</td>
                            <td class="px-3 py-3"><x-admin.status-badge :tone="$product->is_active ? 'good' : 'neutral'" :label="$product->is_active ? 'Visible' : 'Hidden'" /></td>
                            <td class="px-5 py-3 text-right whitespace-nowrap lg:px-7">
                                <div class="inline-flex items-center gap-1">
                                    @if ($product->is_active)
                                        <a href="{{ route('shop.show', $product->slug) }}" target="_blank" rel="noopener" class="icon-btn h-9 w-9 text-smoke hover:text-ink" title="View on website"><x-icon name="external-link" /></a>
                                    @endif
                                    <a href="{{ route('admin.products.edit', $product) }}" class="icon-btn h-9 w-9 text-smoke hover:text-ink" title="Edit"><x-icon name="pen-tool" /></a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" x-data @submit="if (!confirm('Delete “{{ addslashes($product->name) }}”?')) $event.preventDefault()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn h-9 w-9 text-smoke hover:text-[#b5432f]" title="Delete"><x-icon name="circle-x" /></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

{{ $products->links('admin.partials.pagination') }}
@endsection
