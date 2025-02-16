import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    base: './',
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});

// export default defineConfig({
//     base: '/',
//     server: {
//         host: 'localhost',
//         port: 5173,
//         strictPort: true,
//         hmr: {
//             protocol: 'ws',
//             host: 'localhost'
//         },
//         cors: {
//             origin: `http://${process.env.VITE_APP_ONION_DOMAIN}`,
//             credentials: true
//         }
//     },
//     plugins: [
//         laravel({
//             input: 'resources/js/app.js',
//             refresh: {
//                 paths: ['resources/views/**'],
//                 delay: 200
//             },
//         }),
//         vue({
//             template: {
//                 transformAssetUrls: {
//                     base: process.env.VITE_APP_URL,
//                     includeAbsolute: false,
//                 },
//             },
//         }),
//     ]
// });
