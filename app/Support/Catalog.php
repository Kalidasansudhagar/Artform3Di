<?php

namespace App\Support;

use App\Models\Product;
use App\Models\Project;
use Illuminate\Support\Collection;

/**
 * Shop and project content. Products and projects live in the database and are
 * managed in the admin panel; the sample arrays below seed a fresh install
 * (`php artisan migrate --seed`) and can be edited or deleted from the admin.
 */
class Catalog
{
    public const CATEGORIES = [
        'hindu-deity-sculptures' => 'Statues & Sculptures',
        'spiritual-art' => 'Spiritual & Display Models',
        'decorative-sculptures' => 'Decorative Pieces & Gifts',
        'premium-figurines' => 'Miniatures & Figurines',
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

    /** Visible products, in the array shape the shop views use. */
    public static function products(): Collection
    {
        return Product::query()->visible()->get()->map(fn (Product $p) => $p->toCatalogArray());
    }

    public static function find(string $slug): ?array
    {
        return Product::query()->where('is_active', true)->where('slug', $slug)->first()?->toCatalogArray();
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
            ['title' => 'Statues & Models', 'text' => 'Statues, sculptures and display models, printed in sections where needed.', 'category' => 'hindu-deity-sculptures', 'image' => 'assets/images/collections/divine.svg'],
            ['title' => 'Gifts & Decor', 'text' => 'Personalised gifts, keepsakes and decorative pieces.', 'category' => 'decorative-sculptures', 'image' => 'assets/images/collections/living-forms.svg'],
            ['title' => 'Custom Creations', 'text' => 'Miniatures and custom models made from your references.', 'category' => 'custom-3d-creations', 'image' => 'assets/images/collections/bespoke.svg'],
        ];
    }

    /** Visible projects, in the array shape the home and services views use. */
    public static function projects(): array
    {
        return Project::query()->visible()->get()->map(fn (Project $p) => $p->toCatalogArray())->all();
    }

