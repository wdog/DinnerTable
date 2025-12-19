import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament/dinner/theme.css'],
            refresh: [...refreshPaths,
                "app/Livewire/**",
                "app/Filament/**",
                "app/Providers/Filament/**",
                "resources/views/**"
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: "0.0.0.0",
        hmr: {
            host: "localhost",
            https: true
        }
    },
});
