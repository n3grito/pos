import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('chart.js') || id.includes('chartjs')) {
                        return 'vendor-charts';
                    }
                    if (id.includes('alpinejs')) {
                        return 'vendor-alpine';
                    }
                    if (id.includes('pusher-js') || id.includes('laravel-echo')) {
                        return 'vendor-echo';
                    }
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                },
            },
        },
        chunkSizeWarningLimit: 300,
    },
});
