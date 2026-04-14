import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

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
                sans:    ['Open Sans', ...defaultTheme.fontFamily.sans],
                heading: ['Roboto',    ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: 'hsl(207, 60%, 28%)',
                    foreground: 'hsl(0, 0%, 100%)',
                    light: 'hsl(207, 60%, 35%)',
                    dark: 'hsl(207, 60%, 20%)',
                },
                secondary: {
                    DEFAULT: 'hsl(28, 80%, 52%)',
                    foreground: 'hsl(0, 0%, 100%)',
                },
                accent: {
                    DEFAULT: 'hsl(28, 80%, 52%)',
                    foreground: 'hsl(0, 0%, 100%)',
                    hover: 'hsl(28, 80%, 45%)',
                },
                muted: {
                    DEFAULT: 'hsl(210, 20%, 95%)',
                    foreground: 'hsl(207, 30%, 45%)',
                },
                destructive: {
                    DEFAULT: 'hsl(0, 84%, 60%)',
                    foreground: 'hsl(0, 0%, 100%)',
                },
                border: 'hsl(210, 20%, 85%)',
                input: 'hsl(210, 20%, 85%)',
                card: {
                    DEFAULT: 'hsl(0, 0%, 100%)',
                    foreground: 'hsl(207, 60%, 28%)',
                },
                background: '#fafbfc',
                foreground: 'hsl(207, 60%, 28%)',
            },
            boxShadow: {
                soft: '0 4px 20px -2px hsl(207 60% 28% / 0.08)',
                card: '0 2px 12px -2px hsl(207 60% 28% / 0.06)',
            },
            borderRadius: {
                DEFAULT: '0.5rem',
            },
        },
    },

    plugins: [forms, typography],
};
