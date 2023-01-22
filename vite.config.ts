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
        emptyOutDir: true,
        outDir: './webroot/dist',
        manifest: true,
        rollupOptions: {
            input: {
				uploadField: './webroot_src/components/UploadField/initUploadField.ts',
			},
        },
    },
    server: {
        hmr: {
            protocol: 'ws',
            host: 'localhost',
            port: 3000,
        },
    },
    /**
     * e.g. for css files from asyncronously loaded modules
     */
    base: '/assets/',
});
