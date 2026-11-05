<?php

namespace App\Console\Commands;

use App\Support\Catalog;
use App\Support\PlaceholderArt;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('artform:placeholders')]
#[Description('Generate on-brand SVG placeholder artwork into public/assets/images')]
class GeneratePlaceholders extends Command
{
    public function handle(PlaceholderArt $art): int
    {
        $files = [
            'hero/hero-poster.svg' => ['printer', 'bronze', 1920, 1080, 1],
            'home/intro-studio.svg' => ['printer', 'ivory', 1000, 1300, 2],
            'home/intro-detail.svg' => ['twist', 'bronze', 900, 900, 3],
            'services/rapid-prototyping.svg' => ['part', 'graphite', 1400, 1000, 1],
            'services/custom-modelling.svg' => ['wireframe', 'graphite', 1400, 1000, 1],
            'services/art-decor.svg' => ['vase', 'bronze', 1400, 1000, 2],
            'services/hero.svg' => ['printer', 'graphite', 1920, 1100, 3],
            'process/concept.svg' => ['wireframe', 'ivory', 900, 700, 2],
            'process/modelling.svg' => ['wireframe', 'graphite', 900, 700, 3],
            'process/printing.svg' => ['printer', 'graphite', 900, 700, 1],
            'process/final.svg' => ['deity', 'bronze', 900, 700, 1],
            'drones/hero.svg' => ['drone', 'steel', 1920, 1200, 1],
            'drones/manufacturing.svg' => ['part', 'steel', 1200, 1200, 2],
            'drones/prototyping.svg' => ['drone', 'steel', 1200, 1200, 2],
            'drones/research.svg' => ['wireframe', 'steel', 1200, 1200, 0],
            'drones/blueprint.svg' => ['drone', 'steel', 1600, 1600, 3],
            'drones/home-feature.svg' => ['drone', 'steel', 1400, 1400, 4],
            'about/studio.svg' => ['printer', 'ivory', 1600, 1100, 0],
            'about/craft.svg' => ['bust', 'stone', 1000, 1300, 1],
            'about/technology.svg' => ['wireframe', 'graphite', 1000, 1300, 1],
            'collections/divine.svg' => ['deity', 'bronze', 1200, 1500, 5],
            'collections/living-forms.svg' => ['twist', 'stone', 1200, 1500, 4],
            'collections/bespoke.svg' => ['bust', 'graphite', 1200, 1500, 3],
            'projects/heritage-relief.svg' => ['deity', 'stone', 1400, 1000, 6],
            'projects/survey-quad.svg' => ['drone', 'steel', 1400, 1000, 1],
            'projects/enclosure.svg' => ['part', 'graphite', 1400, 1000, 3],
            'projects/vessel-series.svg' => ['vase', 'clay', 1400, 1000, 4],
            'projects/portrait-bust.svg' => ['wireframe', 'graphite', 1400, 1000, 0],
            'projects/hexacopter.svg' => ['drone', 'steel', 1400, 1000, 3],
        ];

        foreach (Catalog::ART as $slug => [$variant, $tone, $alt, $seed]) {
            $files["products/{$slug}-1.svg"] = [$variant, $tone, 1200, 1500, $seed];
            $files["products/{$slug}-2.svg"] = [$variant, $alt, 1200, 1500, $seed + 1];
            $files["products/{$slug}-3.svg"] = ['wireframe', 'graphite', 1200, 1500, $seed];
        }

        foreach ($files as $path => $args) {
            $target = public_path('assets/images/'.$path);
            File::ensureDirectoryExists(dirname($target));
            File::put($target, $art->render(...$args));
        }

        $this->components->info(count($files).' placeholder images written to public/assets/images.');

        return self::SUCCESS;
    }
}
