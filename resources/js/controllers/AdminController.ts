import { AdminApiService } from '../services/AdminApiService';
import { EditorContext } from '../strategies/EditorContext';

// 모듈 범위의 로컬 언로드 플래그
let isUnloading = false;
window.addEventListener('beforeunload', () => {
    isUnloading = true;
});

export class AdminController {
    // 반응성 속성 선언 (Alpine.js 바인딩용)
    public initialized = false;
    public freezeUpdateRows = false;
    public modelName = '';
    public modelTitle = '';
    public modelSingle = '';
    public baseUrl = '';
    public primaryKey = 'id';
    public expandWidth: number | null = null;
    public rows: any[] = [];
    public rowsPerPage = 20;
    public rowsPerPageOptions: any[] = [];
    public boolOptions = [
        { id: 'true', text: 'Yes' },
        { id: 'false', text: 'No' }
    ];
    public file_url = '';
    public columns: any[] = [];
    public listOptions: Record<string, any> = {};
    public sortOptions = {
        field: null as string | null,
        direction: null as string | null
    };
    public pagination = {
        page: 1,
        last: 1,
        total: 0,
        per_page: 20
    };
    public filters: any[] = [];
    public editFields: any[] = [];
    public originalEditFields: any[] = [];
    public originalData: Record<string, any> = {};
    public activeItem: any = null;
    public lastItem: any = null;
    public loadingItem = false;
    public itemLoadingId: any = null;
    public loadingRows = false;
    public rowLoadingId = 0;
    public freezeForm = false;
    public freezeActions = false;
    public freezeConstraints = false;
    public constraintsQueue: Record<string, any> = {};
    public holdConstraintsQueue = true;
    public actions: any[] = [];
    public globalActions: any[] = [];
    public actionPermissions: Record<string, any> = {};
    public languages: Record<string, string> = {};
    public statusMessage = '';
    public statusMessageType = '';
    public globalStatusMessage = '';
    public globalStatusMessageType = '';
    public itemLink: string | null = null;
    public autocompleteData: Record<string, any> = {};
    public columnHidePoints: Record<number, number> = {};
    public dataTableScrollable = true;
    public historyStarted = false;
    public showFilters = true;

    // 객체 지향 캡슐화 인스턴스
    public apiService!: AdminApiService;
    private editorContext: EditorContext;
    private selfProxy: any = null;

    // Alpine.js 매직 헬퍼 참조용 선언
    private $watch!: (path: string, callback: (val: any) => void) => void;
    private $nextTick!: (callback: () => void) => void;

    constructor() {
        this.editorContext = new EditorContext();

        // Laravel의 기본 모델 속성들을 주입받을 수 있도록 최상위 데이터 로드 설정
        const defaultModel = (window.adminData && (window.adminData.data_model || window.adminData.data)) || {};
        Object.keys(defaultModel).forEach(key => {
            (this as any)[key] = defaultModel[key];
        });

        // 모든 edit_fields를 생성자 시점에 미리 정의하여 완벽한 반응형 속성(reactivity)을 보장합니다.
        const editFields = (window.adminData && window.adminData.edit_fields) || [];
        const fieldsArray = Array.isArray(editFields) ? editFields : Object.values(editFields);
        fieldsArray.forEach((field: any) => {
            if (field && field.field_name && !(field.field_name in this)) {
                const isMultiple = field.type === 'belongs_to_many' || field.type === 'has_many' || !!field.multiple_values;
                (this as any)[field.field_name] = isMultiple ? [] : '';
            }
        });
    }

    get isFirstPage(): boolean {
        return parseInt(this.pagination.page as any) === 1;
    }

    get isLastPage(): boolean {
        return parseInt(this.pagination.page as any) === parseInt(this.pagination.last as any);
    }

    get settingsTitle(): string {
        return this.modelTitle;
    }

