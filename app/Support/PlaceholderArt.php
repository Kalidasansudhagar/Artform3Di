<?php

namespace App\Support;

/**
 * Generates on-brand SVG placeholder artwork until client photography is supplied.
 *
 * Every sculpture is drawn as stacked "print layers" so the placeholders already
 * speak the visual language of FDM printing. Output is written to
 * public/assets/images by `php artisan artform:placeholders`.
 */
class PlaceholderArt
{
    public const TONES = [
        'bronze' => ['bg1' => '#22201c', 'bg2' => '#0d0d0e', 'hi' => '#ecd7ad', 'mid' => '#a8895a', 'lo' => '#2e2418', 'line' => '#b59a6d'],
        'ivory' => ['bg1' => '#2b2926', 'bg2' => '#111110', 'hi' => '#fffaf0', 'mid' => '#d6cab4', 'lo' => '#5b5243', 'line' => '#d6cab4'],
        'graphite' => ['bg1' => '#26272a', 'bg2' => '#0e0e10', 'hi' => '#e3e5e8', 'mid' => '#7e838a', 'lo' => '#1f2124', 'line' => '#9aa3ab'],
        'stone' => ['bg1' => '#ece8e1', 'bg2' => '#d9d3c9', 'hi' => '#fdfcf9', 'mid' => '#b5ada0', 'lo' => '#5f584e', 'line' => '#8a8378'],
        'clay' => ['bg1' => '#e7e1d8', 'bg2' => '#cfc6b8', 'hi' => '#f6e7d6', 'mid' => '#b48a6a', 'lo' => '#4f3a2c', 'line' => '#8c7a5b'],
        'steel' => ['bg1' => '#161a1e', 'bg2' => '#0a0c0e', 'hi' => '#dfe6ec', 'mid' => '#7f8b96', 'lo' => '#1c2228', 'line' => '#8fa0ae'],
    ];

    private string $id;

    private array $c;

