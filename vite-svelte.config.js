import { defineConfig } from 'vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';

export default defineConfig({
  plugins: [svelte()],
  root: 'src/Templates',
  base: '/assets/',
  build: {
    manifest: true,
    outDir: '../../public/assets',
    rollupOptions: {
      input: {
        'article-list': 'src/Templates/svelte/components/ArticleList.svelte',
        'contact-form': 'src/Templates/svelte/components/ContactForm.svelte',
        'theme-toggle': 'src/Templates/svelte/components/ThemeToggle.svelte',
      },
      output: {
        entryFileNames: '[name].[hash].js',
        chunkFileNames: '[name].[hash].js',
        assetFileNames: '[name].[hash].[ext]'
      }
    }
  }
});
