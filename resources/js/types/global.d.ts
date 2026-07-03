declare module '*.css';

interface AdminData {
    primary_key: string;
    id?: string | number;
    rows?: {
        page: number;
        last: number;
        total: number;
        results: any[];
    };
    rows_per_page?: number;
    sortOptions?: {
        field: string | null;
        direction: string | null;
    };
    model_name: string;
    model_title: string;
    model_single: string;
    expand_width?: number;
    actions?: any[];
    global_actions?: any[];
    filters?: any;
    edit_fields?: any[];
    data_model?: any;
    data?: any;
    column_model?: any[];
    action_permissions?: any;
    languages?: any;
    csrf?: string;
}

interface Window {
    adminData: AdminData;
    csrf?: string;
    base_url?: string;
    save_url?: string;
    route?: string;
    file_url?: string;
    rows_per_page_url?: string;
    marked?: any;
    accounting?: any;
    markdown?: {
        toHTML: (str: string) => string;
    };
    Quill?: any;
    CKEDITOR?: any;
    History?: any;
    Alpine?: any;
}
