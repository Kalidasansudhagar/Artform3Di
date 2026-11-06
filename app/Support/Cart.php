<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Shopping bag kept in the session as [product_id => quantity].
 * Prices and availability are always read from the database, so admin
 * changes (price, hidden, deleted) apply to bags straight away.
 */
class Cart
{
    public const MAX_QTY = 20;

    private const KEY = 'cart';

    /** @return array<int, int> */
    public static function raw(): array
    {
        return session(self::KEY, []);
    }

    public static function add(Product $product, int $quantity = 1): void
    {
        $cart = self::raw();
        $cart[$product->id] = min(self::MAX_QTY, ($cart[$product->id] ?? 0) + max(1, $quantity));
        session([self::KEY => $cart]);
    }

    public static function update(int $productId, int $quantity): void
    {
        $cart = self::raw();
        if ($quantity < 1) {
            unset($cart[$productId]);
        } elseif (isset($cart[$productId])) {
            $cart[$productId] = min(self::MAX_QTY, $quantity);
        }
        session([self::KEY => $cart]);
    }

    public static function remove(int $productId): void
    {
        self::update($productId, 0);
    }

    public static function clear(): void
    {
        session()->forget(self::KEY);
    }

    /**
     * Lines for products that can still be bought; anything hidden, deleted or
     * without a price is dropped from the bag.
     *
     * @return Collection<int, array{product: array, id: int, quantity: int, unit_price: int, line_total: int}>
     */
    public static function items(): Collection
    {
        $cart = self::raw();
        if (! $cart) {
            return collect();
        }

        $products = Product::query()->whereKey(array_keys($cart))->where('is_active', true)->whereNotNull('price')->get()->keyBy('id');

        $lines = collect($cart)
            ->filter(fn ($qty, $id) => $products->has($id))
            ->map(fn ($qty, $id) => [
                'id' => (int) $id,
                'product' => $products[$id]->toCatalogArray(),
                'quantity' => (int) $qty,
                'unit_price' => (int) $products[$id]->price,
                'line_total' => (int) $products[$id]->price * (int) $qty,
            ])
            ->values();

        if ($lines->count() !== count($cart)) {
            session([self::KEY => $lines->mapWithKeys(fn ($l) => [$l['id'] => $l['quantity']])->all()]);
        }

        return $lines;
    }

    public static function count(): int
    {
        return array_sum(self::raw());
    }

    public static function subtotal(?Collection $items = null): int
    {
        return ($items ?? self::items())->sum('line_total');
    }
}
