import { defineConfig } from 'vite';
import legacy from '@vitejs/plugin-legacy';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        legacy({
            targets: ['defaults', 'not IE 11'],
        }),
        vue(),
    ],
    build: {
        emptyOutDir: false,
        outDir: './webroot/',
        assetsDir: 'build',
        manifest: true,
        rollupOptions: {
            input: './webroot_src/main.ts',
        },
    },
    server: {
        hmr: {
            protocol: 'ws',
            host: 'localhost',
            port: 3000,
        },
    },
});
