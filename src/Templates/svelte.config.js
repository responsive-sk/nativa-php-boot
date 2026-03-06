import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

export default {
  preprocess: [vitePreprocess()],
  compilerOptions: {
    // Svelte 5 - enable runes
    runes: true
  }
};