    public init(): void {
        const el = document.getElementById('admin_page');
        this.selfProxy = el && (window as any).Alpine ? (window as any).Alpine.$data(el) : this;

        if (!window.adminData) {
            console.error('글로벌 adminData 객체를 찾을 수 없습니다.');
            return;
        }

        // API 서비스 초기화
        this.apiService = new AdminApiService(window.base_url || '', window.csrf || '');

        // 1. 기본 프로퍼티 바인딩
        this.modelName = window.adminData.model_name || '';
        this.modelTitle = window.adminData.model_title || '';
        this.modelSingle = window.adminData.model_single || '';
        this.baseUrl = window.base_url || '';
        this.primaryKey = window.adminData.primary_key || 'id';
        this.expandWidth = window.adminData.expand_width || null;
        this.rows = window.adminData.rows ? window.adminData.rows.results : [];
        this.rowsPerPage = window.adminData.rows_per_page || 20;
        this.file_url = window.file_url || '';

        if (window.adminData.rows) {
            this.pagination.page = window.adminData.rows.page || 1;
            this.pagination.last = window.adminData.rows.last || 1;
            this.pagination.total = window.adminData.rows.total || 0;
            this.pagination.per_page = window.adminData.rows_per_page || 20;
        }

        if (window.adminData.sortOptions) {
            this.sortOptions.field = window.adminData.sortOptions.field;
            this.sortOptions.direction = window.adminData.sortOptions.direction;
        }

        this.actions = window.adminData.actions || [];
        this.globalActions = window.adminData.global_actions || [];
        this.actionPermissions = window.adminData.action_permissions || {};
        this.languages = window.adminData.languages || {};

        // 2. 1부터 100까지의 rowsPerPageOptions 채우기
        this.rowsPerPageOptions = [];
        for (let i = 1; i <= 100; i++) {
            this.rowsPerPageOptions.push({ id: i, text: String(i) });
        }

        // 3. columns, filters, editFields 속성 변환 및 전처리
        this.columns = this.prepareColumns();
        this.filters = this.prepareFilters();
        this.originalEditFields = window.adminData.edit_fields || [];
        this.editFields = this.prepareEditFields(this.originalEditFields);

        // editFields의 필드명이 컨트롤러 속성에 없는 경우 반응형 멤버로 초기화합니다.
        if (this.editFields) {
            this.editFields.forEach(field => {
                if (field && field.field_name && !(field.field_name in this)) {
                    const isMultiple = field.type === 'belongs_to_many' || field.type === 'has_many' || !!field.multiple_values;
                    (this as any)[field.field_name] = isMultiple ? [] : '';
                }
            });
        }

        // 4. 연관 관계 리스트 바인딩
        this.initRelationships();

        // 5. history.js 히스토리 상태 및 브라우저 이벤트 리스너 바인딩
        this.initHistory();
        this.initEvents();

        // 6. 상태 변화 감시자(Watchers) 등록
        this.$watch('rowsPerPage', (value) => {
            this.updateRowsPerPage(parseInt(value));
        });

        // 필터 데이터 변경 감시
        this.filters.forEach((filter, index) => {
            this.$watch(`filters[${index}].value`, (val) => {
                if (filter.type === 'key') {
                    const intVal = isNaN(parseInt(val)) ? '' : parseInt(val);
                    this.filters[index].value = intVal;
                }
                this.updateRows();
            });

            if ('min_value' in filter) {
                this.$watch(`filters[${index}].min_value`, () => this.updateRows());
            }
            if ('max_value' in filter) {
                this.$watch(`filters[${index}].max_value`, () => this.updateRows());
            }
        });

        // 제약 조건 필드 변경 감시
        this.editFields.forEach((field) => {
            if (field.constraints && Object.keys(field.constraints).length > 0) {
                this.establishFieldConstraints(field);
            }
        });

        // 레이아웃 높이 및 데이터 테이블 크기 초기 조정
        this.resizePage();

        // 컴포넌트 활성화 대기 시간 적용 (스켈레톤 리렌더링 최적화 50ms)
        setTimeout(() => {
            this.initialized = true;
            this.resizePage();
        }, 50);

        window.addEventListener('load', () => {
            this.resizePage();
        });
    }

    // 에디터 생성 위임 인터페이스 메소드
    public initEditor(element: HTMLElement, fieldName: string, type: string): void {
        const strategy = this.editorContext.getStrategy(type);
        if (strategy) {
            strategy.initialize(element, fieldName, (this as any)[fieldName] || '', (newVal) => {
                (this as any)[fieldName] = newVal;
            });

            // 모델 값 변화에 따른 에디터 갱신 감시자 바인딩
            this.$watch(fieldName, (newVal) => {
                strategy.setValue(fieldName, newVal || '');
            });
        }
    }

    private prepareColumns(): any[] {
        const columns: any[] = [];
        const colModel = window.adminData.column_model || [];
        colModel.forEach(column => {
            columns.push({
                ...column,
                visible: !!column.visible
            });
        });
        return columns;
    }

    private prepareFilters(): any[] {
        const filters: any[] = [];
        const rawFilters = window.adminData.filters || {};
        const filterItems = Array.isArray(rawFilters) ? rawFilters : Object.values(rawFilters);
        
        filterItems.forEach(filter => {
            const prepared = { ...filter };
            prepared.value = filter.value !== undefined ? filter.value : null;
            if ('min_value' in filter) prepared.min_value = filter.min_value !== undefined ? filter.min_value : null;
            if ('max_value' in filter) prepared.max_value = filter.max_value !== undefined ? filter.max_value : null;
            
            if (filter.relationship) {
                prepared.loadingOptions = false;
            }
            prepared.field_id = 'filter_field_' + filter.field_name;
            filters.push(prepared);
        });
        return filters;
    }

    private prepareEditFields(editFieldsSrc: any[]): any[] {
        const fields: any[] = [];
        const srcList = Array.isArray(editFieldsSrc) ? editFieldsSrc : Object.values(editFieldsSrc);
        
        srcList.forEach((field, ind) => {
            const prepared = { ...field };
            if (field.relationship) {
                prepared.loadingOptions = false;
                prepared.constraintLoading = false;
            }
            if (field.type === 'image' || field.type === 'file') {
                prepared.uploading = false;
                prepared.upload_percentage = 0;
            }
            prepared.field_id = 'edit_field_' + ind;
            fields.push(prepared);
        });
        return fields;
    }

