<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $items = Cart::items();

        return view('frontend.cart', ['items' => $items, 'subtotal' => Cart::subtotal($items)]);
    }

    /** Add to bag (and Buy Now, which then goes straight to checkout). */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'slug' => ['required', 'string'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.Cart::MAX_QTY],
        ]);

        $product = Product::query()->where('is_active', true)->where('slug', $data['slug'])->first();
        if (! $product) {
            throw ValidationException::withMessages(['slug' => 'This piece is no longer available.']);
        }
        if (! $product->price) {
            throw ValidationException::withMessages(['slug' => 'This piece is priced on request — please ask us for a quote.']);
        }

        Cart::add($product, (int) ($data['quantity'] ?? 1));

        if ($request->expectsJson()) {
            return response()->json(['count' => Cart::count(), 'message' => "“{$product->name}” added to your bag."]);
        }

        return redirect()->route('cart.index')->with('notice', "“{$product->name}” added to your bag.");
    }

    public function update(Request $request, int $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:'.Cart::MAX_QTY]]);
        Cart::update($product, (int) $data['quantity']);

        return redirect()->route('cart.index');
    }

    public function destroy(int $product): RedirectResponse
    {
        Cart::remove($product);

        return redirect()->route('cart.index')->with('notice', 'Removed from your bag.');
    }
}
