import preset from './vendor/filament/filament/tailwind.config.preset.js'
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/css/filament/*.css',
        './app/Filament/**/*.php',
        './app/Livewire/**/*.php',
        './vendor/filament/**/*.blade.php',
        './vendor/filament/**/*.php',
    ],

    // Filament renders many class names dynamically (in PHP/Blade we never
    // see as literal strings), so Tailwind's content scan would purge them.
    // Safelist the descendant classes we override in
    // resources/css/filament/*.css so they survive the content scan even
    // when they only appear as CSS selectors. Chrome wrappers (.fi-sidebar,
    // .fi-topbar, .fi-header, .fi-layout, .fi-main-*, .fi-logo) are NOT
    // styled here — Filament's defaults render them — so they don't need
    // safelist entries.
    safelist: [
        // Body scope used as a specificity bump on every override
        'fi-body',

        // Sidebar descendants
        'fi-sidebar-group',
        'fi-sidebar-group-label',
        'fi-sidebar-group-button',
        'fi-sidebar-group-collapse-button',
        'fi-sidebar-group-items',
        'fi-sidebar-item',
        'fi-sidebar-item-active',
        'fi-sidebar-item-button',
        'fi-sidebar-item-icon',
        'fi-sidebar-item-label',

        // Topbar descendants
        'fi-topbar-open-sidebar-btn',
        'fi-topbar-close-sidebar-btn',
        'fi-topbar-item',
        'fi-user-menu',
        'fi-global-search-field',

        // Header / breadcrumbs
        'fi-header',
        'fi-header-heading',
        'fi-header-subheading',
        'fi-breadcrumbs',
        'fi-breadcrumbs-item',

        // Widgets / stats
        'fi-wi',
        'fi-wi-widget',
        'fi-wi-stats-overview',
        'fi-wi-stats-overview-stat',
        'fi-wi-stats-overview-stat-icon',
        'fi-wi-stats-overview-stat-label',
        'fi-wi-stats-overview-stat-value',
        'fi-wi-stats-overview-stat-description',
        'fi-wi-chart',
        'fi-wi-table',
        'fi-dashboard-page',

        // Sections / cards
        'fi-section',
        'fi-section-header',
        'fi-section-header-heading',
        'fi-section-content',
        'fi-modal-window',
        'fi-modal-heading',
        'fi-modal-close-button',
        'fi-modal-footer',
        'fi-modal-content',
        'fi-notification',
        'fi-notification-title',
        'fi-notification-body',
        'fi-notification-icon',
        'fi-notification-close-button',
        'fi-ta-empty-state',
        'fi-ta-empty-state-content',

        // Tables
        'fi-ta-ctn',
        'fi-ta-heading',
        'fi-ta-row',
        'fi-ta-cell',
        'fi-ta-header-cell',
        'fi-ta-summary-header-cell-label',
        'fi-ta-col-summary-label',

        // Forms
        'fi-input',
        'fi-select-input-el',
        'fi-textarea-input',
        'fi-fo-select-input',
        'fi-fo-textarea-input',
        'fi-fo-text-input',
        'fi-fo-field-wrp-label',
        'fi-fo-field-wrp-helper-text',
        'fi-fo-field-wrp-error-message',
        'fi-checkbox-input',
        'fi-radio-input',

        // Buttons / badges / pagination
        'fi-btn',
        'fi-btn-color-primary',
        'fi-btn-color-gray',
        'fi-btn-color-success',
        'fi-btn-color-danger',
        'fi-btn-color-warning',
        'fi-badge',
        'fi-badge-color-primary',
        'fi-badge-color-info',
        'fi-badge-color-success',
        'fi-badge-color-warning',
        'fi-badge-color-danger',
        'fi-badge-color-gray',
        'fi-pagination',
        'fi-pagination-records-per-page',
        'fi-pagination-item',
        'fi-pagination-item-active',

        // Custom utilities used by the login view
        'neu-paper',
        'neu-surface',
        'neu-surface-sm',
        'neu-inset',
        'neu-inset-sm',
        'neu-input',
        'neu-btn',
        'neu-btn-primary',

        // Widget color bars used in PHP string output
        'fi-wi-fleet-status',
        'bg-sage-400',
        'bg-amber-400',
        'bg-rust-400',
        'bg-clay-400',
        'bg-surface-400',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                // White-and-black monochrome neomorphism palette.
                // Premium, clean, Apple-style: white base, black accent.
                // Used by inner pages, login, and Filament.
                surface: {
                    50:  '#ffffff', // lightest highlight
                    100: '#fafafa', // surface raised
                    200: '#f5f5f5', // base background
                    300: '#e5e7eb', // surface inset / borders
                    400: '#9ca3af', // muted line
                    500: '#6b7280', // muted text on light
                    600: '#374151', // body text on light
                    700: '#1f2937', // heading text on light
                    800: '#0a0a0a', // primary text on light
                    900: '#000000', // deepest text / primary accent
                },
                clay: {
                    50:  '#f5f5f5',
                    100: '#e5e7eb',
                    200: '#d1d5db',
                    300: '#9ca3af',
                    400: '#000000', // primary accent (black)
                    500: '#1f2937',
                    600: '#374151',
                    700: '#0a0a0a',
                },
                sage: {
                    50:  '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#16a34a', // success / available
                    500: '#15803d',
                },
                amber: {
                    400: '#d97706', // warning / on rent
                    500: '#b45309',
                },
                rust: {
                    400: '#dc2626', // danger / out of service
                    500: '#b91c1c',
                },

                // Premium "automotive" palette for the public landing page.
                // Near-black foundation for hero, footer, and dark sections.
                ink: {
                    50:  '#f5f5f5',
                    100: '#e5e5e5',
                    200: '#d4d4d4',
                    300: '#a3a3a3',
                    400: '#737373',
                    500: '#525252',
                    600: '#404040',
                    700: '#262626',
                    800: '#1a1a1a',
                    900: '#111111',
                    950: '#0a0a0a',
                },
                // Warm off-white / light gray sections.
                bone: {
                    50:  '#fafaf7',
                    100: '#f5f5f0',
                    200: '#ebe9e2',
                    300: '#d6d3cc',
                    400: '#a8a29e',
                    500: '#78716c',
                },
                // Single strong accent — WhatsApp green for CTAs.
                accent: {
                    300: '#6fe3a4',
                    400: '#2ee374',
                    500: '#25d366',
                    600: '#20bd5a',
                    700: '#169c49',
                },
            },

            borderRadius: {
                'neu': '1.25rem',      // 20px — the canonical neomorphism radius
                'neu-sm': '0.75rem',   // 12px
                'neu-lg': '2rem',      // 32px
                // Premium subtle rounded corners used across the landing page.
                'premium': '0.75rem',  // 12px — cards, buttons
                'premium-lg': '1.25rem', // 20px — large feature cards
            },


            maxWidth: {
                'reading': '70ch',
            },

            fontFamily: {
                sans: ['"Inter"', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
                display: ['"Manrope"', '"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },

            // The two-shadow signature of neomorphism. Use boxShadow="neu" or "neu-inset".
            boxShadow: {
                'neu':         '8px 8px 16px rgba(0, 0, 0, 0.08), -8px -8px 16px rgba(255, 255, 255, 0.9)',
                'neu-sm':      '4px 4px 8px rgba(0, 0, 0, 0.06), -4px -4px 8px rgba(255, 255, 255, 0.9)',
                'neu-lg':      '12px 12px 24px rgba(0, 0, 0, 0.10), -12px -12px 24px rgba(255, 255, 255, 0.95)',
                'neu-inset':   'inset 6px 6px 12px rgba(0, 0, 0, 0.10), inset -6px -6px 12px rgba(255, 255, 255, 0.9)',
                'neu-inset-sm':'inset 3px 3px 6px rgba(0, 0, 0, 0.08), inset -3px -3px 6px rgba(255, 255, 255, 0.9)',
                'neu-pressed': 'inset 4px 4px 8px rgba(0, 0, 0, 0.10), inset -4px -4px 8px rgba(255, 255, 255, 0.9)',
                'neu-clay':    '6px 6px 12px rgba(0, 0, 0, 0.20), -6px -6px 12px rgba(255, 255, 255, 0.9)',
                // Premium subtle shadows for the landing page.
                'premium-sm':  '0 1px 2px 0 rgba(0, 0, 0, 0.04)',
                'premium':     '0 4px 16px -2px rgba(0, 0, 0, 0.06), 0 2px 6px -1px rgba(0, 0, 0, 0.03)',
                'premium-lg':  '0 12px 32px -4px rgba(0, 0, 0, 0.10), 0 4px 12px -2px rgba(0, 0, 0, 0.05)',
                'accent-glow': '0 8px 24px -6px rgba(37, 211, 102, 0.45)',
                'ink-glow':    '0 8px 24px -6px rgba(10, 10, 10, 0.45)',
            },

            transitionTimingFunction: {
                'neu': 'cubic-bezier(0.4, 0, 0.2, 1)',
                'premium': 'cubic-bezier(0.16, 1, 0.3, 1)',
            },

            keyframes: {
                'neu-press': {
                    '0%':   { boxShadow: '8px 8px 16px rgba(0, 0, 0, 0.08), -8px -8px 16px rgba(255, 255, 255, 0.9)' },
                    '100%': { boxShadow: 'inset 4px 4px 8px rgba(0, 0, 0, 0.10), inset -4px -4px 8px rgba(255, 255, 255, 0.9)' },
                },
                'reveal-up': {
                    '0%':   { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%':   { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'pulse-ring': {
                    '0%':   { transform: 'scale(0.95)', opacity: '0.7' },
                    '70%':  { transform: 'scale(1.6)', opacity: '0' },
                    '100%': { transform: 'scale(1.6)', opacity: '0' },
                },
            },
            animation: {
                'neu-press': 'neu-press 180ms cubic-bezier(0.4, 0, 0.2, 1) forwards',
                'reveal-up': 'reveal-up 700ms cubic-bezier(0.16, 1, 0.3, 1) forwards',
                'fade-in':   'fade-in 600ms cubic-bezier(0.16, 1, 0.3, 1) forwards',
                'pulse-ring':'pulse-ring 2.4s cubic-bezier(0.16, 1, 0.3, 1) infinite',
            },
        },
    },

    plugins: [forms, typography],
};
