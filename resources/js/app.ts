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

// 전역 window.Alpine 이 존재하면 공유하고, 없으면 패키지 내 수입본을 할당
const alpineInstance = (window as any).Alpine || Alpine;
if (!(window as any).Alpine) {
    (window as any).Alpine = alpineInstance;
}

// 개별 분리된 OOP 컨트롤러 수입
import { AdminController } from './controllers/AdminController';
import { RelationSelectController } from './controllers/RelationSelectController';

// Alpine 컴포넌트 데이터 바인딩 등록 및 강제 인젝션 방어 구조
const injectComponents = () => {
    const w = window as any;
    if (w.Alpine && typeof w.Alpine.data === 'function') {
        w.Alpine.data('adminController', () => new AdminController());
        w.Alpine.data('relationSelect', (config: any) => new RelationSelectController(config));
        
        // Alpine.js $root 매직 프로퍼티 오버라이드
        w.Alpine.magic('root', (el: any) => {
            const parent = el.parentElement;
            const rootEl = parent ? parent.closest('[x-data]') : el.closest('[x-data]');
            return rootEl ? w.Alpine.$data(rootEl) : {};
        });
    }
};

// 1) 즉시 실행
injectComponents();

// 2) DOMContentLoaded 시점에 실행
document.addEventListener('DOMContentLoaded', injectComponents);

// 3) 호스트 app.js 비동기 로딩을 대비해 100ms 주기로 2초간 폴백 인젝션
let injectCount = 0;
const injectInterval = setInterval(() => {
    injectComponents();
    injectCount++;
    if (injectCount > 20) {
        clearInterval(injectInterval);
    }
}, 100);

// 윈도우 스코프에도 레거시 안전 노출
(window as any).adminController = () => new AdminController();
(window as any).relationSelect = (config: any) => new RelationSelectController(config);

// 전역 인스턴스가 패키지에서 처음 생성된 경우에만 start() 호출
if (alpineInstance === Alpine) {
    alpineInstance.start();
}
