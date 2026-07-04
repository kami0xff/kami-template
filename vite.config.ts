import { defineConfig } from 'vite';

// Plain Vite (no dev server / HMR). Run `npm run dev` (= `vite build --watch`)
// to rebuild static assets into public/build on change, and reference them in
// Blade via the vite_asset() helper. Output filenames are fixed (no hash);
// cache-busting is handled by vite_asset() appending a filemtime query.
export default defineConfig({
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: false,
        rollupOptions: {
            input: {
                app: 'resources/ts/app.ts',
            },
            output: {
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: 'assets/[name][extname]',
            },
        },
    },
});
