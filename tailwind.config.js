/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    './app/Filament/**/*.php',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        'custom-accent': '#F4AC45',
        'custom-primary': '#09C2EC',
        'custom-secondary': '#086788'
      },
    },
  },
  plugins: [],
}

