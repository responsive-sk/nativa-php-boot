import { defineConfig } from "vitest/config";
import { resolve } from "path";

export default defineConfig({
  test: {
    globals: true,
    environment: "jsdom",
    setupFiles: ["./tests/setup.ts"],
    include: ["src/**/*.test.ts"],
    coverage: {
      provider: "v8",
      reporter: ["text", "json", "html"],
      include: ["src/**/*.ts"],
      exclude: [
        "src/**/*.d.ts",
        "src/types/**",
        "src/vendors/**",
        "**/*.config.ts",
      ],
    },
  },
  resolve: {
    alias: {
      "@": resolve(__dirname, "src"),
      "@components": resolve(__dirname, "src/components"),
      "@storage": resolve(__dirname, "src/storage"),
      "@ui": resolve(__dirname, "src/ui"),
      "@effects": resolve(__dirname, "src/effects"),
      "@navigation": resolve(__dirname, "src/navigation"),
      "@forms": resolve(__dirname, "src/forms"),
      "@core": resolve(__dirname, "src/core"),
    },
  },
});
