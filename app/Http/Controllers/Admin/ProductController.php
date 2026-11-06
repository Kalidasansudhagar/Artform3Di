<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Catalog;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    private const MAX_IMAGES = 3;

    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());

        return view('admin.products.index', [
            'products' => Product::query()
                ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                ->orderBy('sort_order')->orderBy('id')
                ->paginate(20)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', ['product' => new Product(['is_active' => true, 'sort_order' => Product::max('sort_order') + 1])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $product = new Product;
        $this->save($request, $product);

        return redirect()->route('admin.products.index')->with('status', "“{$product->name}” added.");
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', ['product' => $product]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->save($request, $product);

        return redirect()->route('admin.products.index')->with('status', "“{$product->name}” saved.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        Uploads::delete($product->images ?? []);
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', "“{$product->name}” deleted.");
    }

    private function save(Request $request, Product $product): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', 'alpha_dash', Rule::unique('products', 'slug')->ignore($product->id)],
            'category' => ['required', Rule::in(array_keys(Catalog::CATEGORIES))],
            'summary' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'badge' => ['nullable', 'string', 'max:40'],
            'material' => ['nullable', 'string', 'max:160'],
            'dimensions' => ['nullable', 'string', 'max:120'],
            'weight' => ['nullable', 'string', 'max:40'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'remove_images' => ['array'],
            'remove_images.*' => ['string'],
            'new_images' => ['array', 'max:'.self::MAX_IMAGES],
            'new_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $remove = $data['remove_images'] ?? [];
        $kept = array_values(array_diff($product->images ?? [], $remove));
        $uploaded = Uploads::store($request->file('new_images', []), 'products');
        $images = array_slice([...$kept, ...$uploaded], 0, self::MAX_IMAGES);
        Uploads::delete([...$remove, ...array_diff($uploaded, $images)]);

        $product->fill([
            'name' => $data['name'],
            'slug' => ($data['slug'] ?? null) ?: ($product->slug ?: $this->uniqueSlug($data['name'])),
            'category' => $data['category'],
            'summary' => $data['summary'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
            'badge' => $data['badge'] ?? null,
            'material' => $data['material'] ?? null,
            'dimensions' => $data['dimensions'] ?? null,
            'weight' => $data['weight'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'images' => $images,
        ])->save();
    }

    /** "contour-vase", then "contour-vase-2", "contour-vase-3"… if taken. */
    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        for ($i = 2; Product::query()->where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
