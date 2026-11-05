<?php

namespace App\Http\Controllers;

use App\Support\Catalog;
use Illuminate\View\View;

/**
 * Frontend pages. Data comes from the placeholder Catalog for now; swap the
 * Catalog calls for Eloquent queries in the backend phase.
 */
class FrontendController extends Controller
{
    public function home(): View
    {
        return view('frontend.home', [
            'featured' => Catalog::products()->whereIn('slug', [
                'ganesha-seated-study', 'lakshmi-lotus-form', 'serene-buddha', 'helix-form-iii', 'the-thinker-bust', 'bespoke-portrait-bust',
            ])->values(),
            'projects' => Catalog::projects(),
        ]);
    }

    public function shop(): View
    {
        $products = Catalog::products();

        return view('frontend.shop', [
            'products' => $products,
            'categories' => Catalog::CATEGORIES,
            'collections' => Catalog::collections(),
            'priceCeiling' => (int) ceil($products->max('price') / 1000) * 1000,
        ]);
    }

    public function product(string $slug): View
    {
        $product = Catalog::find($slug) ?? abort(404);

        return view('frontend.product-details', [
            'product' => $product,
            'related' => Catalog::related($product),
        ]);
    }

    public function services(): View
    {
        return view('frontend.services', [
            'projects' => collect(Catalog::projects())->whereNotIn('category', ['Drone Engineering', 'Research & Development'])->values(),
        ]);
    }

    public function drones(): View
    {
        return view('frontend.drones');
    }

    public function about(): View
    {
        return view('frontend.about');
    }

    public function contact(): View
    {
        return view('frontend.contact');
    }
}
