const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    '100': '#C9DFFC',
                    '200': '#95BFF5',
                    '300': '#6CA4ED',
                    '400': '#438CEA',
                    '500': '#1A74E8',
                    '600': '#0767E3',
                    '700': '#05479B',
                    '800': '#03387D',
                    '900': '#022A5E',
                },
                brand2: {
                    '100': '#6FD1C7',
                    '200': '#47B7AB',
                    '300': '#25A497',
                    '400': '#2D8D83',
                    '500': '#118377',
                    '600': '#076D62',
                    '700': '#005A50',
                    '800': '#00433C',
                    '900': '#002d28',
                }
            }
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
