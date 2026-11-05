<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Placeholder catalogue for the frontend phase.
 *
 * Every product, price, dimension and project below is sample content to be
 * replaced by the client's real data. The array shapes mirror what future
 * Eloquent models should expose so the Blade views will not need to change.
 */
class Catalog
{
    public const CATEGORIES = [
        'hindu-deity-sculptures' => 'Hindu Deity Sculptures',
        'spiritual-art' => 'Spiritual Art',
        'decorative-sculptures' => 'Decorative Sculptures',
        'premium-figurines' => 'Premium Figurines',
        'custom-3d-creations' => 'Custom 3D Creations',
    ];

    /** Art direction for each placeholder: [variant, primary tone, alternate tone, seed]. */
    public const ART = [
        'ganesha-seated-study' => ['deity', 'bronze', 'stone', 1],
        'lakshmi-lotus-form' => ['deity', 'ivory', 'clay', 2],
        'nataraja-cosmic-ring' => ['deity', 'graphite', 'stone', 3],
        'serene-buddha' => ['deity', 'stone', 'graphite', 4],
        'om-resonance' => ['twist', 'bronze', 'stone', 2],
        'stillness-orb' => ['orb', 'ivory', 'clay', 1],
        'aurora-vessel' => ['vase', 'stone', 'graphite', 1],
        'helix-form-iii' => ['twist', 'graphite', 'stone', 5],
        'contour-vase' => ['vase', 'bronze', 'clay', 3],
        'the-thinker-bust' => ['bust', 'ivory', 'stone', 1],
        'dancer-in-motion' => ['figurine', 'bronze', 'stone', 2],
        'bespoke-portrait-bust' => ['bust', 'graphite', 'clay', 2],
    ];

    public static function products(): Collection
    {
        return collect([
            ['slug' => 'ganesha-seated-study', 'name' => 'Ganesha — Seated Study', 'category' => 'hindu-deity-sculptures', 'price' => 14500, 'badge' => 'Signature', 'material' => 'Premium PLA, hand-finished antique bronze patina', 'dimensions' => 'H 24 × W 18 × D 14 cm', 'weight' => '0.9 kg'],
            ['slug' => 'aurora-vessel', 'name' => 'Aurora Vessel', 'category' => 'decorative-sculptures', 'price' => 11200, 'badge' => null, 'material' => 'Stone-fill PLA, unglazed ceramic look', 'dimensions' => 'H 34 × Ø 18 cm', 'weight' => '0.9 kg'],
            ['slug' => 'the-thinker-bust', 'name' => 'The Thinker — Bust', 'category' => 'premium-figurines', 'price' => 16500, 'badge' => null, 'material' => 'High-detail resin, ivory marble finish', 'dimensions' => 'H 28 × W 20 × D 15 cm', 'weight' => '1.1 kg'],
            ['slug' => 'lakshmi-lotus-form', 'name' => 'Lakshmi Lotus Form', 'category' => 'hindu-deity-sculptures', 'price' => 18900, 'badge' => 'New', 'material' => 'High-detail resin, pearl ivory finish', 'dimensions' => 'H 30 × W 20 × D 16 cm', 'weight' => '1.2 kg'],
            ['slug' => 'helix-form-iii', 'name' => 'Helix Form III', 'category' => 'decorative-sculptures', 'price' => 13400, 'badge' => 'New', 'material' => 'Carbon-fibre PETG, satin graphite', 'dimensions' => 'H 40 × W 16 × D 16 cm', 'weight' => '0.7 kg'],
            ['slug' => 'stillness-orb', 'name' => 'Stillness Orb', 'category' => 'spiritual-art', 'price' => 7900, 'badge' => 'Limited', 'material' => 'Translucent resin on turned pedestal', 'dimensions' => 'H 20 × Ø 14 cm', 'weight' => '0.6 kg'],
            ['slug' => 'serene-buddha', 'name' => 'Serene Buddha', 'category' => 'spiritual-art', 'price' => 12800, 'badge' => null, 'material' => 'Stone-fill PLA, matte sandstone finish', 'dimensions' => 'H 22 × W 18 × D 13 cm', 'weight' => '0.8 kg'],
            ['slug' => 'dancer-in-motion', 'name' => 'Dancer in Motion', 'category' => 'premium-figurines', 'price' => 10900, 'badge' => null, 'material' => 'Premium PLA, hand-burnished bronze', 'dimensions' => 'H 32 × W 10 × D 10 cm', 'weight' => '0.4 kg'],
            ['slug' => 'contour-vase', 'name' => 'Contour Vase', 'category' => 'decorative-sculptures', 'price' => 8700, 'badge' => null, 'material' => 'Silk PLA, warm bronze finish', 'dimensions' => 'H 26 × Ø 16 cm', 'weight' => '0.5 kg'],
            ['slug' => 'nataraja-cosmic-ring', 'name' => 'Nataraja Cosmic Ring', 'category' => 'hindu-deity-sculptures', 'price' => 22000, 'badge' => null, 'material' => 'Premium PLA, graphite metallic finish', 'dimensions' => 'H 36 × W 32 × D 12 cm', 'weight' => '1.4 kg'],
            ['slug' => 'om-resonance', 'name' => 'Om Resonance', 'category' => 'spiritual-art', 'price' => 9600, 'badge' => null, 'material' => 'Silk PLA, brushed bronze finish', 'dimensions' => 'H 28 × W 12 × D 12 cm', 'weight' => '0.5 kg'],
            ['slug' => 'bespoke-portrait-bust', 'name' => 'Bespoke Portrait Bust', 'category' => 'custom-3d-creations', 'price' => 24000, 'badge' => 'Made to order', 'material' => 'Scanned and sculpted to brief; finish of your choice', 'dimensions' => 'Made to your specification', 'weight' => 'Varies'],
        ])->map(fn (array $p) => self::hydrate($p));
    }

