/** @type {import('tailwindcss').Config} */
export default {
  content: [
    // Blade 템플릿 및 PHP 뷰 파일 내의 Tailwind 클래스를 감지하도록 설정합니다.
    "./src/views/**/*.blade.php",
    "./src/views/**/*.php",
    "./resources/js/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
