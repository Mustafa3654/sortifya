import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/Filament/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Display: geometric, faintly technical. Headlines only.
                display: ['Sora', ...defaultTheme.fontFamily.sans],
                // Body: quiet, slightly condensed grotesque.
                sans: ['Instrument Sans', ...defaultTheme.fontFamily.sans],
                // Data: every figure here is money or a count. All mono.
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
                // Arabic swaps the whole stack; the Latin faces have no Arabic cuts.
                arabic: ['IBM Plex Sans Arabic', 'Noto Kufi Arabic', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                // Near-black canvas with a green undertone, not neutral grey.
                ink: {
                    950: '#05070a',
                    900: '#080c10',
                    850: '#0b1014',
                    800: '#101820',
                    750: '#16202a',
                },
                // Light canvas, cooled slightly green so it never reads cream.
                mist: {
                    50: '#f7faf9',
                    100: '#eff4f2',
                    200: '#e2eae7',
                },
            },

            letterSpacing: {
                tightest: '-0.045em',
            },

            boxShadow: {
                // Raised panels in dark mode; the glow does the lifting.
                glow: '0 0 0 1px rgb(16 185 129 / 0.12), 0 18px 60px -20px rgb(16 185 129 / 0.28)',
                panel: '0 1px 2px rgb(2 6 23 / 0.04), 0 12px 36px -16px rgb(2 6 23 / 0.16)',
            },

            backgroundImage: {
                'grid-light':
                    'linear-gradient(to right, rgb(15 23 42 / 0.05) 1px, transparent 1px), linear-gradient(to bottom, rgb(15 23 42 / 0.05) 1px, transparent 1px)',
                'grid-dark':
                    'linear-gradient(to right, rgb(226 232 240 / 0.05) 1px, transparent 1px), linear-gradient(to bottom, rgb(226 232 240 / 0.05) 1px, transparent 1px)',
            },

            backgroundSize: {
                cell: '44px 34px',
            },

            keyframes: {
                'cell-in': {
                    '0%': { opacity: '0', transform: 'translateX(-6px) skewX(-8deg)' },
                    '60%': { opacity: '1' },
                    '100%': { opacity: '1', transform: 'translateX(0) skewX(0deg)' },
                },
                caret: {
                    '0%, 45%': { opacity: '1' },
                    '50%, 95%': { opacity: '0' },
                },
                drift: {
                    '0%, 100%': { transform: 'translate3d(0,0,0) scale(1)' },
                    '50%': { transform: 'translate3d(0,-14px,0) scale(1.06)' },
                },
            },

            animation: {
                'cell-in': 'cell-in 0.5s cubic-bezier(0.2, 0.9, 0.25, 1) both',
                caret: 'caret 1.1s steps(1) infinite',
                drift: 'drift 14s ease-in-out infinite',
            },
        },
    },

    plugins: [forms, typography],
};
