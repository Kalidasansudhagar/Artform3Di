<?php

namespace App\Http\Controllers;

use App\Support\Catalog;
use Illuminate\View\View;

/**
 * Public pages. Products and projects come from the database (managed in the
 * admin panel) through Catalog, in the array shapes the views expect.
 */
class FrontendController extends Controller
{
    public function home(): View
    {
        return view('frontend.home', [
            'featured' => Catalog::products()->take(6)->values(),
            // The editorial project grid on the homepage is laid out for up to six tiles.
            'projects' => array_slice(Catalog::projects(), 0, 6),
        ]);
    }

    public function shop(): View
    {
        $products = Catalog::products();

        return view('frontend.shop', [
            'products' => $products,
            'categories' => Catalog::CATEGORIES,
            'collections' => Catalog::collections(),
            'priceCeiling' => max(10000, (int) ceil(($products->max('price') ?? 0) / 1000) * 1000),
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
            'projects' => collect(Catalog::projects())->values(),
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
