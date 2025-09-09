/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./view/**/*.php",
    "./index.php",
    "./**/*.html"
  ],
  theme: {
    extend: {
      fontFamily: {
        'heading': ['Playfair Display', 'serif'],
        'body': ['Poppins', 'sans-serif'],
        'din': ['DIN', 'Arial', 'sans-serif'],
      },
      colors: {
        // Main motif colors
        'greenDark': {
          50: '#f0f9f4',
          100: '#dcf4e6',
          200: '#bce8d1',
          300: '#8dd5b4',
          400: '#56bc91',
          500: '#2d9b6f',
          600: '#1f7a5a',
          700: '#1a6249',
          800: '#164e3b',
          900: '#014421', // Primary dark green
          950: '#012a15',
        },
        'gold': {
          50: '#fefce8',
          100: '#fef9c3',
          200: '#fef08a',
          300: '#fde047',
          400: '#facc15',
          500: '#C9A227', // Primary gold
          600: '#ca8a04',
          700: '#a16207',
          800: '#854d0e',
          900: '#713f12',
          950: '#422006',
        },
        'brown': {
          50: '#fdf8f6',
          100: '#f2e8e5',
          200: '#eaddd7',
          300: '#e0cec7',
          400: '#d2bab0',
          500: '#bfa094',
          600: '#a18072',
          700: '#6F4E37', // Primary brown
          800: '#5a3e2d',
          900: '#4a3224',
          950: '#2d1e16',
        },
        // Additional complementary colors
        'sage': {
          50: '#f6f7f4',
          100: '#e8ebe4',
          200: '#d2d8c8',
          300: '#b5c0a7',
          400: '#9aa585',
          500: '#7f8a6b',
          600: '#6b7558',
          700: '#565e48',
          800: '#474d3c',
          900: '#3d4234',
          950: '#1f2219',
        },
        'cream': {
          50: '#fefdfb',
          100: '#fdf9f3',
          200: '#faf2e6',
          300: '#f6e8d1',
          400: '#f0d9b5',
          500: '#e8c896',
          600: '#deb575',
          700: '#d4a155',
          800: '#b88a47',
          900: '#9a723c',
          950: '#5c441f',
        }
      },
      animation: {
        'fade-in': 'fadeIn 1s ease-in-out',
        'fade-in-delay': 'fadeIn 1.5s ease-in-out',
        'slide-down': 'slideDown 1s ease-out',
        'slide-up': 'slideUp 1s ease-out',
        'pop': 'pop 0.8s ease-out',
        'float': 'float 3s ease-in-out infinite',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideDown: {
          '0%': { transform: 'translateY(-20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        pop: {
          '0%': { transform: 'scale(0.9)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-10px)' },
        },
      },
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
        'hero-pattern': "url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23014421\" fill-opacity=\"0.05\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')",
      },
      boxShadow: {
        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
        'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
        'strong': '0 10px 40px -10px rgba(0, 0, 0, 0.15), 0 2px 10px -2px rgba(0, 0, 0, 0.05)',
        'glow': '0 0 20px rgba(201, 162, 39, 0.3)',
        'glow-green': '0 0 20px rgba(1, 68, 33, 0.3)',
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
