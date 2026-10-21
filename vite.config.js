import { resolve } from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
	// Relative, so asset URLs in the CSS (the font files) resolve next to it
	// in dist/ rather than from the site root.
	base: './',
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
