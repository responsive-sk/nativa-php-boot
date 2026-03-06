import { defineConfig, loadEnv } from "vite";
import { resolve } from "path";
import { svelte } from '@sveltejs/vite-plugin-svelte';
import compression from "vite-plugin-compression2";

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode || "development", process.cwd(), "");
  const isProd = mode === "production";
  const baseAssetUrl = env.VITE_ASSET_BASE || "/assets/svelte/";

  return {
    root: "./svelte/frontend",
    base: baseAssetUrl,
    publicDir: '../../public',

    plugins: [
      svelte(),
    ],

    build: {
      cssCodeSplit: true,
      manifest: "svelte-manifest.json",
      outDir: resolve(__dirname, "../../public/assets/svelte"),
      emptyOutDir: true,

      target: "es2017",
      minify: isProd ? "esbuild" : false,
      sourcemap: !isProd,

      rollupOptions: {
        input: {
          'navigation-enhance': resolve(__dirname, 'src/navigation-enhance.js'),
        },
        output: {
          entryFileNames: "[name].[hash].js",
          assetFileNames: (assetInfo) => {
            const name = assetInfo.name ?? "";

            if (name.endsWith(".css")) {
              return isProd ? "[name].[hash].css" : "[name].css";
            }

            return isProd ? "[name]-[hash][extname]" : "[name][extname]";
          },
        },
      },
    },

    plugins: [
      svelte(),
      compression({
        algorithms: ["gzip"],
        exclude: [/\.(br)$/, /\.(gz)$/],
        threshold: 1024,
      }),
      compression({
        algorithms: ["brotliCompress"],
        exclude: [/\.(br)$/, /\.(gz)$/],
        threshold: 1024,
      }),
    ],
  };
});
