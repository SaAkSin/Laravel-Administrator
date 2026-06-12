export interface EditorStrategy {
    initialize(element: HTMLElement, fieldName: string, value: string, onChange: (val: string) => void): void;
    destroy(fieldName: string): void;
    setValue(fieldName: string, value: string): void;
}
