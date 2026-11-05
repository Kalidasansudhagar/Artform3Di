<?php

namespace App\Support;

use Carbon\CarbonImmutable;

/**
 * Sample data for the admin dashboard preview.
 *
 * Every figure here is illustrative and deterministic. Replace each method with
 * real queries (orders, enquiries, customers) in the backend phase; the array
 * shapes are what the dashboard view and charts consume.
 */
class AdminDemo
{
    public const RANGES = [
        '7d' => 'Last 7 days',
        '30d' => 'Last 30 days',
        '90d' => 'Last 90 days',
        '12m' => 'Last 12 months',
    ];

    public static function dashboard(): array
    {
        $series = collect(self::RANGES)->keys()->mapWithKeys(fn ($range) => [$range => self::revenueSeries($range)]);

        return [
            'ranges' => self::RANGES,
            'defaultRange' => '30d',
            'series' => $series->all(),
            'kpis' => $series->map(fn ($points, $range) => self::kpis($range, $points))->all(),
            'statusCounts' => self::statusCounts(),
            'categorySales' => self::categorySales(),
            'orders' => self::recentOrders(),
            'enquiries' => self::enquiries(),
            'topProducts' => self::topProducts(),
        ];
    }

    /** Revenue points for a range: [label, value]. Smooth upward trend with weekly rhythm. */
    private static function revenueSeries(string $range): array
    {
        $today = CarbonImmutable::today();

        [$count, $step, $format, $base] = match ($range) {
            '7d' => [7, 'day', 'D j', 9800],
            '30d' => [30, 'day', 'j M', 9200],
            '90d' => [13, 'week', 'j M', 61000],
            '12m' => [12, 'month', 'M Y', 236000],
        };

        $points = [];
        for ($i = $count - 1; $i >= 0; $i--) {
            $date = $today->sub($step, $i);
            $k = $count - $i;
            $value = $base * (1 + 0.012 * $k) * (1 + 0.16 * sin($k * 1.3) + 0.07 * cos($k * 0.55));
            $points[] = ['label' => $date->format($format), 'value' => (int) (round($value / 100) * 100)];
        }

        return $points;
    }

    private static function kpis(string $range, array $points): array
    {
        $revenue = array_sum(array_column($points, 'value'));
        $scale = ['7d' => 0.24, '30d' => 1, '90d' => 2.9, '12m' => 11.6][$range];
        $orders = (int) round(64 * $scale);
        $values = array_column($points, 'value');

        return [
            ['label' => 'Revenue', 'value' => $revenue, 'format' => 'currency', 'delta' => 12.4, 'trend' => self::sparkline($values)],
            ['label' => 'Orders', 'value' => $orders, 'format' => 'number', 'delta' => 8.1, 'trend' => self::sparkline($values, 0.9)],
            ['label' => 'Average order value', 'value' => (int) round($revenue / max($orders, 1)), 'format' => 'currency', 'delta' => 3.9, 'trend' => self::sparkline($values, 1.1)],
            ['label' => 'Project enquiries', 'value' => (int) round(27 * $scale), 'format' => 'number', 'delta' => -4.2, 'trend' => self::sparkline($values, 1.6)],
        ];
    }

    /** 12-point sparkline derived from the series, varied by a phase factor. */
    private static function sparkline(array $values, float $phase = 1.0): array
    {
        $out = [];
        for ($i = 0; $i < 12; $i++) {
            $v = $values[(int) floor($i * (count($values) - 1) / 11)];
            $out[] = (int) round($v * (1 + 0.08 * sin($i * $phase)));
        }

        return $out;
    }

    private static function statusCounts(): array
    {
        return [
            ['label' => 'Awaiting confirmation', 'count' => 6, 'tone' => 'warning'],
            ['label' => 'Printing', 'count' => 9, 'tone' => 'neutral'],
            ['label' => 'Finishing', 'count' => 4, 'tone' => 'neutral'],
            ['label' => 'Shipped', 'count' => 11, 'tone' => 'neutral'],
            ['label' => 'Delivered', 'count' => 42, 'tone' => 'good'],
            ['label' => 'Cancelled', 'count' => 2, 'tone' => 'critical'],
        ];
    }

    private static function categorySales(): array
    {
        return collect(Catalog::CATEGORIES)->values()
            ->zip([148600, 96200, 82400, 61300, 118900])
            ->map(fn ($pair) => ['label' => $pair[0], 'value' => $pair[1]])
            ->sortByDesc('value')->values()->all();
    }

    private static function recentOrders(): array
    {
        $rows = [
            ['AF-1048', 'Priya S.', 'lakshmi-lotus-form', 1, 'Printing', 'neutral', '2 hours ago'],
            ['AF-1047', 'Arjun M.', 'bespoke-portrait-bust', 1, 'Awaiting confirmation', 'warning', '5 hours ago'],
            ['AF-1046', 'Meera K.', 'aurora-vessel', 2, 'Shipped', 'neutral', 'Yesterday'],
            ['AF-1045', 'Rahul V.', 'ganesha-seated-study', 1, 'Delivered', 'good', 'Yesterday'],
            ['AF-1044', 'Divya R.', 'helix-form-iii', 1, 'Finishing', 'neutral', '2 days ago'],
            ['AF-1043', 'Karthik N.', 'stillness-orb', 3, 'Cancelled', 'critical', '3 days ago'],
        ];

        return collect($rows)->map(function ($r) {
            $product = Catalog::find($r[2]);

            return [
                'number' => $r[0], 'customer' => $r[1], 'product' => $product['name'], 'image' => $product['image'],
                'qty' => $r[3], 'total' => $product['price'] * $r[3], 'status' => $r[4], 'tone' => $r[5], 'when' => $r[6],
            ];
        })->all();
    }

    private static function enquiries(): array
    {
        return [
            ['name' => 'Anitha P.', 'type' => 'Custom sculpture', 'excerpt' => 'Looking for a 45 cm temple-style Ganesha for our new home…', 'when' => '35 min ago', 'new' => true],
            ['name' => 'Vikram T.', 'type' => 'Drone engineering', 'excerpt' => 'Need a lightweight frame prototype for an agricultural survey drone…', 'when' => '3 hours ago', 'new' => true],
            ['name' => 'Studio Nine', 'type' => '3D printing service', 'excerpt' => 'Batch of 20 enclosure prototypes in PETG, two colour options…', 'when' => 'Yesterday', 'new' => false],
            ['name' => 'Lakshmi R.', 'type' => 'Shop enquiry', 'excerpt' => 'Is the Serene Buddha available in a larger size with ivory finish?', 'when' => '2 days ago', 'new' => false],
        ];
    }

    private static function topProducts(): array
    {
        return collect([['ganesha-seated-study', 38], ['lakshmi-lotus-form', 27], ['helix-form-iii', 21], ['the-thinker-bust', 16], ['contour-vase', 14]])
            ->map(fn ($p) => ['product' => Catalog::find($p[0]), 'units' => $p[1]])
            ->all();
    }
}
