import { EditorStrategy } from './EditorStrategy';
import { CKEditorStrategy } from './CKEditorStrategy';
import { QuillEditorStrategy } from './QuillEditorStrategy';

export class EditorContext {
    private strategies: Record<string, EditorStrategy> = {};

    constructor() {
        this.strategies['wysiwyg'] = new CKEditorStrategy();
        this.strategies['wysiwyg2'] = new QuillEditorStrategy();
    }

    public getStrategy(type: string): EditorStrategy | null {
        return this.strategies[type] || null;
    }
}
