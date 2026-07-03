// CSS 스타일시트 엔트리를 빌드에 포함
import '../css/app.css';

// 모던 의존성 패키지 전역 바인딩
import { marked } from 'marked';
import accounting from 'accounting';

(window as any).marked = marked;
(window as any).accounting = accounting;

// 마크다운 내부 HTML XSS 방어를 위한 escape 헬퍼 함수
const escapeHtml = (text: string): string => {
    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};

// HTML Entity를 디코딩하여 원본 텍스트를 복원 (우회 방어)
const decodeHtmlEntities = (value: string): string => {
    if (typeof document === 'undefined') return value;
    const textarea = document.createElement('textarea');
    textarea.innerHTML = value;
    return textarea.value;
};

// 제어문자 및 공백을 정규화하여 우회 차단
const normalizeUrlForPolicy = (value: string): string => {
    return decodeHtmlEntities(value)
        .replace(/[\u0000-\u001F\u007F\s]+/g, '')
        .trim();
};

// 명시적인 프로토콜 스키마가 존재하는지 확인
const hasExplicitScheme = (value: string): boolean => {
    return /^[a-z][a-z0-9+.-]*:/i.test(value);
};

// 검증된 프로토콜 allowlist 기반 URL Sanitizer
const sanitizeUrl = (rawUrl: string, allowedProtocols: string[]): string => {
    try {
        const normalizedUrl = normalizeUrlForPolicy(rawUrl);
        if (normalizedUrl === '') {
            return '#';
        }

        // 상대 경로(/path, ./path 등)는 스키마가 없으므로 허용하되 이스케이프 처리
        if (!hasExplicitScheme(normalizedUrl)) {
            return escapeHtml(normalizedUrl);
        }

        // URL 파싱을 통해 프로토콜 확인
        const parsedUrl = new URL(normalizedUrl);
        if (allowedProtocols.includes(parsedUrl.protocol)) {
            return escapeHtml(normalizedUrl);
        }

        return '#';
    } catch (e) {
        return '#';
    }
};

const sanitizeLinkUrl = (rawUrl: string): string => {
    return sanitizeUrl(rawUrl, ['http:', 'https:', 'mailto:', 'tel:']);
};

const sanitizeImageUrl = (rawUrl: string): string => {
    return sanitizeUrl(rawUrl, ['http:', 'https:']);
};

marked.use({
    renderer: {
        html(token: any) {
            return escapeHtml(token.text || token.raw || '');
        },
        link(this: any, token: any) {
            const href = sanitizeLinkUrl(token.href || '');
            const title = token.title ? ` title="${escapeHtml(token.title)}"` : '';
            const text = this.parser.parse(token.tokens || []);
            return `<a href="${href}"${title}>${text}</a>`;
        },
        image(token: any) {
            const src = sanitizeImageUrl(token.href || '');
            const title = token.title ? ` title="${escapeHtml(token.title)}"` : '';
            const alt = token.text ? ` alt="${escapeHtml(token.text)}"` : '';
            return `<img src="${src}"${alt}${title} />`;
        }
    }
});

(window as any).markdown = {
    toHTML: (str: string) => {
        const parsed = marked.parse(str || '');
        return typeof parsed === 'string' ? parsed : '';
    }
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

// 마크다운 XSS 회귀 검증 스크립트 실행
export const runMarkdownXssRegressionTests = (): void => {
    if (typeof document === 'undefined') return;

    const unsafeInputs = [
        '<img src=x onerror=alert(1)>',
        '[x](javascript:alert(1))',
        '[x](javascript&#58;alert(1))',
        '[x](java\nscript:alert(1))',
        '[x](data:text/html,<script>alert(1)</script>)',
        '![x](data:image/svg+xml,<svg onload=alert(1)>)',
        '[x](vbscript:msgbox(1))',
        '[x](https://example.com/"onclick="alert(1))',
    ];

    for (const input of unsafeInputs) {
        const output = marked.parse(input) as string;
        const doc = new DOMParser().parseFromString(output, 'text/html');

        for (const element of Array.from(doc.body.querySelectorAll('*'))) {
            // 1. onclick, onerror 등 이벤트 핸들러 삽입 여부 검사
            for (const attributeName of element.getAttributeNames()) {
                if (attributeName.toLowerCase().startsWith('on')) {
                    throw new Error(`unsafe event attribute: ${input} -> ${output}`);
                }
            }

            // 2. href, src 의 URL 위험성 검사
            for (const attributeName of ['href', 'src']) {
                const attributeValue = element.getAttribute(attributeName);
                if (!attributeValue) {
                    continue;
                }

                const normalizedValue = normalizeUrlForPolicy(attributeValue).toLowerCase();
                if (/^(javascript|data|vbscript):/.test(normalizedValue)) {
                    throw new Error(`unsafe url attribute: ${input} -> ${output}`);
                }
            }
        }
    }
    console.log('[보안 검증] 모든 마크다운 XSS 회귀 테스트 케이스를 무사히 통과했습니다.');
};

// 로딩 시점에 회귀 테스트 즉시 기동하여 실시간 안전성 담보
try {
    runMarkdownXssRegressionTests();
} catch (error) {
    console.error('[보안 경고] 마크다운 XSS 회귀 테스트 실패:', error);
}
