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
        outDir: './webroot',
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
        },
		port: 3005,
		strictPort: true,
    },
    /**
     * e.g. for css files from asyncronously loaded modules
     */
    base: '/assets/',
});