    /** Seed data: sample products until the client supplies their own. */
    public static function sampleProducts(): array
    {
        return array_map(fn (array $p) => $p + [
            'images' => ["assets/images/products/{$p['slug']}-1.svg", "assets/images/products/{$p['slug']}-2.svg", "assets/images/products/{$p['slug']}-3.svg"],
        ], [
            ['slug' => 'ganesha-seated-study', 'name' => 'Ganesha — Seated Study', 'category' => 'hindu-deity-sculptures', 'price' => 14500, 'badge' => 'Signature', 'material' => 'PLA, painted antique bronze finish', 'dimensions' => 'H 24 × W 18 × D 14 cm', 'weight' => '0.9 kg'],
            ['slug' => 'aurora-vessel', 'name' => 'Aurora Vessel', 'category' => 'decorative-sculptures', 'price' => 11200, 'badge' => null, 'material' => 'PLA, matte stone-look finish', 'dimensions' => 'H 34 × Ø 18 cm', 'weight' => '0.9 kg'],
            ['slug' => 'the-thinker-bust', 'name' => 'The Thinker — Bust', 'category' => 'premium-figurines', 'price' => 16500, 'badge' => null, 'material' => 'PLA, painted ivory finish', 'dimensions' => 'H 28 × W 20 × D 15 cm', 'weight' => '1.1 kg'],
            ['slug' => 'lakshmi-lotus-form', 'name' => 'Lakshmi Lotus Form', 'category' => 'hindu-deity-sculptures', 'price' => 18900, 'badge' => 'New', 'material' => 'PLA, painted pearl ivory finish', 'dimensions' => 'H 30 × W 20 × D 16 cm', 'weight' => '1.2 kg'],
            ['slug' => 'helix-form-iii', 'name' => 'Helix Form III', 'category' => 'decorative-sculptures', 'price' => 13400, 'badge' => 'New', 'material' => 'PETG, satin graphite finish', 'dimensions' => 'H 40 × W 16 × D 16 cm', 'weight' => '0.7 kg'],
            ['slug' => 'stillness-orb', 'name' => 'Stillness Orb', 'category' => 'spiritual-art', 'price' => 7900, 'badge' => 'Limited', 'material' => 'PLA, ivory finish on a printed pedestal', 'dimensions' => 'H 20 × Ø 14 cm', 'weight' => '0.6 kg'],
            ['slug' => 'serene-buddha', 'name' => 'Serene Buddha', 'category' => 'spiritual-art', 'price' => 12800, 'badge' => null, 'material' => 'PLA, matte sandstone finish', 'dimensions' => 'H 22 × W 18 × D 13 cm', 'weight' => '0.8 kg'],
            ['slug' => 'dancer-in-motion', 'name' => 'Dancer in Motion', 'category' => 'premium-figurines', 'price' => 10900, 'badge' => null, 'material' => 'PLA, painted bronze finish', 'dimensions' => 'H 32 × W 10 × D 10 cm', 'weight' => '0.4 kg'],
            ['slug' => 'contour-vase', 'name' => 'Contour Vase', 'category' => 'decorative-sculptures', 'price' => 8700, 'badge' => null, 'material' => 'PLA, warm bronze finish', 'dimensions' => 'H 26 × Ø 16 cm', 'weight' => '0.5 kg'],
            ['slug' => 'nataraja-cosmic-ring', 'name' => 'Nataraja Cosmic Ring', 'category' => 'hindu-deity-sculptures', 'price' => 22000, 'badge' => null, 'material' => 'PLA, graphite metallic finish', 'dimensions' => 'H 36 × W 32 × D 12 cm', 'weight' => '1.4 kg'],
            ['slug' => 'om-resonance', 'name' => 'Om Resonance', 'category' => 'spiritual-art', 'price' => 9600, 'badge' => null, 'material' => 'PLA, brushed bronze finish', 'dimensions' => 'H 28 × W 12 × D 12 cm', 'weight' => '0.5 kg'],
            ['slug' => 'bespoke-portrait-bust', 'name' => 'Bespoke Portrait Bust', 'category' => 'custom-3d-creations', 'price' => 24000, 'badge' => 'Made to order', 'material' => 'Modelled from your references; material and finish of your choice', 'dimensions' => 'Made to your specification', 'weight' => 'Varies'],
        ]);
    }

    /** Seed data: project areas from the client brief. */
    public static function sampleProjects(): array
    {
        return [
            // Application areas from the client brief; replace with real case studies once supplied.
            ['title' => 'Statues & Miniatures', 'category' => 'Creative Models', 'text' => 'Statues, sculptures, miniatures and display models, with painting available on selected pieces.', 'image' => 'assets/images/projects/heritage-relief.svg'],
            ['title' => 'Drone & RC Components', 'category' => 'Drones & RC', 'text' => 'Printed frames, mounts and custom components for drone and RC projects.', 'image' => 'assets/images/projects/survey-quad.svg'],
            ['title' => 'Parts Restoration', 'category' => 'Automotive & Restoration', 'text' => 'Broken or unavailable plastic parts recreated from a sample, measurements or photographs.', 'image' => 'assets/images/projects/enclosure.svg'],
            ['title' => 'Personalised Gifts', 'category' => 'Gifts & Decor', 'text' => 'Name-based and design-led gifts, keepsakes and desk accessories.', 'image' => 'assets/images/projects/vessel-series.svg'],
            ['title' => 'CAD Design & Modelling', 'category' => 'Engineering & Education', 'text' => 'Part modelling in SolidWorks and Fusion 360 for products, student projects and prototypes.', 'image' => 'assets/images/projects/portrait-bust.svg'],
            ['title' => 'Enclosures & Prototypes', 'category' => 'Product Development', 'text' => 'IoT enclosures, product housings and functional prototypes for fit and design iteration.', 'image' => 'assets/images/services/rapid-prototyping.svg'],
        ];
    }
}
