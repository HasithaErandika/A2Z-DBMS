<?php
// src/views/partials/theme.php
?>
<!-- Centralized Google Fonts (Plus Jakarta Sans) -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Centralized FontAwesome Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- Centralized Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {

                    // Primary Blue
                    blue: {
                        50: '#EFF6FF',
                        100: '#DBEAFE',
                        200: '#BFDBFE',
                        300: '#93C5FD',
                        400: '#60A5FA',
                        500: '#3B82F6',
                        600: '#2563EB',
                        700: '#1D4ED8',
                        800: '#1E40AF',
                        900: '#1E3A8A',
                        950: '#172554'
                    },

                    // Professional Indigo
                    indigo: {
                        50: '#EEF2FF',
                        100: '#E0E7FF',
                        200: '#C7D2FE',
                        300: '#A5B4FC',
                        400: '#818CF8',
                        500: '#6366F1',
                        600: '#4F46E5',
                        700: '#4338CA',
                        800: '#3730A3',
                        900: '#312E81',
                        950: '#1E1B4B'
                    },

                    // Brand Orange
                    orange: {
                        50: '#FFF7ED',
                        100: '#FFEDD5',
                        200: '#FED7AA',
                        300: '#FDBA74',
                        400: '#FB923C',
                        500: '#F97316',
                        600: '#EA580C',
                        700: '#C2410C',
                        800: '#9A3412',
                        900: '#7C2D12',
                        950: '#431407'
                    },

                    // Brand Colors
                    brand: {
                        blue: '#2563EB',
                        indigo: '#4F46E5',
                        orange: '#F97316',

                        blueHover: '#1D4ED8',
                        indigoHover: '#4338CA',
                        orangeHover: '#EA580C',

                        background: '#F8FAFC',
                        card: '#FFFFFF',
                        border: '#E2E8F0',

                        text: '#0F172A',
                        textMuted: '#64748B'
                    }
                },

                fontFamily: {
                    sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    poppins: ['"Plus Jakarta Sans"', 'sans-serif']
                },

                boxShadow: {
                    soft: '0 2px 8px rgba(15,23,42,0.06)',
                    card: '0 6px 20px rgba(15,23,42,0.08)',
                    glow: '0 0 0 4px rgba(37,99,235,0.12)'
                }
            }
        }
    }
</script>

<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #F8FAFC;
        color: #0F172A;
    }

    .neon-glow {
        box-shadow: 0 10px 40px -10px rgba(37, 99, 235, 0.18);
    }

    .brand-gradient {
        background: linear-gradient(
            135deg,
            #2563EB 0%,
            #4F46E5 50%,
            #F97316 100%
        );
    }
</style>