import { EditorStrategy } from './EditorStrategy';

export class QuillEditorStrategy implements EditorStrategy {
    private instances: Record<string, any> = {};

    public initialize(element: HTMLElement, fieldName: string, value: string, onChange: (val: string) => void): void {
        const quillLib = (window as any).Quill;
        if (!quillLib) {
            console.warn('[QuillEditorStrategy] Quill 전역 객체를 찾을 수 없습니다.');
            return;
        }

        const editorId = element.id || `quill_${fieldName}`;
        if (!element.id) {
            element.id = editorId;
        }

        const quill = new quillLib(element, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'font': [] }, { 'size': ['small', false, 'large', 'huge'] }],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'direction': 'rtl' }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });

        quill.root.innerHTML = value || '';

        quill.on('text-change', () => {
            let html = quill.root.innerHTML;
            if (html === '<p><br></p>') {
                html = '';
            }
            onChange(html);
        });

        this.instances[fieldName] = quill;
    }

    public destroy(fieldName: string): void {
        if (this.instances[fieldName]) {
            delete this.instances[fieldName];
        }
    }

    public setValue(fieldName: string, value: string): void {
        const quill = this.instances[fieldName];
        if (quill) {
            let currentHTML = quill.root.innerHTML;
            if (currentHTML === '<p><br></p>') {
                currentHTML = '';
            }
            if (currentHTML !== value) {
                quill.root.innerHTML = value || '';
            }
        }
    }
}
