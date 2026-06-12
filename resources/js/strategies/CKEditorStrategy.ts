import { EditorStrategy } from './EditorStrategy';

export class CKEditorStrategy implements EditorStrategy {
    private instances: Record<string, any> = {};

    public initialize(element: HTMLElement, fieldName: string, value: string, onChange: (val: string) => void): void {
        const ck = (window as any).CKEDITOR;
        if (!ck) {
            console.warn('[CKEditorStrategy] CKEDITOR 전역 객체를 찾을 수 없습니다.');
            return;
        }

        const editorId = element.id || `editor_${fieldName}`;
        if (!element.id) {
            element.id = editorId;
        }

        // 중복 초기화 방지를 위해 기존 인스턴스 파괴
        if (ck.instances[editorId]) {
            ck.instances[editorId].destroy(true);
        }

        const editor = ck.replace(editorId, {
            toolbar: [
                { name: 'document', items: [ 'Source' ] },
                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                { name: 'links', items: [ 'Link', 'Unlink' ] },
                { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'tools', items: [ 'Maximize' ] }
            ]
        });

        editor.setData(value || '');

        editor.on('change', () => {
            onChange(editor.getData());
        });

        this.instances[fieldName] = editor;
    }

    public destroy(fieldName: string): void {
        const editor = this.instances[fieldName];
        if (editor) {
            editor.destroy(true);
            delete this.instances[fieldName];
        }
    }

    public setValue(fieldName: string, value: string): void {
        const editor = this.instances[fieldName];
        if (editor) {
            const currentData = editor.getData();
            if (currentData !== value) {
                editor.setData(value || '');
            }
        }
    }
}
