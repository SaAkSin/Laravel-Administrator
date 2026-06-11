// CSS 스타일시트 엔트리를 빌드에 포함
import '../css/app.css';

// 모던 의존성 패키지 전역 바인딩
import { marked } from 'marked';
import accounting from 'accounting';

(window as any).marked = marked;
(window as any).accounting = accounting;

(window as any).markdown = {
    toHTML: (str: string) => marked.parse(str || '')
};

// Alpine.js 엔진 및 Quill 모듈 바인딩
import Alpine from 'alpinejs';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

(window as any).Quill = Quill;

// 개별 분리된 OOP 컨트롤러 수입
import { AdminController } from './controllers/AdminController';
import { RelationSelectController } from './controllers/RelationSelectController';

// Alpine.js $root 매직 프로퍼티 오버라이드 (반응성 상태 프록시 리턴)
Alpine.magic('root', (el) => {
    const rootEl = el.closest('[x-data]');
    return rootEl ? Alpine.$data(rootEl) : {};
});

// Alpine 컴포넌트 데이터 바인딩 등록
Alpine.data('adminController', () => new AdminController());
Alpine.data('relationSelect', (config: any) => new RelationSelectController(config));

// 윈도우 스코프에도 레거시 안전 노출
(window as any).adminController = () => new AdminController();
(window as any).relationSelect = (config: any) => new RelationSelectController(config);

// Alpine.js 기동
Alpine.start();
