import { defineConfig, loadEnv } from "vite";
import { resolve } from "path";
import compression from "vite-plugin-compression2";

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode || "development", process.cwd(), "");
  const isProd = mode === "production";
  const baseAssetUrl = env.VITE_ASSET_BASE || "/assets/admin/";

  return {
    root: "./vanilla/admin",
    base: baseAssetUrl,
    publicDir: '../../public',

    build: {
      cssCodeSplit: true,
      manifest: "admin-manifest.json",
      outDir: resolve(__dirname, "../../public/assets/admin"),
      emptyOutDir: true,

      // Target ES2017
      target: "es2017",

      // Production optimizations
      minify: isProd ? "esbuild" : false,
      sourcemap: !isProd,

      rollupOptions: {
        input: {
          // Admin Core
          'admin-core': resolve(__dirname, 'vanilla/admin/src/use-cases/admin.ts'),
          
          // Admin Pages (add as needed)
          // 'dashboard': resolve(__dirname, 'vanilla/admin/src/pages/dashboard.ts'),
          // 'articles': resolve(__dirname, 'vanilla/admin/src/pages/articles.ts'),
        },
        output: {
          entryFileNames: "[name].[hash].js",
          assetFileNames: (assetInfo) => {
            const name = assetInfo.name ?? "";

            if (name.endsWith(".css")) {
              return isProd ? "[name].[hash].css" : "[name].css";
            }

            if (name.match(/\.(woff2|woff|ttf|otf)$/)) {
              return "fonts/[name][extname]";
            }

            return isProd ? "[name]-[hash][extname]" : "[name][extname]";
          },
        },
      },
    },

    plugins: [
      // Compression
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
