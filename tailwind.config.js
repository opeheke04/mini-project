import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f5f3ff',
                    100: '#ede9fe',
                    200: '#ddd6fe',
                    300: '#c4b5fd',
                    400: '#a78bfa',
                    500: '#8b5cf6',
                    600: '#7c3aed',
                    700: '#6d28d9',
                    800: '#5b21b6',
                    900: '#4c1d95',
                },
                accent: {
                    50: '#f8fafc',
                    100: '#e2e8f0',
                    200: '#cbd5e1',
                    300: '#94a3b8',
                    400: '#64748b',
                    500: '#475569',
                    600: '#334155',
                    700: '#1e293b',
                    800: '#0f172a',
                    900: '#020617',
                },
                luxe: {
                    gold: '#d4af37',
                    champagne: '#f5e7c6',
                    mist: '#f8fafc',
                },
            },
            boxShadow: {
                soft: '0 10px 30px rgba(15, 23, 42, 0.08)',
                glow: '0 12px 30px rgba(124, 58, 237, 0.2)',
            },
        },
    },

    plugins: [forms],
};
