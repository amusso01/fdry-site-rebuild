import { resolve } from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
	css: {
		preprocessorOptions: {
			scss: {
				loadPaths: [resolve(__dirname, 'node_modules')],
			},
		},
	},
	build: {
		outDir: 'dist',
		emptyOutDir: true,
		manifest: true,
		rollupOptions: {
			input: resolve(__dirname, 'src/scripts/main.js'),
			output: {
				entryFileNames: 'fdry.[hash].js',
				assetFileNames: 'fdry.[hash][extname]',
			},
		},
	},
});