    public function render(string $variant, string $tone = 'bronze', int $w = 1200, int $h = 1500, int $seed = 1): string
    {
        $this->id = 'a'.substr(md5($variant.$tone.$w.$h.$seed), 0, 6);
        $this->c = self::TONES[$tone] ?? self::TONES['bronze'];

        $body = match ($variant) {
            'wireframe' => $this->wireframe($w, $h, $seed),
            'printer' => $this->printer($w, $h, $seed),
            'drone' => $this->drone($w, $h, $seed),
            'part' => $this->part($w, $h, $seed),
            default => $this->sculpture($variant, $w, $h, $seed),
        };

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d" preserveAspectRatio="xMidYMid slice">%s%s</svg>',
            $w, $h, $w, $h, $this->defs($w, $h), $body
        );
    }

    /** Half-width profile (0..1) and horizontal offset for a normalised height t (0 = base, 1 = top). */
    public static function profile(string $variant, float $t, int $seed = 1): array
    {
        $ell = fn ($c, $r, $a) => abs($t - $c) < $r ? $a * sqrt(1 - (($t - $c) / $r) ** 2) : 0;
        $band = fn ($a, $b, $v) => ($t >= $a && $t <= $b) ? $v : 0;
        $s = $seed * 0.7;

        return match ($variant) {
            'vase' => [max(
                $band(0, 0.05, 0.42),
                0.28 + 0.55 * exp(-((($t - 0.36 - 0.03 * sin($s)) / 0.24) ** 2)) + 0.2 * exp(-((($t - 0.97) / 0.045) ** 2))
            ), 0],
            'bust' => [max(
                $band(0, 0.1, 0.46), $band(0.1, 0.14, 0.34),
                $ell(0.36, 0.23, 0.86) * ($t > 0.14 ? 1 : 0),
                $band(0.5, 0.64, 0.2),
                $ell(0.8, 0.19, 0.36)
            ), 0],
            'deity' => [max(
                $band(0, 0.13, 0.62 + 0.14 * abs(sin($t * 70))),
                $ell(0.24, 0.12, 0.86),
                $ell(0.47, 0.19, 0.46),
                $band(0.6, 0.68, 0.16),
                $ell(0.75, 0.085, 0.22),
                $t > 0.82 && $t <= 0.99 ? 0.17 * (1 - ($t - 0.82) / 0.17) : 0
            ), 0],
            'figurine' => [max(
                $band(0, 0.05, 0.5),
                $band(0.05, 0.42, 0.13 + 0.05 * sin($t * 9)),
                $ell(0.5, 0.1, 0.25),
                $ell(0.66, 0.1, 0.27),
                $band(0.74, 0.79, 0.07),
                $ell(0.87, 0.085, 0.14)
            ), 0.06 * sin($t * 4 + $s)],
            'twist' => [max($band(0, 0.04, 0.4), 0.3 + 0.18 * sin($t * M_PI * 3 + $s)), 0.22 * sin($t * M_PI * 2 + $s) * ($t > 0.04 ? 1 : 0)],
            'orb' => [max($band(0, 0.04, 0.42), $band(0.04, 0.26, 0.14), $ell(0.62, 0.37, 0.76)), 0],
            default => [0.4, 0],
        };
    }

    private function defs(int $w, int $h): string
    {
        $c = $this->c;
        $id = $this->id;

        return <<<SVG
<defs>
<radialGradient id="{$id}bg" cx="50%" cy="38%" r="75%"><stop offset="0" stop-color="{$c['bg1']}"/><stop offset="1" stop-color="{$c['bg2']}"/></radialGradient>
<linearGradient id="{$id}m" x1="0" x2="1" y1="0" y2="0"><stop offset="0" stop-color="{$c['lo']}"/><stop offset=".28" stop-color="{$c['mid']}"/><stop offset=".42" stop-color="{$c['hi']}"/><stop offset=".62" stop-color="{$c['mid']}"/><stop offset="1" stop-color="{$c['lo']}"/></linearGradient>
<radialGradient id="{$id}glow" cx="50%" cy="50%" r="50%"><stop offset="0" stop-color="{$c['hi']}" stop-opacity=".22"/><stop offset="1" stop-color="{$c['hi']}" stop-opacity="0"/></radialGradient>
<radialGradient id="{$id}sh" cx="50%" cy="50%" r="50%"><stop offset="0" stop-color="#000" stop-opacity=".45"/><stop offset="1" stop-color="#000" stop-opacity="0"/></radialGradient>
<pattern id="{$id}grid" width="48" height="48" patternUnits="userSpaceOnUse"><path d="M48 0H0V48" fill="none" stroke="{$c['line']}" stroke-opacity=".09" stroke-width="1"/></pattern>
</defs>
<rect width="{$w}" height="{$h}" fill="url(#{$id}bg)"/>
SVG;
    }

    private function layers(string $variant, float $cx, float $baseY, float $height, float $maxHalf, int $count, int $seed, float $progress = 1): string
    {
        $out = '';
        $lh = $height / $count;
        $limit = (int) floor($count * $progress);

        for ($i = 0; $i < $limit; $i++) {
            $t = $i / ($count - 1);
            [$half, $dx] = self::profile($variant, $t, $seed);
            $width = $half * $maxHalf * 2;
            if ($width < 2) {
                continue;
            }
            $x = $cx + $dx * $maxHalf - $width / 2;
            $y = $baseY - ($i + 1) * $lh;
            $out .= sprintf('<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" rx="%.1f" fill="url(#%sm)"/>', $x, $y, $width, $lh * 0.74, $lh * 0.37, $this->id);
        }

        return $out;
    }

    private function sculpture(string $variant, int $w, int $h, int $seed): string
    {
        $id = $this->id;
        $c = $this->c;
        $size = min($w, $h * 0.8);
        $cx = $w / 2;
        $baseY = $h * 0.84;
        $height = min($h * 0.66, $size * 0.95);
        $maxHalf = $size * 0.3;

        $halo = '';
        if ($variant === 'deity') {
            $hy = $baseY - $height * 0.76;
            $r = $height * 0.24;
            $halo = sprintf(
                '<circle cx="%.1f" cy="%.1f" r="%.1f" fill="none" stroke="%s" stroke-opacity=".55" stroke-width="%.1f" stroke-dasharray="2 7"/><circle cx="%.1f" cy="%.1f" r="%.1f" fill="none" stroke="%s" stroke-opacity=".35" stroke-width="1.5"/>',
                $cx, $hy, $r, $c['hi'], $size * 0.012, $cx, $hy, $r * 1.18, $c['line']
            );
        }

        return sprintf('<rect width="%d" height="%d" fill="url(#%sgrid)" opacity=".6"/>', $w, $h, $id)
            .sprintf('<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="url(#%sglow)"/>', $cx, $baseY - $height * 0.5, $size * 0.62, $height * 0.62, $id)
            .$halo
            .sprintf('<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="url(#%ssh)"/>', $cx, $baseY + 4, $maxHalf * 1.25, $size * 0.035, $id)
            .$this->layers($variant, $cx, $baseY, $height, $maxHalf, 92, $seed);
    }

    private function wireframe(int $w, int $h, int $seed): string
    {
        $c = $this->c;
        $variant = ['bust', 'vase', 'deity', 'twist'][$seed % 4];
        $cx = $w / 2;
        $baseY = $h * 0.84;
        $height = min($h * 0.66, $w * 0.8);
        $maxHalf = min($w, $h) * 0.28;
        $out = sprintf('<rect width="%d" height="%d" fill="url(#%sgrid)"/>', $w, $h, $this->id);

        // Meridians: the profile projected at several rotation angles.
        foreach ([1, 0.866, 0.5, 0.0, -0.5, -0.866, -1] as $k) {
            $pts = [];
            for ($i = 0; $i <= 60; $i++) {
                $t = $i / 60;
                [$half, $dx] = self::profile($variant, $t, $seed);
                $pts[] = sprintf('%.1f,%.1f', $cx + ($dx + $half * $k) * $maxHalf, $baseY - $t * $height);
            }
            $out .= sprintf('<polyline points="%s" fill="none" stroke="%s" stroke-opacity="%.2f" stroke-width="1.2"/>', implode(' ', $pts), $c['hi'], abs($k) === 1.0 ? 0.9 : 0.35);
        }

        // Rings.
        for ($i = 1; $i < 24; $i++) {
            $t = $i / 24;
            [$half, $dx] = self::profile($variant, $t, $seed);
            if ($half <= 0.01) {
                continue;
            }
            $out .= sprintf('<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="none" stroke="%s" stroke-opacity=".4" stroke-width="1"/>', $cx + $dx * $maxHalf, $baseY - $t * $height, $half * $maxHalf, $half * $maxHalf * 0.16, $c['hi']);
        }

        // Viewport chrome.
        $m = 36;
        $out .= sprintf('<path d="M%1$d %2$dh40M%1$d %2$dv40M%3$d %2$dh-40M%3$d %2$dv40M%1$d %4$dh40M%1$d %4$dv-40M%3$d %4$dh-40M%3$d %4$dv-40" stroke="%5$s" stroke-opacity=".6" stroke-width="1.5" fill="none"/>', $m, $m, $w - $m, $h - $m, $c['line']);
        $out .= sprintf('<text x="%d" y="%d" fill="%s" fill-opacity=".7" font-family="monospace" font-size="%d" letter-spacing="3">PERSPECTIVE · MESH</text>', $m + 16, $m + 34, $c['line'], max(12, (int) ($w / 70)));

        return $out;
    }

    private function printer(int $w, int $h, int $seed): string
    {
        $c = $this->c;
        $id = $this->id;
        $variant = ['bust', 'deity', 'vase', 'twist'][$seed % 4];
        $progress = [0.62, 0.74, 0.55, 0.8][$seed % 4];
        $cx = $w / 2;
        $plateY = $h * 0.8;
        $frameW = min($w * 0.62, $h * 0.95);
        $left = $cx - $frameW / 2;
        $right = $cx + $frameW / 2;
        $top = $h * 0.1;
        $height = ($plateY - $top) * 0.68;
        $maxHalf = $frameW * 0.22;
        $count = 80;

        $nozzleY = $plateY - $height * $progress - 6;
        [$half, $dx] = self::profile($variant, $progress, $seed);
        $nozzleX = $cx + ($dx + $half * 0.6) * $maxHalf;

        $out = sprintf('<rect width="%d" height="%d" fill="url(#%sgrid)" opacity=".5"/>', $w, $h, $id);
        $out .= sprintf('<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="url(#%sglow)"/>', $nozzleX, $nozzleY, $frameW * 0.5, $frameW * 0.36, $id);
        // Frame posts and gantry.
        $out .= sprintf('<rect x="%.1f" y="%.1f" width="18" height="%.1f" fill="%s" opacity=".85"/><rect x="%.1f" y="%.1f" width="18" height="%.1f" fill="%s" opacity=".85"/>', $left, $top, $plateY - $top + 40, $c['lo'], $right - 18, $top, $plateY - $top + 40, $c['lo']);
        $out .= sprintf('<rect x="%.1f" y="%.1f" width="%.1f" height="14" fill="%s" opacity=".9"/>', $left, $top, $frameW, $c['lo']);
        $out .= sprintf('<rect x="%.1f" y="%.1f" width="%.1f" height="5" fill="%s" opacity=".7"/>', $left + 18, $nozzleY - 64, $frameW - 36, $c['mid']);
        // Build plate.
        $out .= sprintf('<rect x="%.1f" y="%.1f" width="%.1f" height="12" rx="3" fill="%s"/><rect x="%.1f" y="%.1f" width="%.1f" height="2" fill="%s" opacity=".6"/>', $left + 30, $plateY, $frameW - 60, $c['lo'], $left + 30, $plateY, $frameW - 60, $c['hi']);
        $out .= $this->layers($variant, $cx, $plateY, $height, $maxHalf, $count, $seed, $progress);
        // Print head + nozzle glow.
        $out .= sprintf('<rect x="%.1f" y="%.1f" width="72" height="54" rx="6" fill="%s"/><rect x="%.1f" y="%.1f" width="72" height="3" fill="%s" opacity=".7"/>', $nozzleX - 36, $nozzleY - 80, $c['lo'], $nozzleX - 36, $nozzleY - 80, $c['mid']);
        $out .= sprintf('<path d="M%.1f %.1fl-9 18h18z" fill="%s"/>', $nozzleX, $nozzleY - 8, $c['mid']);
        $out .= sprintf('<circle cx="%.1f" cy="%.1f" r="4" fill="%s"/><circle cx="%.1f" cy="%.1f" r="22" fill="%s" opacity=".18"/>', $nozzleX, $nozzleY + 10, $c['hi'], $nozzleX, $nozzleY + 10, $c['hi']);
        $out .= sprintf('<path d="M%.1f %.1fC%.1f %.1f %.1f %.1f %.1f 0" stroke="%s" stroke-width="2" fill="none" opacity=".5"/>', $nozzleX, $nozzleY - 80, $nozzleX, $top - 40, $right, $top + 30, $right + 60, $c['mid']);

        return $out;
    }

    private function drone(int $w, int $h, int $seed): string
    {
        $c = $this->c;
        $arms = $seed % 3 === 0 ? 6 : 4;
        $cx = $w / 2;
        $cy = $h / 2;
        $span = min($w, $h) * 0.34;
        $rotor = $span * ($arms === 6 ? 0.32 : 0.42);
        $angle0 = $arms === 6 ? 0 : 45;
        $line = $c['line'];

        $out = sprintf('<rect width="%d" height="%d" fill="url(#%sgrid)"/>', $w, $h, $this->id);
        $out .= sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="none" stroke="%s" stroke-opacity=".18" stroke-dasharray="4 10"/>', $cx, $cy, $span + $rotor + 30, $line);
        $out .= sprintf('<path d="M%1$.1f %2$.1fH%3$.1fM%4$.1f %5$.1fV%6$.1f" stroke="%7$s" stroke-opacity=".25"/>', $cx - $span - $rotor - 60, $cy, $cx + $span + $rotor + 60, $cx, $cy - $span - $rotor - 60, $cy + $span + $rotor + 60, $line);

        for ($i = 0; $i < $arms; $i++) {
            $a = deg2rad($angle0 + $i * 360 / $arms);
            $x = $cx + cos($a) * $span;
            $y = $cy + sin($a) * $span;
            $out .= sprintf('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="%s" stroke-width="%.1f" stroke-linecap="round"/>', $cx, $cy, $x, $y, $c['mid'], $span * 0.07);
            $out .= sprintf('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="%s" stroke-width="2" stroke-linecap="round" opacity=".6"/>', $cx, $cy, $x, $y, $c['hi']);
            $out .= sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s" fill-opacity=".08" stroke="%s" stroke-opacity=".55" stroke-width="1.5"/>', $x, $y, $rotor, $c['hi'], $c['hi']);
            $out .= sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="none" stroke="%s" stroke-opacity=".3" stroke-dasharray="3 6"/>', $x, $y, $rotor * 1.12, $line);
            $out .= sprintf('<g transform="rotate(%d %.1f %.1f)"><rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" rx="%.1f" fill="%s" opacity=".75"/></g>', 25 * $i + $seed * 13, $x, $y, $x - $rotor * 0.95, $y - 5, $rotor * 1.9, 10, 5, $c['mid']);
            $out .= sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s" stroke="%s" stroke-width="2"/>', $x, $y, $span * 0.07, $c['lo'], $c['hi']);
            $label = sprintf('M%02d', $i + 1);
            $out .= sprintf('<text x="%.1f" y="%.1f" fill="%s" fill-opacity=".75" font-family="monospace" font-size="%d" letter-spacing="2" text-anchor="middle">%s</text>', $x, $y + $rotor + 34, $line, max(11, (int) ($span / 16)), $label);
        }

        // Fuselage.
        $out .= sprintf('<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" rx="%.1f" fill="%s" stroke="%s" stroke-width="2"/>', $cx - $span * 0.24, $cy - $span * 0.32, $span * 0.48, $span * 0.64, $span * 0.12, $c['lo'], $c['mid']);
        $out .= sprintf('<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" rx="%.1f" fill="url(#%sm)" opacity=".85"/>', $cx - $span * 0.15, $cy - $span * 0.2, $span * 0.3, $span * 0.4, $span * 0.06, $this->id);
        $out .= sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s"/><circle cx="%.1f" cy="%.1f" r="%.1f" fill="none" stroke="%s" stroke-opacity=".5"/>', $cx, $cy - $span * 0.27, 6, $c['hi'], $cx, $cy - $span * 0.27, 16, $c['hi']);

        // Dimension line.
        $dy = $cy + $span + $rotor + 70;
        if ($dy < $h - 30) {
            $x1 = $cx - $span;
            $x2 = $cx + $span;
            $out .= sprintf('<path d="M%1$.1f %2$.1fH%3$.1fM%1$.1f %4$.1fV%5$.1fM%3$.1f %4$.1fV%5$.1f" stroke="%6$s" stroke-opacity=".55"/>', $x1, $dy, $x2, $dy - 10, $dy + 10, $line);
            $out .= sprintf('<text x="%.1f" y="%.1f" fill="%s" fill-opacity=".7" font-family="monospace" font-size="%d" letter-spacing="3" text-anchor="middle">AXIS SPAN</text>', $cx, $dy - 16, $line, max(11, (int) ($span / 16)));
        }

        return $out;
    }

    private function part(int $w, int $h, int $seed): string
    {
        $c = $this->c;
        $cx = $w / 2;
        $cy = $h * 0.52;
        $r = min($w, $h) * 0.27;
        $teeth = 10 + ($seed % 3) * 2;
        $depth = 26;

        $gear = function (float $ox, float $oy) use ($teeth, $r, $cx, $cy) {
            $pts = [];
            for ($i = 0; $i < $teeth * 4; $i++) {
                $a = 2 * M_PI * $i / ($teeth * 4);
                $rr = ($i % 4 < 2) ? $r : $r * 0.84;
                $pts[] = sprintf('%.1f,%.1f', $cx + $ox + cos($a) * $rr, $cy + $oy + sin($a) * $rr * 0.58);
            }

            return implode(' ', $pts);
        };

        $out = sprintf('<rect width="%d" height="%d" fill="url(#%sgrid)" opacity=".7"/>', $w, $h, $this->id);
        $out .= sprintf('<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="url(#%sglow)"/>', $cx, $cy, $r * 1.8, $r * 1.4, $this->id);
        $out .= sprintf('<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="url(#%ssh)"/>', $cx, $cy + $depth + $r * 0.5, $r * 1.15, $r * 0.25, $this->id);
        for ($i = $depth; $i > 0; $i -= 2) {
            $out .= sprintf('<polygon points="%s" fill="%s" stroke="%s" stroke-opacity=".25"/>', $gear(0, $i), $c['lo'], $c['mid']);
        }
        $out .= sprintf('<polygon points="%s" fill="url(#%sm)"/>', $gear(0, 0), $this->id);
        $out .= sprintf('<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="%s" stroke="%s" stroke-width="2"/>', $cx, $cy, $r * 0.32, $r * 0.32 * 0.58, $c['bg2'], $c['mid']);
        for ($i = 0; $i < 6; $i++) {
            $a = 2 * M_PI * $i / 6;
            $out .= sprintf('<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="%s" opacity=".85"/>', $cx + cos($a) * $r * 0.58, $cy + sin($a) * $r * 0.58 * 0.58, $r * 0.07, $r * 0.04, $c['lo']);
        }

        return $out;
    }
}
