import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** /** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Nama warna : Kode Hex dari Figma
        'brand-primary': '#10B981',   // Contoh hijau utama untuk tombol/ikon
        'brand-dark': '#1F2937',      // Contoh warna gelap untuk sidebar
        'brand-gray': '#F3F4F6',      // Contoh abu-abu untuk background halaman
        'status-full': '#EF4444',     // Merah untuk tong sampah penuh
      }
    },
  },
  plugins: [],
}