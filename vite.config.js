import { defineConfig } from 'vite';
import { resolve } from 'path';

// Vite 빌드 및 에셋 번들러 설정 정의
export default defineConfig({
  // 라라벨 패키지 퍼블리싱 에셋의 실제 절대 경로 배포 위치를 base로 지정하여 CSS 내 폰트/이미지 404 예방
  base: '/packages/saaksin/administrator/dist/',
  // publicDir를 false로 지정하여 outDir과 public 디렉토리가 겹쳐 발생하는 Vite 경고를 방지합니다.
  publicDir: false,
  build: {
    // 빌드 결과물이 위치할 디렉토리를 public/dist로 설정합니다.
    outDir: 'public/dist',
    // 빌드 시작 전에 기존 outDir 폴더의 리소스를 정리합니다.
    emptyOutDir: true,
    // manifest.json 자동 생성 활성화 (Laravel 에셋 매핑용)
    manifest: true,
    rollupOptions: {
      input: {
        // 애플리케이션의 메인 엔트리 포인트를 지정합니다.
        app: resolve(__dirname, 'resources/js/app.ts'),
      },
      output: {
        // 빌드 에셋 파일 이름에 해시를 추가하여 자동 캐시 버스팅을 구현합니다.
        entryFileNames: 'js/[name]-[hash].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: ({ name }) => {
          if (name && name.endsWith('.css')) {
            return 'css/[name]-[hash].[ext]';
          }
          return '[ext]/[name]-[hash].[ext]';
        },
      },
    },
  },
});