    private initRelationships(): void {
        this.filters.forEach((filter, ind) => {
            if (filter.relationship) {
                this.listOptions[ind] = filter.options || [];
                this.listOptions['filter_' + filter.field_name] = filter.options || [];
            }
            if (filter.autocomplete) {
                const autoKey = filter.field_name + '_autocomplete';
                if (!(autoKey in this.autocompleteData)) {
                    this.autocompleteData[autoKey] = {};
                }
                if (filter.options) {
                    filter.options.forEach((option: any) => {
                        this.autocompleteData[autoKey][option.id] = option;
                    });
                }
            }
        });

        this.editFields.forEach((field, ind) => {
            if (field.relationship) {
                this.listOptions[ind] = field.options || [];
                this.listOptions['edit_' + field.field_name] = field.options || [];
                this.listOptions[field.field_name] = field.options || [];
            }
            if (field.autocomplete) {
                const autoKey = field.field_name + '_autocomplete';
                if (!(autoKey in this.autocompleteData)) {
                    this.autocompleteData[autoKey] = {};
                }
                if (field.options) {
                    field.options.forEach((option: any) => {
                        this.autocompleteData[autoKey][option.id] = option;
                    });
                }
            }
        });
    }

    // this 안전 보장을 위한 멤버 화살표 함수 선언
    public uploadFile = async (event: any, field: any): Promise<void> => {
        const self: AdminController = this.selfProxy || this;
        const file = event.target.files[0];
        if (!file) return;

        if (field.size_limit && file.size > field.size_limit * 1024 * 1024) {
            alert((self.languages['file_too_large'] || '파일 용량이 너무 큽니다. 제한: ') + field.size_limit + 'MB');
            event.target.value = '';
            return;
        }

        field.uploading = true;
        field.upload_percentage = 0;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', window.csrf || (window.adminData && window.adminData.csrf) || '');

        const url = `${window.base_url}${self.modelName}/${field.field_name}/file_upload`;

        try {
            const response = await new Promise<any>((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', url);
                
                xhr.setRequestHeader('X-CSRF-TOKEN', window.csrf || (window.adminData && window.adminData.csrf) || '');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        field.upload_percentage = percent;
                    }
                };

                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            resolve(JSON.parse(xhr.responseText));
                        } catch (err) {
                            reject(new Error('JSON 파싱 오류'));
                        }
                    } else {
                        reject(new Error(`HTTP 오류! 상태코드: ${xhr.status}`));
                    }
                };

                xhr.onerror = () => reject(new Error('네트워크 오류'));
                xhr.send(formData);
            });

            field.uploading = false;
            event.target.value = '';

            if (response.filename && (!response.errors || Object.keys(response.errors).length === 0)) {
                (self as any)[field.field_name] = response.filename;
            } else {
                alert(response.errors ? JSON.stringify(response.errors) : '업로드 실패');
            }
        } catch (error) {
            field.uploading = false;
            event.target.value = '';
            console.error('[어드민 에러] 파일 업로드 실패:', error);
            alert('파일 업로드 중 네트워크 오류가 발생했습니다.');
        }
    };

    public save = async (event?: any): Promise<void> => {
        const self: AdminController = this.selfProxy || this;
        const saveData: Record<string, any> = {};

        self.editFields.forEach(field => {
            if (field && field.field_name) {
                let val = (self as any)[field.field_name];
                if (Array.isArray(val)) {
                    val = val.join(',');
                }
                saveData[field.field_name] = val;
            }
        });

        saveData._token = window.csrf || (window.adminData && window.adminData.csrf);

        self.statusMessage = self.languages['saving'] || 'Saving...';
        self.statusMessageType = '';
        self.freezeForm = true;

        const url = window.save_url || `${window.base_url}${self.modelName}/save`;

        try {
            const response = await self.apiService.request<any>(url, {
                method: 'POST',
                data: saveData
            });

            self.freezeForm = false;

            if (response.success) {
                const savedMsg = self.languages['saved'] || 'Settings saved.';
                self.statusMessage = savedMsg;
                self.statusMessageType = 'success';
                
                setTimeout(() => {
                    if (self.statusMessage === savedMsg) {
                        self.statusMessage = '';
                        self.statusMessageType = '';
                    }
                }, 3000);
            } else {
                self.statusMessage = response.errors || 'Save failed';
                self.statusMessageType = 'error';
            }
        } catch (error) {
            self.freezeForm = false;
            self.statusMessage = 'A network error occurred.';
            self.statusMessageType = 'error';
        }
    };

    public saveItem = async (): Promise<void> => {
        const self: AdminController = this.selfProxy || this;
        const saveData: Record<string, any> = {};

        const fields = window.adminData.data_model || {};
        Object.keys(fields).forEach(key => {
            saveData[key] = (self as any)[key];
        });

        self.editFields.forEach(field => {
            if (field && field.field_name) {
                let val = (self as any)[field.field_name];
                if (Array.isArray(val)) {
                    val = val.join(',');
                }
                saveData[field.field_name] = val;
            }
        });

        saveData._token = window.csrf || (window.adminData && window.adminData.csrf);

        if (!saveData[self.primaryKey]) {
            delete saveData[self.primaryKey];
        }

        self.editFields.forEach(field => {
            if (field.relationship && !field.external && saveData[field.field_name] === '') {
                saveData[field.field_name] = false;
            }
        });

        self.statusMessage = self.languages['saving'] || 'Saving...';
        self.statusMessageType = '';
        self.freezeForm = true;

        const url = `${window.base_url}${self.modelName}/${(self as any)[self.primaryKey] || 0}/save`;

        try {
            const response = await self.apiService.request<any>(url, {
                method: 'POST',
                data: saveData
            });

            self.freezeForm = false;
            self.resizePage();

            if (response.success) {
                const savedMsg = self.languages['saved'] || 'Item saved.';
                self.statusMessage = savedMsg;
                self.statusMessageType = 'success';
                
                setTimeout(() => {
                    if (self.statusMessage === savedMsg) {
                        self.statusMessage = '';
                        self.statusMessageType = '';
                    }
                }, 3000);
                
                self.freezeUpdateRows = true;
                
                self.setData(response.data);
                await self.updateSelfRelationships();
                
                setTimeout(async () => {
                    self.freezeUpdateRows = false;
                    await self.updateRows();
                }, 50);

                setTimeout(() => {
                    if (window.History && window.History.pushState) {
                        window.History.pushState({ modelName: self.modelName }, null, window.route + self.modelName);
                    }
                }, 200);
            } else {
                self.statusMessage = response.errors || 'Save failed';
                self.statusMessageType = 'error';
            }
        } catch (error) {
            self.freezeForm = false;
            self.statusMessage = 'A network error occurred.';
            self.statusMessageType = 'error';
            self.resizePage();
        }
    };

    public deleteItem = async (): Promise<boolean | void> => {
        const self: AdminController = this.selfProxy || this;
        const conf = confirm(self.languages['delete_active_item'] || 'Are you sure you want to delete this item?');
        if (!conf) return false;

        self.statusMessage = self.languages['deleting'] || 'Deleting...';
        self.statusMessageType = '';
        self.freezeForm = true;

        const url = `${window.base_url}${self.modelName}/${(self as any)[self.primaryKey]}/delete`;

        try {
            const response = await self.apiService.request<any>(url, {
                method: 'POST',
                data: { _token: window.csrf || (window.adminData && window.adminData.csrf) }
            });

            self.freezeForm = false;
            self.resizePage();

            if (response.success) {
                const deletedMsg = self.languages['deleted'] || 'Item deleted.';
                self.statusMessage = deletedMsg;
                self.statusMessageType = 'success';
                
                setTimeout(() => {
                    if (self.statusMessage === deletedMsg) {
                        self.statusMessage = '';
                        self.statusMessageType = '';
                    }
                }, 3000);
                
                await self.updateRows();
                await self.updateSelfRelationships();

                setTimeout(() => {
                    self.clearItem();
                    if (window.History && window.History.pushState) {
                        window.History.pushState({ modelName: self.modelName }, null, window.route + self.modelName);
                    }
                }, 500);
            } else {
                self.statusMessage = response.error || 'Delete failed';
                self.statusMessageType = 'error';
            }
        } catch (error) {
            self.freezeForm = false;
            self.statusMessage = 'A network error occurred.';
            self.statusMessageType = 'error';
            self.resizePage();
        }
    };

    public clickItem(id: any): void {
        if (!this.loadingItem && this.activeItem !== id && this.actionPermissions.view) {
            this.getItem(id);

            if (window.History && window.History.pushState) {
                window.History.pushState({ modelName: this.modelName, id: id }, null, window.route + this.modelName + '/' + id);
            }
        }
    }

    public async getItem(id: any): Promise<void> {
        const self: AdminController = this.selfProxy || this;
        self.loadingItem = true;

        window.adminData.edit_fields = self.originalEditFields;
        self.editFields = self.prepareEditFields(self.originalEditFields);

        self.holdConstraintsQueue = true;

        const defaultModel = window.adminData.data_model || {};
        Object.keys(defaultModel).forEach(key => {
            (self as any)[key] = defaultModel[key];
        });
        self.originalData = {};

        window.scrollTo({ top: 0, behavior: 'smooth' });

        if (!id) {
            self.setUpNewItem();
            return;
        }

        self.freezeConstraints = true;
        self.itemLoadingId = id;

        const url = `${window.base_url}${self.modelName}/${id}`;

        try {
            const data = await self.apiService.request<any>(url);

            if (data.success === false && data.errors) {
                alert(data.errors);
                return;
            }

            if (self.itemLoadingId !== id) {
                if (self.itemLoadingId === null) {
                    self.loadingItem = false;
                    self.clearItem();
                }
            } else {
                self.setData(data);
            }
        } catch (error) {
            self.loadingItem = false;
            console.error('[어드민 에러] 상세 조회 실패:', error);
            alert('데이터를 가져오는 중 오류가 발생했습니다.');
        }
    }

    private setUpNewItem(): void {
        this.itemLoadingId = null;
        this.activeItem = 0;
        this.lastItem = 0;
        this.loadingItem = false;
        
        this.actionPermissions = (window.adminData && window.adminData.action_permissions) || {};

        this.runConstraintsQueue();
    }

    private setData(data: any): void {
        const self: AdminController = this.selfProxy || this;
        self.activeItem = data[self.primaryKey];
        self.loadingItem = false;

        window.adminData.edit_fields = data.administrator_edit_fields || [];
        self.editFields = self.prepareEditFields(window.adminData.edit_fields);

        if (self.editFields) {
            self.editFields.forEach(field => {
                if (field && field.field_name && !(field.field_name in self)) {
                    (self as any)[field.field_name] = field.type === 'belongs_to_many' || field.type === 'has_many' ? [] : '';
                }
            });
        }

        self.actions = data.administrator_actions || [];
        self.actionPermissions = data.administrator_action_permissions || {};

        self.originalData = data;

        const fields = window.adminData.edit_fields || [];
        const fieldsArray = Array.isArray(fields) ? fields : Object.values(fields);
        fieldsArray.forEach(el => {
            if (el && el.relationship && el.autocomplete) {
                const autoKey = el.field_name + '_autocomplete';
                self.autocompleteData[autoKey] = data[autoKey] || {};
            }
        });

        if (data.admin_item_link) {
            self.itemLink = data.admin_item_link;
        }

        self.lastItem = data[self.primaryKey];

        const defaultModel = window.adminData.data_model || {};
        Object.keys(defaultModel).forEach(key => {
            (self as any)[key] = defaultModel[key];
        });

        Object.keys(data).forEach(key => {
            const isField = key in defaultModel || key === self.primaryKey || (self.editFields && self.editFields.some(f => f.field_name === key));
            if (isField) {
                (self as any)[key] = data[key];
            }
        });

        if (self.editFields) {
            self.editFields.forEach((field, ind) => {
                if (field && field.relationship) {
                    const optKey = field.field_name + '_options';
                    if (data[optKey]) {
                        self.listOptions['edit_' + field.field_name] = data[optKey] || [];
                        self.listOptions[field.field_name] = data[optKey] || [];
                        self.listOptions[ind] = data[optKey] || [];
                    }
                }
            });
        }
        
        if (self.editFields) {
            self.editFields.forEach(field => {
                if (field && field.field_name && (self as any)[field.field_name]) {
                    let val = String((self as any)[field.field_name]);
                    if (field.type === 'datetime') {
                        val = val.replace(' ', 'T');
                        if (val.length > 16) {
                            val = val.substring(0, 16);
                        }
                        (self as any)[field.field_name] = val;
                    } else if (field.type === 'time') {
                        if (val.length > 5) {
                            val = val.substring(0, 5);
                        }
                        (self as any)[field.field_name] = val;
                    }
                }
            });
        }

        self.freezeConstraints = false;
        self.resizePage();
        self.runConstraintsQueue();
    }

    public closeItem(): void {
        this.clearItem();

        if (window.History && window.History.pushState) {
            window.History.pushState({ modelName: this.modelName }, null, window.route + this.modelName);
        }
    }

    public clearItem(): void {
        this.freezeForm = false;
        this.statusMessage = '';
        this.statusMessageType = '';
        this.itemLink = null;
        this.itemLoadingId = null;
        this.activeItem = null;
        this.lastItem = null;
    }

    public addNewItem(): void {
        this.getItem(0);
    }

    public customAction = async (isItem: boolean, action: string, messages: any, confirmation: string, reload: boolean): Promise<boolean | void> => {
        const self: AdminController = this.selfProxy || this;
        const data: Record<string, any> = {
            _token: window.csrf || (window.adminData && window.adminData.csrf),
            action_name: action
        };
        let url;

        if (confirmation) {
            if (!confirm(confirmation)) return false;
        }

        if (isItem) {
            url = `${window.base_url}${self.modelName}/${(self as any)[self.primaryKey]}/custom_action`;
            self.statusMessage = messages.active || 'Performing action...';
            self.statusMessageType = '';
        } else {
            url = `${window.base_url}${self.modelName}/custom_action`;
            data.sortOptions = self.sortOptions;
            data.filters = self.getFilters();
            data.page = self.pagination.page;
            self.globalStatusMessage = messages.active || 'Performing action...';
            self.globalStatusMessageType = '';
        }

        self.freezeForm = true;

        try {
            const response = await self.apiService.request<any>(url, {
                method: 'POST',
                data: data
            });

            self.freezeForm = false;

            if (response.success) {
                const actionSuccessMsg = messages.success || 'Action completed';
                if (isItem) {
                    self.statusMessage = actionSuccessMsg;
                    self.statusMessageType = 'success';
                    self.setData(response.data);
                    
                    setTimeout(() => {
                        if (self.statusMessage === actionSuccessMsg) {
                            self.statusMessage = '';
                            self.statusMessageType = '';
                        }
                    }, 3000);
                } else {
                    self.globalStatusMessage = actionSuccessMsg;
                    self.globalStatusMessageType = 'success';
                    
                    setTimeout(() => {
                        if (self.globalStatusMessage === actionSuccessMsg) {
                            self.globalStatusMessage = '';
                            self.globalStatusMessageType = '';
                        }
                    }, 3000);
                }

                if (response.redirect) {
                    isUnloading = true;
                    window.location.href = response.redirect;
                    return;
                }

                if (response.download) {
                    self.downloadFile(response.download);
                }

                await self.updateRows();

                if (reload) {
                    self.page(self.pagination.page);
                }
            } else {
                if (isItem) {
                    self.statusMessage = response.error || '작업 실패';
                    self.statusMessageType = 'error';
                } else {
                    self.globalStatusMessage = response.error || '작업 실패';
                    self.globalStatusMessageType = 'error';
                }
            }
        } catch (error) {
            self.freezeForm = false;
            const errText = '오류가 발생했습니다.';
            if (isItem) {
                self.statusMessage = errText;
                self.statusMessageType = 'error';
            } else {
                self.globalStatusMessage = errText;
                self.globalStatusMessageType = 'error';
            }
        }
    };

    public downloadFile(url: string): void {
        const hiddenIFrameId = 'hiddenDownloader';
        let iframe = document.getElementById(hiddenIFrameId) as HTMLIFrameElement | null;

        if (iframe === null) {
            iframe = document.createElement('iframe') as HTMLIFrameElement;
            iframe.id = hiddenIFrameId;
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }

        iframe.src = url;
    }

    public updateRows = async (): Promise<void> => {
        const self: AdminController = this.selfProxy || this;
        if (self.freezeUpdateRows) return;

        const id = ++self.rowLoadingId;
        const data = {
            _token: window.csrf || (window.adminData && window.adminData.csrf),
            sortOptions: self.sortOptions,
            filters: self.getFilters(),
            page: self.pagination.page
        };

        if (!self.initialized) return;

        if (!data.page) {
            data.page = 1;
        }

        self.setLoadingRows(true);

        const url = `${window.base_url}${self.modelName}/results`;

        try {
            const response = await self.apiService.request<any>(url, {
                method: 'POST',
                data: data
            });

            if (self.rowLoadingId !== id) {
                return;
            }

            self.applyRowsUpdate(response);
        } catch (error) {
            console.error('[디버그] updateRows API 요청 실패:', error);
            if (self.rowLoadingId === id) {
                self.setLoadingRows(false);
            }
        }
    };

    public applyRowsUpdate(response: any): void {
        this.pagination.page = parseInt(response.last ? response.page : response.last) || 1;
        this.pagination.last = parseInt(response.last) || 1;
        this.pagination.total = parseInt(response.total) || 0;
        this.rows = response.results || [];
        this.loadingRows = false;
    }

    public setLoadingRows(loading: boolean): void {
        this.loadingRows = loading;
    }

    public setSortOptions(field: string): boolean | void {
        let found = false;

        this.columns.forEach(col => {
            if (field === col.sort_field || field === col.column_name) {
                found = true;
            }
        });

        if (!found) return false;

        if (field === this.sortOptions.field) {
            this.sortOptions.direction = this.sortOptions.direction === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortOptions.direction = 'asc';
        }

        this.sortOptions.field = field;
        this.updateRows();
    }

    public page(page: any): void {
        const currPage = parseInt(this.pagination.page as any) || 1;
        let newPage = 1;
        const lastPage = parseInt(this.pagination.last as any) || 1;

        if (page === 'prev') {
            if (currPage > 1) {
                newPage = currPage - 1;
            }
        } else if (page === 'next') {
            if (currPage < lastPage) {
                newPage = currPage + 1;
            } else {
                newPage = lastPage;
            }
        } else {
            const parsed = parseInt(page);
            if (!isNaN(parsed)) {
                newPage = parsed;
                if (newPage > lastPage) {
                    newPage = lastPage;
                }
                if (newPage < 1) {
                    newPage = 1;
                }
            }
        }

        this.pagination.page = parseInt(newPage as any);
        this.updateRows();
    }

    public updateRowsPerPage = async (rows: number): Promise<void> => {
        const self: AdminController = this.selfProxy || this;
        const url = window.rows_per_page_url || '';
        try {
            await self.apiService.request<any>(url, {
                method: 'POST',
                data: {
                    _token: window.csrf || (window.adminData && window.adminData.csrf),
                    rows: rows
                }
            });
        } catch (error) {
            console.error(error);
        } finally {
            self.updateRows();
        }
    };

    private getFilters(): any[] {
        const filters: any[] = [];
        const observables = ['value', 'min_value', 'max_value'];

        this.filters.forEach(el => {
            const filter: any = {
                field_name: el.field_name,
                type: el.type,
                value: el.value ? el.value : null
            };

            observables.forEach(obs => {
                if (obs in el) {
                    filter[obs] = el[obs] ? el[obs] : null;

                    if (obs === 'value' && filter[obs] && el.type === 'belongs_to_many' && typeof filter[obs] === 'string') {
                        filter.value = filter.value.split(',');
                    }
                }
            });

            filters.push(filter);
        });

        return filters;
    }

    public fieldIsDirty(field: string): boolean {
        return this.originalData[field] !== (this as any)[field];
    }

    public updateSelfRelationships = async (): Promise<void> => {
        const self: AdminController = this.selfProxy || this;
        const filterPromises = self.filters.map(async (filter, ind) => {
            const fieldName = filter.field_name;

            if ((!filter.constraints || !filter.constraints.length) && filter.self_relationship) {
                self.filters[ind].loadingOptions = true;

                const url = `${window.base_url}${self.modelName}/update_options`;
                try {
                    const response = await self.apiService.request<any>(url, {
                        method: 'POST',
                        data: {
                            fields: [{
                                type: 'filter',
                                field: fieldName,
                                selectedItems: filter.value
                            }]
                        }
                    });

                    self.listOptions['filter_' + fieldName] = response[fieldName];
                } catch (error) {
                    console.error(error);
                } finally {
                    self.filters[ind].loadingOptions = false;
                }
            }
        });

        const fieldPromises = self.editFields.map(async (field, ind) => {
            const fieldName = field.field_name;

            if ((!field.constraints || !field.constraints.length) && field.self_relationship) {
                self.editFields[ind].loadingOptions = true;

                const url = `${window.base_url}${self.modelName}/update_options`;
                try {
                    const response = await self.apiService.request<any>(url, {
                        method: 'POST',
                        data: {
                            fields: [{
                                type: 'edit',
                                field: fieldName,
                                selectedItems: (self as any)[fieldName]
                            }]
                        }
                    });

                    self.listOptions['edit_' + fieldName] = response[fieldName];
                    self.listOptions[fieldName] = response[fieldName];
                } catch (error) {
                    console.error(error);
                } finally {
                    self.editFields[ind].loadingOptions = false;
                }
            }
        });

        await Promise.all([...filterPromises, ...fieldPromises]);
    };

    private establishFieldConstraints(field: any): void {
        const fieldName = field.field_name;
        const constraintsLength = this.getFieldConstraintsLength(field.field_name);

        Object.keys(field.constraints).forEach(key => {
            this.$watch(key, (val) => {
                if (this.freezeConstraints || field.loadingOptions) return;

                if (!this.constraintsQueue[key]) {
                    this.constraintsQueue[key] = {};
                }

                this.constraintsQueue[key][fieldName] = field;

                const currentQueueLength = Object.keys(this.constraintsQueue[key]).length;

                if (!this.holdConstraintsQueue && (currentQueueLength === constraintsLength)) {
                    this.runConstraintsQueue();
                }
            });
        });
    }

    private getFieldConstraintsLength(key: string): number {
        let length = 0;
        this.editFields.forEach(field => {
            if (field.constraints && field.constraints[key]) {
                length++;
            }
        });
        return length;
    }

    private setConstrainerFreeze(key: string, freeze: boolean): void {
        const self: AdminController = this.selfProxy || this;
        self.editFields.forEach((field, ind) => {
            if (field.field_name === key) {
                self.editFields[ind].constraintLoading = freeze;
            }
        });
    }

    private setFieldLoadingOptions(fieldName: string, type: boolean): void {
        const self: AdminController = this.selfProxy || this;
        self.editFields.forEach((field, ind) => {
            if (field.field_name === fieldName) {
                self.editFields[ind].loadingOptions = type;
            }
        });
    }

    public runConstraintsQueue = async (): Promise<void> => {
        const self: AdminController = this.selfProxy || this;
        const fields = self.buildConstraintsFromQueue();

        if (!fields.length) return;

        self.freezeActions = true;

        const url = `${window.base_url}${self.modelName}/update_options`;

        try {
            const response = await self.apiService.request<any>(url, {
                method: 'POST',
                data: { fields: fields }
            });

            Object.keys(response).forEach(fieldName => {
                const el = response[fieldName] || [];
                const data: Record<string, any> = {};

                el.forEach((e: any) => {
                    data[e.id] = e;
                });

                const autoKey = fieldName + '_autocomplete';
                self.autocompleteData[autoKey] = data;
                self.listOptions[fieldName] = el;

                const editFields = self.editFields;
                editFields.forEach((field, ind) => {
                    if (field.field_name === fieldName) {
                        const currentVal = (self as any)[fieldName];
                        if (Array.isArray(currentVal)) {
                            const filtered = currentVal.filter(id => data[id]);
                            (self as any)[fieldName] = filtered;
                        } else if (currentVal && !data[currentVal]) {
                            (self as any)[fieldName] = '';
                        }
                    }
                });
            });
        } catch (error) {
            console.error(error);
        } finally {
            self.freezeActions = false;

            Object.keys(self.constraintsQueue).forEach(key => {
                const fieldConstraints = self.constraintsQueue[key];
                Object.keys(fieldConstraints).forEach(fieldName => {
                    self.setFieldLoadingOptions(fieldName, false);
                    self.setConstrainerFreeze(key, false);
                });
            });

            self.constraintsQueue = {};
            self.holdConstraintsQueue = false;
        }
    };

    private buildConstraintsFromQueue(): any[] {
        const self: AdminController = this.selfProxy || this;
        const allConstraints: any[] = [];

        Object.keys(self.constraintsQueue).forEach(key => {
            const fieldConstraints = self.constraintsQueue[key];
            Object.keys(fieldConstraints).forEach(fieldName => {
                const field = fieldConstraints[fieldName];
                const constraints: Record<string, any> = {};

                self.setFieldLoadingOptions(fieldName, true);
                self.setConstrainerFreeze(key, true);

                Object.keys(field.constraints).forEach(ckey => {
                    constraints[ckey] = (self as any)[ckey];
                });

                allConstraints.push({
                    constraints: constraints,
                    type: 'edit',
                    field: fieldName,
                    selectedItems: (self as any)[fieldName]
                });
            });
        });

        return allConstraints;
    }

    private initEvents(): void {
        document.addEventListener('click', (e) => {
            const target = (e.target as HTMLElement).closest('div.results_header a.new_item');
            if (target) {
                e.preventDefault();
                if (window.History && window.History.pushState) {
                    window.History.pushState({ modelName: this.modelName, id: 0 }, null, window.route + this.modelName + '/new');
                }
            }

            // 메뉴 클릭 감지 시 isUnloading 플래그 조기 활성화 (중복 리로드 방지)
            const a = (e.target as HTMLElement).closest('a');
            if (a) {
                const isMenuLink = a.closest('header') || a.closest('#menu') || a.closest('#mobile_menu') || a.closest('#right_nav');
                if (isMenuLink) {
                    const href = a.getAttribute('href');
                    const targetAttr = a.getAttribute('target');
                    if (href && !href.startsWith('#') && !href.startsWith('javascript:') && targetAttr !== '_blank') {
                        isUnloading = true;
                    }
                }
            }
        });

        window.addEventListener('resize', this.resizePage.bind(this));
        document.body.addEventListener('mouseup', this.resizePage.bind(this));
        document.body.addEventListener('keypress', this.resizePage.bind(this));

        if (window.History && window.History.Adapter) {
            window.History.Adapter.bind(window, 'statechange', () => {
                if (isUnloading) return;

                const state = window.History.getState();

                if (state.data.ignore || (state.data.init && !this.historyStarted)) return;

                if ('modelName' in state.data) {
                    if (state.data.modelName !== this.modelName) {
                        window.location.reload();
                    }
                }

                if ('id' in state.data) {
                    if (state.data.id !== this.activeItem) {
                        this.getItem(state.data.id);
                    }
                } else {
                    this.clearItem();
                }
            });
        }
    }

    private initHistory(): void {
        const historyData: Record<string, any> = {
            modelName: this.modelName,
            init: true
        };
        let uri = window.route + this.modelName;

        if (window.adminData && 'id' in window.adminData) {
            const timer = setInterval(() => {
                if (window.Alpine) {
                    this.getItem(window.adminData.id);
                    historyData.id = window.adminData.id;
                    uri += '/' + (historyData.id ? historyData.id : 'new');

                    if (window.History && window.History.pushState) {
                        window.History.pushState(historyData, null, uri);
                    }

                    clearInterval(timer);
                }
            }, 100);
        }

        this.historyStarted = true;
    }

    public resizePage(): void {
        setTimeout(() => {
            const winHeight = window.innerHeight;
            const itemEdit = document.querySelector('div.item_edit') as HTMLElement | null;
            const itemEditHeight = itemEdit ? itemEdit.offsetHeight + 50 : 0;
            const usedHeight = winHeight > itemEditHeight ? winHeight - 45 : itemEditHeight;

            const adminPage = document.getElementById('admin_page');
            if (adminPage) {
                adminPage.style.minHeight = `${usedHeight}px`;
            }

            if (!this.dataTableScrollable) {
                this.resizeDataTable();
            } else {
                this.scrollDataTable();
            }
        }, 50);
    }

    private scrollDataTable(): void {
        const tableContainer = document.querySelector('div.table_container') as HTMLElement | null;
        const dataTable = tableContainer ? tableContainer.querySelector('table.results') as HTMLElement | null : null;

        if (!dataTable || !tableContainer) return;
        if (dataTable.parentElement && dataTable.parentElement.classList.contains('table_scrollable')) return;

        const wrapper = document.createElement('div');
        wrapper.classList.add('table_scrollable');
        if (dataTable.parentNode) {
            dataTable.parentNode.insertBefore(wrapper, dataTable);
        }
        wrapper.appendChild(dataTable);
    }

    private resizeDataTable(): void {
        this.columns.forEach((col) => {
            col.visible = true;
        });
    }
}
