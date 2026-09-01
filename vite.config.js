import { createHash } from 'crypto';
import { existsSync, readFileSync, writeFileSync } from 'fs';
import { resolve } from 'path';
import { defineConfig } from 'vite';

function fdryManifest() {
	return {
		name: 'fdry-manifest',
		closeBundle() {
			const dist = resolve(__dirname, 'dist');
			const jsPath = resolve(dist, 'fdry.js');
			const cssPath = resolve(dist, 'fdry.css');

			if (!existsSync(jsPath)) {
				return;
			}

			const js = readFileSync(jsPath);
			const css = existsSync(cssPath) ? readFileSync(cssPath) : Buffer.alloc(0);
			const version = createHash('sha256').update(js).update(css).digest('hex').slice(0, 8);

			writeFileSync(
				resolve(dist, 'manifest.json'),
				`${JSON.stringify(
					{
						js: 'fdry.js',
						css: existsSync(cssPath) ? 'fdry.css' : null,
						version,
					},
					null,
					2
				)}\n`
			);
		},
	};
}

export default defineConfig({
	plugins: [fdryManifest()],
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
		rollupOptions: {
			input: resolve(__dirname, 'src/scripts/main.js'),
			output: {
				entryFileNames: 'fdry.js',
				assetFileNames: 'fdry[extname]',
			},
		},
	},
});
