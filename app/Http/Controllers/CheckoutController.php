<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Checkout for signed-in customers. Orders are placed without online payment:
 * the studio confirms each order (Admin → Orders), then the customer pays by
 * cash on delivery or UPI / bank transfer.
 */
class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $items = Cart::items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $user = $request->user();
        $lastOrder = $user->orders()->first();

        return view('frontend.checkout', [
            'items' => $items,
            'subtotal' => Cart::subtotal($items),
            'address' => $user->address ?? $lastOrder?->shipping_address ?? ['full_name' => $user->name, 'phone' => $user->phone],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $items = Cart::items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('notice', 'Your bag is empty.');
        }

        $address = AccountController::validateAddress($request);
        $data = $request->validate([
            'payment_method' => ['required', Rule::in(array_keys(Order::PAYMENT_METHODS))],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $subtotal = Cart::subtotal($items);

        $order = DB::transaction(function () use ($user, $items, $address, $data, $subtotal) {
            $order = Order::query()->create([
                'user_id' => $user->id,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'customer_name' => $address['full_name'],
                'customer_email' => $user->email,
                'customer_phone' => $address['phone'],
                'shipping_address' => $address,
                'notes' => $data['notes'] ?? null,
            ]);

            $order->items()->createMany($items->map(fn ($line) => [
                'product_id' => $line['id'],
                'product_name' => $line['product']['name'],
                'product_slug' => $line['product']['slug'],
                'image' => $line['product']['image'],
                'unit_price' => $line['unit_price'],
                'quantity' => $line['quantity'],
                'line_total' => $line['line_total'],
            ])->all());

            return $order;
        });

        // Remember the address for next time.
        $user->update(['address' => $address, 'phone' => $user->phone ?: $address['phone']]);
        Cart::clear();

        return redirect()->route('account.orders.show', $order)->with('placed', true);
    }
}
