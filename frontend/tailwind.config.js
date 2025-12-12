/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './src/pages/**/*.{js,ts,jsx,tsx,mdx}',
    './src/components/**/*.{js,ts,jsx,tsx,mdx}',
    './src/app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {
      colors: {
        // Cultural entomology brand colors - warm, earthy tones with insect-inspired accents
        primary: {
          50: '#fef7ee',
          100: '#fdecd3',
          200: '#fad5a5',
          300: '#f6b96d',
          400: '#f19332',
          500: '#ed7712',
          600: '#de5c08',
          700: '#b84409',
          800: '#93360f',
          900: '#772e10',
          950: '#401506',
        },
        accent: {
          50: '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#22c55e',
          600: '#16a34a',
          700: '#15803d',
          800: '#166534',
          900: '#14532d',
          950: '#052e16',
        },
        surface: {
          50: '#faf8f5',
          100: '#f5f0e8',
          200: '#e9dfd0',
          300: '#dac9b0',
          400: '#c7ad8e',
          500: '#b89574',
          600: '#ab8160',
          700: '#8e6a51',
          800: '#755746',
          900: '#60493c',
          950: '#33251e',
        },
      },
      fontFamily: {
        display: ['Playfair Display', 'Georgia', 'serif'],
        body: ['Source Sans Pro', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'Consolas', 'monospace'],
      },
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
        'texture-paper': "url('/textures/paper.png')",
      },
    },
  },
  plugins: [],
};

