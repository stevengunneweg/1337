import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import vueDevTools from 'vite-plugin-vue-devtools';
import svgLoader from 'vite-svg-loader';

// https://vite.dev/config/
export default defineConfig({
	plugins: [
		vue(),
		vueDevTools(),
		svgLoader({ defaultImport: 'url' }),
		viteStaticCopy({
			targets: [
				{
					src: 'manifest.json',
					dest: './',
				},
				{
					src: 'robots.txt',
					dest: './',
				},
				{
					src: 'api/',
					dest: './',
				},
				{
					src: '../db.php',
					dest: './',
				},
			],
		}),
	],
	resolve: {
		alias: {
			'@': fileURLToPath(new URL('./src', import.meta.url)),
		},
	},
	server: {
		watch: {
			ignored: ['**/api/**'],
		},
	},
});
