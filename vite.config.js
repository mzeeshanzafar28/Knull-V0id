import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import dotenv from 'dotenv';
dotenv.config();

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

//configuration for tor - uncomment to host over tor, and comment the above


// export default defineConfig({
//     server: {
//         host: '0.0.0.0',
//         port: 5173,
//         strictPort: true,
//         allowedHosts: [
//             process.env.VITE_APP_ONION_DOMAIN,
//         ],
//         hmr: {
//             protocol: 'ws',
//             host: process.env.VITE_APP_ONION_DOMAIN,
//             clientPort: 5173,
//             timeout: 30000

//         },
//         cors: {
//             origin: `http://${process.env.VITE_APP_ONION_DOMAIN}`,
//             credentials: true,
//         },
//     },
//     plugins: [
//         laravel({
//             input: 'resources/js/app.js',
//             refresh: {
//                 paths: ['resources/views/**'],
//                 delay: 200,
//             },
//         }),
//         vue({
//             template: {
//                 transformAssetUrls: {
//                     base: null,
//                     includeAbsolute: false,
//                 },
//             },
//         }),
//     ],
// });