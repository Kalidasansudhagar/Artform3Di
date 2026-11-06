<?php

namespace App\Support;

use App\Models\Enquiry;
use App\Models\Product;
use App\Models\Order;
use Carbon\CarbonImmutable;

/**
 * Live figures for the admin dashboard, built from the enquiries, orders and products tables.
 */
class AdminStats
{
    public const RANGES = [
        '7d' => 'Last 7 days',
        '30d' => 'Last 30 days',
        '90d' => 'Last 90 days',
        '12m' => 'Last 12 months',
    ];

    public static function dashboard(): array
    {
        $series = collect(self::RANGES)->keys()->mapWithKeys(fn ($range) => [$range => self::enquirySeries($range)]);
        $newCount = Enquiry::query()->where('status', 'new')->count();
        $activeProducts = Product::query()->where('is_active', true)->count();
        $pendingOrders = Order::query()->where('status', 'pending')->count();

        return [
            'ranges' => self::RANGES,
            'defaultRange' => '30d',
            'series' => $series->all(),
            'kpis' => $series->map(fn ($points, $range) => [
                ['label' => 'Enquiries', 'value' => array_sum(array_column($points, 'value')), 'delta' => self::delta($range), 'trend' => self::sparkline($points)],
                ['label' => 'Awaiting reply', 'value' => $newCount, 'delta' => null, 'trend' => null],
                ['label' => 'Products in shop', 'value' => $activeProducts, 'delta' => null, 'trend' => null],
                ['label' => 'Orders to confirm', 'value' => $pendingOrders, 'delta' => null, 'trend' => null],
            ])->all(),
            'statusCounts' => collect(Enquiry::STATUSES)->map(fn ($meta, $status) => [
                'label' => $meta[0],
                'tone' => $meta[1],
                'count' => Enquiry::query()->where('status', $status)->count(),
            ])->values()->all(),
            'serviceCounts' => Enquiry::query()->selectRaw('subject as label, count(*) as value')
                ->groupBy('subject')->orderByDesc('value')->limit(6)->get()
                ->map(fn ($row) => ['label' => $row->label, 'value' => (int) $row->value])->all(),
            'recentEnquiries' => Enquiry::query()->latest()->take(6)->get(),
            'latestProducts' => Product::query()->latest()->take(5)->get(),
        ];
    }

    /** Enquiry counts per day / week / month for a range: [label, value]. */
    private static function enquirySeries(string $range): array
    {
        $today = CarbonImmutable::today();
        [$count, $unit, $format] = match ($range) {
            '7d' => [7, 'day', 'D j'],
            '30d' => [30, 'day', 'j M'],
            '90d' => [13, 'week', 'j M'],
            '12m' => [12, 'month', 'M Y'],
        };

        $points = [];
        for ($i = $count - 1; $i >= 0; $i--) {
            $start = match ($unit) {
                'day' => $today->subDays($i),
                'week' => $today->subWeeks($i)->startOfWeek(),
                'month' => $today->subMonths($i)->startOfMonth(),
            };
            $end = match ($unit) {
                'day' => $start->endOfDay(),
                'week' => $start->endOfWeek(),
                'month' => $start->endOfMonth(),
            };
            $points[] = [
                'label' => $start->format($format),
                'value' => Enquiry::query()->whereBetween('created_at', [$start, $end])->count(),
            ];
        }

        return $points;
    }

    /** Percentage change versus the previous period of the same length, or null when there is nothing to compare. */
    private static function delta(string $range): ?float
    {
        $days = ['7d' => 7, '30d' => 30, '90d' => 91, '12m' => 365][$range];
        $now = CarbonImmutable::now();
        $current = Enquiry::query()->where('created_at', '>=', $now->subDays($days))->count();
        $previous = Enquiry::query()->whereBetween('created_at', [$now->subDays($days * 2), $now->subDays($days)])->count();

        return $previous ? round(($current - $previous) / $previous * 100, 1) : null;
    }

    private static function sparkline(array $points): array
    {
        $values = array_column($points, 'value');
        $out = [];
        for ($i = 0; $i < 12; $i++) {
            $out[] = $values[(int) floor($i * (count($values) - 1) / 11)];
        }

        return $out;
    }
}