    public static function find(string $slug): ?array
    {
        return self::products()->firstWhere('slug', $slug);
    }

    public static function related(array $product, int $count = 4): Collection
    {
        return self::products()
            ->reject(fn ($p) => $p['slug'] === $product['slug'])
            ->sortByDesc(fn ($p) => $p['category'] === $product['category'])
            ->take($count)
            ->values();
    }

    public static function collections(): array
    {
        return [
            ['title' => 'The Divine Series', 'text' => 'Devotional forms rendered with sculptural restraint.', 'category' => 'hindu-deity-sculptures', 'image' => 'assets/images/collections/divine.svg'],
            ['title' => 'Living Forms', 'text' => 'Vessels and abstractions shaped by parametric curves.', 'category' => 'decorative-sculptures', 'image' => 'assets/images/collections/living-forms.svg'],
            ['title' => 'Bespoke Studio', 'text' => 'Portraits and commissions modelled to your brief.', 'category' => 'custom-3d-creations', 'image' => 'assets/images/collections/bespoke.svg'],
        ];
    }

    public static function projects(): array
    {
        return [
            ['title' => 'Heritage Relief Study', 'category' => 'Sculpture', 'text' => 'A layered relief panel translating traditional carving into a printed, hand-finished surface.', 'image' => 'assets/images/projects/heritage-relief.svg'],
            ['title' => 'Survey Quad Airframe', 'category' => 'Drone Engineering', 'text' => 'A lightweight printed airframe iterated through rapid prototyping and flight testing.', 'image' => 'assets/images/projects/survey-quad.svg'],
            ['title' => 'Enclosure Prototype', 'category' => 'Rapid Prototyping', 'text' => 'Functional housing prototypes for fit, form and assembly validation.', 'image' => 'assets/images/projects/enclosure.svg'],
            ['title' => 'Parametric Vessel Series', 'category' => 'Art & Decor', 'text' => 'A family of algorithmically shaped vessels for an interiors collection.', 'image' => 'assets/images/projects/vessel-series.svg'],
            ['title' => 'Digital Portrait Bust', 'category' => '3D Modelling', 'text' => 'From reference photography to a sculpted digital model and a printed bust.', 'image' => 'assets/images/projects/portrait-bust.svg'],
            ['title' => 'Hexacopter R&D Platform', 'category' => 'Research & Development', 'text' => 'A modular test platform for payload and propulsion experiments.', 'image' => 'assets/images/projects/hexacopter.svg'],
        ];
    }

    private static function hydrate(array $p): array
    {
        $p['category_name'] = self::CATEGORIES[$p['category']];
        $p['images'] = [
            "assets/images/products/{$p['slug']}-1.svg",
            "assets/images/products/{$p['slug']}-2.svg",
            "assets/images/products/{$p['slug']}-3.svg",
        ];
        $p['image'] = $p['images'][0];
        $p['image_url'] = asset($p['image']);
        $p['url'] = route('shop.show', $p['slug']);
        $p['price_label'] = ($p['slug'] === 'bespoke-portrait-bust' ? 'From ' : '').'₹'.number_format($p['price']);
        $p['excerpt'] ??= 'A sculptural piece printed layer by layer and finished by hand in the studio.';
        // Path to a .glb file once the client supplies 3D models; enables the interactive viewer.
        $p['model'] ??= null;

        return $p;
    }
}
