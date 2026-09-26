import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                // IBM Plex Sans untuk teks: figuranya tabular sehingga kolom angka di
                // tabel sejajar vertikal, dan karakternya administratif, bukan sans
                // generik. IBM Plex Mono hanya untuk identifier seperti ISBN dan
                // nomor anggota, supaya kode mudah dibandingkan antarbaris.
                bunny('IBM Plex Sans', {
                    weights: [400, 500, 600],
                }),
                bunny('IBM Plex Mono', {
                    weights: [400, 500],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
