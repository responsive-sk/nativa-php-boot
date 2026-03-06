import { defineConfig, loadEnv } from "vite";
import { resolve } from "path";
import fs from "fs";
import path from "path";
import compression from "vite-plugin-compression2";
import { svelte } from '@sveltejs/vite-plugin-svelte';

// Helper na rekurzívne kopírovanie
function copyRecursiveSync(src: string, dest: string) {
  if (!fs.existsSync(src)) {
    console.warn(`[copy-assets] Source not found: ${src}`);
    return;
  }
  fs.mkdirSync(dest, { recursive: true });
  for (const entry of fs.readdirSync(src, { withFileTypes: true })) {
    const srcPath = path.join(src, entry.name);
    const destPath = path.join(dest, entry.name);
    if (entry.isDirectory()) {
      copyRecursiveSync(srcPath, destPath);
    } else {
      fs.copyFileSync(srcPath, destPath);
    }
  }
}

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode || "development", process.cwd(), "");
  const isProd = mode === "production";
  const baseAssetUrl = env.VITE_ASSET_BASE || "/assets/";

  return {
    root: "./src",
    base: baseAssetUrl,
    publicDir: 'public',

    build: {
      cssCodeSplit: true,
      manifest: "manifest.json",
      outDir: resolve(__dirname, "../../public/assets"),
      emptyOutDir: true,
      copyPublicDir: true,

      // Target ES2017 for Android 8 Chrome compatibility (Chrome 60+)
      // Android 8 ships with Chrome 60 which supports ES2017
      target: "es2017",

      // Production optimizations
      minify: isProd ? "esbuild" : false,
      sourcemap: !isProd,

      // CSS specific optimizations
      cssMinify: isProd,

      rollupOptions: {
        input: {
          // Core - always loaded
          'core-init': resolve(__dirname, 'src/init.js'),
          'core-app': resolve(__dirname, 'src/app.ts'),
          'core-css': resolve(__dirname, 'src/css.ts'),

          // Feature modules - loaded per page
          'admin': resolve(__dirname, 'src/frontend/use-cases/admin/admin.ts'),
          'auth': resolve(__dirname, 'src/frontend/use-cases/auth/auth.ts'),
          'home': resolve(__dirname, 'src/frontend/pages/home.ts'),
          'blog': resolve(__dirname, 'src/frontend/pages/blog.ts'),
          'articles': resolve(__dirname, 'src/frontend/pages/articles.ts'),
          'contact': resolve(__dirname, 'src/frontend/pages/contact.ts'),
          'about': resolve(__dirname, 'src/frontend/pages/about.ts'),
          'portfolio': resolve(__dirname, 'src/frontend/pages/portfolio.ts'),
          'services': resolve(__dirname, 'src/frontend/pages/services.ts'),
          'pricing': resolve(__dirname, 'src/frontend/pages/pricing.ts'),
          'docs': resolve(__dirname, 'src/frontend/pages/docs.ts'),

          // Svelte components (direct entry points)
          'article-list': resolve(__dirname, 'src/article-list.js'),
          'contact-form': resolve(__dirname, 'src/contact-form.js'),
          'theme-toggle': resolve(__dirname, 'src/theme-toggle.js'),
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
            if (name.endsWith(".svg")) {
              if (name.endsWith("/logo.svg") || name === "logo.svg") {
                return "images/[name][extname]";
              }
              return "images/icons/[name][extname]";
            }

            return isProd
              ? "[name]-[hash][extname]"
              : "[name][extname]";
          },
          manualChunks(id) {
            // Minimal vendor splitting - only essential libs
            if (id.includes('node_modules')) {
              // Everything goes to single vendor chunk
              // We minimize 3rd party deps for maximum performance
              return 'vendor';
            }
          },
        },
        treeshake: isProd ? "smallest" : false,
      },

      assetsInlineLimit: 0,
    },

    plugins: [
      // Svelte plugin
      svelte(),

      // Copy ONLY critical fonts and images after build
      {
        name: 'copy-assets',
        closeBundle() {
          const outDir = resolve(__dirname, '../../public/assets');

          // Essential fonts only (referenced in CSS) - saves ~62KB
          const essentialFonts = [
            'sans-serif/font-sans-web.woff2',
            'serif/font-serif-web.woff2',
          ];

          const srcFontsDir = resolve(__dirname, 'src/assets/fonts');
          const destFontsDir = path.join(outDir, 'fonts');

          // Copy only essential fonts
          let copied = 0;
          for (const fontPath of essentialFonts) {
            const srcPath = path.join(srcFontsDir, fontPath);
            const destPath = path.join(destFontsDir, fontPath);
            
            if (fs.existsSync(srcPath)) {
              // Ensure directory exists
              const destDir = path.dirname(destPath);
              if (!fs.existsSync(destDir)) {
                fs.mkdirSync(destDir, { recursive: true });
              }
              
              fs.copyFileSync(srcPath, destPath);
              console.log(`[copy-assets] Copied font: ${fontPath}`);
              copied++;
            }
          }
          
          console.log(`[copy-assets] Copied ${copied}/${essentialFonts.length} essential fonts (${(copied/essentialFonts.length*100).toFixed(0)}%)`);

          // Copy all images
          const srcImagesDir = resolve(__dirname, 'src/assets/images');
          const destImagesDir = path.join(outDir, 'images');
          
          if (fs.existsSync(srcImagesDir)) {
            copyRecursiveSync(srcImagesDir, destImagesDir);
            console.log(`[copy-assets] Copied images: ${srcImagesDir} → ${destImagesDir}`);
          }
        }
      },
      
      // Gzip compression
      compression({
        algorithms: ["gzip"],
        exclude: [/\.(br)$/, /\.(gz)$/],
        threshold: 1024,
      }),
      // Brotli compression
      compression({
        algorithms: ["brotliCompress"],
        exclude: [/\.(br)$/, /\.(gz)$/],
        threshold: 1024,
      }),
    ],

    css: {
      lightningcss: isProd ? {
        targets: {
          chrome: 90,
          firefox: 90,
          safari: 15,
        },
        minify: true,
      } : undefined,
    },

    resolve: {
      alias: {
        "@": resolve(__dirname, "src"),
        "@components": resolve(__dirname, "src/components"),
        "@styles": resolve(__dirname, "src/styles"),
        "@core": resolve(__dirname, "src/core"),
        "@ui": resolve(__dirname, "src/ui"),
        "@effects": resolve(__dirname, "src/effects"),
        "@navigation": resolve(__dirname, "src/navigation"),
        "@forms": resolve(__dirname, "src/forms"),
        "@storage": resolve(__dirname, "src/storage"),
      },
    },

    server: {
      port: 3000,
      open: false,
    },

    optimizeDeps: {
      include: ["lit", "htmx.org"],
      exclude: ["svelte"],
    },
  };
});
