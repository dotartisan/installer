import { defineConfig } from "vite";
import path from "path";

export default defineConfig({
    build: {
        outDir: "resources/dist",
        emptyOutDir: true,
        rollupOptions: {
            input: {
                installer: path.resolve(__dirname, "resources/assets/js/app.js"),
                styles: path.resolve(__dirname, "resources/assets/sass/app.scss")
            },
            output: {
                entryFileNames: "installer.js",
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.endsWith(".css")) return "installer.css";
                    return "[name][extname]";
                }
            }
        }
    }
});