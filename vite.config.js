import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Cormorant Garamond', {
                    variable: '--ff-display',
                    weights: [300, 400, 500],
                    styles: ['normal', 'italic'],
                    preload: [{ weight: 400 }, { weight: 300 }],
                    fallbacks: ['Georgia', 'serif'],
                    optimizedFallbacks: false,
                }),
                bunny('Manrope', {
                    variable: '--ff-sans',
                    weights: [400, 500, 600],
                    preload: [{ weight: 400 }],
                    fallbacks: ['ui-sans-serif', 'system-ui', 'sans-serif'],
                    optimizedFallbacks: false,
                }),
                bunny('JetBrains Mono', {
                    variable: '--ff-mono',
                    weights: [400],
                    preload: false,
                    fallbacks: ['ui-monospace', 'monospace'],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
