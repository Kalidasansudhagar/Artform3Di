<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());

        $orders = Order::query()
            ->withCount('items')
            ->withSum('items as units', 'quantity')
            ->when(array_key_exists($status, Order::STATUSES), fn ($q) => $q->where('status', $status))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_email', 'like', "%{$search}%")
                ->orWhere('customer_phone', 'like', "%{$search}%")
                ->orWhere('id', (int) preg_replace('/\D/', '', $search) ?: 0)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'counts' => Order::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load('items', 'user')]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $order->update($request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]));

        return back()->with('status', "Order {$order->number} updated.");
    }
}
