import { defineConfig } from "vite";
import path from "path";

export default defineConfig({
    base: "./",
    esbuild: {
        charset: "ascii",
    },
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
                    const name = assetInfo.name || "";
                    if (name.endsWith(".css")) return "installer.css";
                    if (/\.(woff2?|ttf|eot|svg)$/.test(name)) return "[name][extname]";
                    return "[name]-[hash][extname]";
                },
            }
        }
    }
});