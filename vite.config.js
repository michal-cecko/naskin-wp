import path from 'path';
import { defineConfig } from 'vite';

const ROOT = path.resolve('../../../')
const BASE = __dirname.replace(ROOT, '');

export default defineConfig({
    base: process.env.NODE_ENV === 'production' ? `${BASE}/dist/` : BASE,
    build: {
        manifest: 'manifest.json',
        assetsDir: '.',
        outDir: `dist`,
        emptyOutDir: true,
        sourcemap: true,
        rollupOptions: {
            input: [
                'resources/scripts/components/commons.js',

                //Admin dashboard styles + scripts
                'resources/scripts/admin.js',
                'resources/scripts/components/admin/calendar.js',
                'resources/scripts/components/admin/single-expense.js',
                'resources/scripts/components/admin/single-product-sale.js',

                'resources/styles/admin/roles/employee/employee_role_dashboard.scss',
                'resources/styles/admin/roles/employee/employee_role_web.scss',
                'resources/styles/admin/calendar.scss',
                'resources/styles/admin/dashboard.scss',

                // Frontend styles + scripts
                'resources/scripts/general.js',
                'resources/scripts/components/dialogs.js',
                'resources/scripts/components/service-taxonomy-single.js',
                'resources/scripts/components/header.js',
                'resources/scripts/components/reservation-form.js',
                'resources/styles/imports/general.scss',
                'resources/styles/pages/front-page.scss',
                'resources/styles/pages/service-taxonomy-single.scss',
                'resources/styles/pages/template-cennik.scss',

            ],
            output: {
                entryFileNames: '[hash].js',
                assetFileNames: '[hash].[ext]',
            },
        },
    },
    plugins: [
        {
            name: 'php',
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.php') || file.endsWith('.scss') || file.endsWith('.js') || file.endsWith('.ts')) {
                    server.ws.send({ type: 'full-reload' });
                }
            },
        },
        // Sass plugin for resolving paths dynamically
        {
            name: 'sass',
            // This will allow you to use the same path resolution logic as in your JavaScript files
            transform: function (code, id) {
                return {
                    code: code.replace(/@\//g, BASE + '/resources/'),
                    map: null,
                };
            },
        },
    ],
});
