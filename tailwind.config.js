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
            colors: {
                brand: {
                    DEFAULT: '#d91426',
                    purple: '#6f22de',
                    dark: '#191339',
                    light: '#f7f5fb',
                },
                gradientFrom: '#6f22de',
                gradientTo: '#d91426',
            },
            fontFamily: {
                sans: ['Poppins', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                floatY: {
                    '0%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                    '100%': { transform: 'translateY(0)' },
                },
                wiggle: {
                    '0%': { transform: 'rotate(-1deg)' },
                    '50%': { transform: 'rotate(1deg)' },
                    '100%': { transform: 'rotate(-1deg)' },
                },
            },
            animation: {
                float: 'floatY 6s ease-in-out infinite',
                wiggle: 'wiggle 8s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
