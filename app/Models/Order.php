<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'status', 'payment_method', 'subtotal', 'total', 'customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'notes', 'admin_notes'])]
class Order extends Model
{
    /** Status => [label, badge tone]. Tones map to the admin status-badge component. */
    public const STATUSES = [
        'pending' => ['Pending', 'warning'],
        'confirmed' => ['Confirmed', 'neutral'],
        'printing' => ['Printing', 'neutral'],
        'shipped' => ['Shipped', 'neutral'],
        'delivered' => ['Delivered', 'good'],
        'cancelled' => ['Cancelled', 'critical'],
    ];

    /** No card payments are taken online: the studio confirms the order, then the customer pays. */
    public const PAYMENT_METHODS = [
        'cod' => 'Cash on delivery',
        'upi' => 'UPI / bank transfer after confirmation',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'subtotal' => 'integer',
            'total' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getNumberAttribute(): string
    {
        return 'AF-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status][0] ?? ucfirst($this->status);
    }

    public function getStatusToneAttribute(): string
    {
        return self::STATUSES[$this->status][1] ?? 'neutral';
    }

    public function getPaymentLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    /** Address lines for display, without empty parts. */
    public function getAddressLinesAttribute(): array
    {
        $a = $this->shipping_address ?? [];

        return array_values(array_filter([
            $a['line1'] ?? null,
            $a['line2'] ?? null,
            trim(($a['city'] ?? '').', '.($a['state'] ?? '').' '.($a['postcode'] ?? ''), ', '),
        ]));
    }

    public static function money(int $amount): string
    {
        return '₹'.number_format($amount);
    }
}
