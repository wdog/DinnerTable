import { defineConfig, loadEnv } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig(({ mode }) => {
    // Load env file based on `mode` in the current working directory.
    const env = loadEnv(mode, process.cwd(), '');
    const hmrHost = env.VITE_HMR_HOST || 'localhost';

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                    'resources/css/filament/dinner/theme.css'],
                refresh: [...refreshPaths,
                    "app/Livewire/**",
                    "app/Filament/**",
                    "app/Providers/Filament/**",
                    "resources/views/**"
                ],
                // Set the dev server URL for the hot file
                // valetTls: false,
                detectTls: false,
            }),
            tailwindcss(),
        ],
        server: {
            host: "0.0.0.0",
            port: 5173,
            strictPort: true,
            hmr: {
                protocol: 'ws',
                clientPort: 5173,
                host: hmrHost,
            },
            watch: {
                // usePolling: true,
            }
        },
    };
});
