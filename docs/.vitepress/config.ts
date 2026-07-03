import { defineConfig } from 'vitepress';

const guideSidebar = [
    { text: 'Introduction', link: '/docs/introduction' },
    { text: 'Installation', link: '/docs/installation' },
    { text: 'Configuration', link: '/docs/configuration' },
    { text: 'Model Configuration', link: '/docs/model-configuration' },
    { text: 'Settings Configuration', link: '/docs/settings-configuration' },
    { text: 'Validation', link: '/docs/validation' },
    { text: 'Localization', link: '/docs/localization' },
    { text: 'Tutorials', link: '/docs/tutorials' },
];

const referenceSidebar = [
    { text: 'Fields', link: '/docs/fields' },
    { text: 'Columns', link: '/docs/columns' },
    { text: 'Relationship Columns', link: '/docs/relationship-columns' },
    { text: 'Actions', link: '/docs/actions' },
];

const fieldSidebar = [
    { text: 'Key', link: '/docs/field-type-key' },
    { text: 'Text', link: '/docs/field-type-text' },
    { text: 'Password', link: '/docs/field-type-password' },
    { text: 'Textarea', link: '/docs/field-type-textarea' },
    { text: 'WYSIWYG', link: '/docs/field-type-wysiwyg' },
    { text: 'WYSIWYG2', link: '/docs/field-type-wysiwyg2' },
    { text: 'Markdown', link: '/docs/field-type-markdown' },
    { text: 'Relationship', link: '/docs/field-type-relationship' },
    { text: 'Number', link: '/docs/field-type-number' },
    { text: 'Bool', link: '/docs/field-type-bool' },
    { text: 'Enum', link: '/docs/field-type-enum' },
    { text: 'Date', link: '/docs/field-type-date' },
    { text: 'Time', link: '/docs/field-type-time' },
    { text: 'Datetime', link: '/docs/field-type-datetime' },
    { text: 'File', link: '/docs/field-type-file' },
    { text: 'Image', link: '/docs/field-type-image' },
    { text: 'Color', link: '/docs/field-type-color' },
];

const koreanSidebar = [
    {
        text: '시작하기',
        items: [
            { text: '소개', link: '/docs/ko/introduction' },
            { text: '설치', link: '/docs/ko/installation' },
            { text: '설정', link: '/docs/ko/configuration' },
            { text: '모델 설정', link: '/docs/ko/model-configuration' },
            { text: '세팅 설정', link: '/docs/ko/settings-configuration' },
            { text: '유효성 검사', link: '/docs/ko/validation' },
            { text: '다국어 지원', link: '/docs/ko/localization' },
        ],
    },
    {
        text: '레퍼런스',
        items: [
            { text: '필드', link: '/docs/ko/fields' },
            { text: '컬럼', link: '/docs/ko/columns' },
            { text: '관계 컬럼', link: '/docs/ko/relationship-columns' },
            { text: '액션', link: '/docs/ko/actions' },
        ],
    },
    {
        text: '필드 타입',
        items: [
            { text: 'Key', link: '/docs/ko/field-type-key' },
            { text: 'Text', link: '/docs/ko/field-type-text' },
            { text: 'Password', link: '/docs/ko/field-type-password' },
            { text: 'Textarea', link: '/docs/ko/field-type-textarea' },
            { text: 'WYSIWYG', link: '/docs/ko/field-type-wysiwyg' },
            { text: 'WYSIWYG2', link: '/docs/ko/field-type-wysiwyg2' },
            { text: 'Markdown', link: '/docs/ko/field-type-markdown' },
            { text: 'Relationship', link: '/docs/ko/field-type-relationship' },
            { text: 'Number', link: '/docs/ko/field-type-number' },
            { text: 'Bool', link: '/docs/ko/field-type-bool' },
            { text: 'Enum', link: '/docs/ko/field-type-enum' },
            { text: 'Date', link: '/docs/ko/field-type-date' },
            { text: 'Time', link: '/docs/ko/field-type-time' },
            { text: 'Datetime', link: '/docs/ko/field-type-datetime' },
            { text: 'File', link: '/docs/ko/field-type-file' },
            { text: 'Image', link: '/docs/ko/field-type-image' },
            { text: 'Color', link: '/docs/ko/field-type-color' },
        ],
    },
    {
        text: '프로젝트',
        items: [
            { text: '기여하기', link: '/docs/ko/contributing' },
            { text: '스타일 가이드', link: '/docs/ko/style-guide' },
            { text: '라이선스', link: '/docs/ko/license' },
        ],
    },
];

export default defineConfig({
    title: 'Laravel Administrator',
    description: 'Laravel 10 기반 관리자 페이지 빌더 패키지',
    lang: 'ko-KR',
    cleanUrls: true,
    lastUpdated: true,
    srcExclude: ['tasks/**'],
    markdown: {
        lineNumbers: true,
    },
    rewrites: (id) => {
        if (id === 'index.md') {
            return id;
        }

        return `docs/${id}`;
    },
    head: [
        ['meta', { name: 'theme-color', content: '#2563eb' }],
        ['meta', { property: 'og:type', content: 'website' }],
        ['meta', { property: 'og:title', content: 'Laravel Administrator' }],
        ['meta', { property: 'og:site_name', content: 'Laravel Administrator' }],
        ['meta', { property: 'og:url', content: 'https://administrator.artgrammer.co.kr' }],
    ],
    themeConfig: {
        logo: '/favicon.svg',
        siteTitle: 'Laravel Administrator',
        nav: [
            { text: '소개', link: '/' },
            { text: '시작하기', link: '/docs/ko/installation' },
            { text: '매뉴얼', link: '/docs/ko/documentation' },
            { text: 'English', link: '/docs/introduction' },
            { text: 'GitHub', link: 'https://github.com/SaAkSin/Laravel-Administrator' },
        ],
        sidebar: {
            '/docs/ko/': [
                ...koreanSidebar,
            ],
            '/docs/': [
                {
                    text: 'Guide',
                    items: guideSidebar,
                },
                {
                    text: 'Reference',
                    items: referenceSidebar,
                },
                {
                    text: 'Field Types',
                    items: fieldSidebar,
                },
            ],
        },
        socialLinks: [
            { icon: 'github', link: 'https://github.com/SaAkSin/Laravel-Administrator' },
        ],
        search: {
            provider: 'local',
        },
        editLink: {
            pattern: 'https://github.com/SaAkSin/Laravel-Administrator/edit/dev/docs/:path',
            text: 'GitHub에서 이 문서 수정',
        },
        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2026 Artgrammer',
        },
    },
});
