export class RelationSelectController {
    public open = false;
    public search = '';
    public options: any[] = [];
    public selectedItems: any[] = [];
    public loading = false;
    public focusedIndex = -1;

    private config: any;

    // Alpine.js 주입 매직 속성 타입 지정
    private $root!: any;
    private $watch!: (path: string | (() => any), callback: (val: any) => void) => void;
    private $nextTick!: (callback: () => void) => void;
    private $refs!: Record<string, HTMLElement>;
    private $el!: HTMLElement;

    constructor(config: any) {
        this.config = config;
    }

    public init(): void {
        this.syncValueFromModel();

        if (!this.config.autocomplete) {
            const optKey = this.config.type === 'filter' 
                ? 'filter_' + this.config.field.field_name 
                : 'edit_' + this.config.field.field_name;
            
            this.options = this.$root.listOptions[optKey] || this.config.field.options || [];

            this.$watch(`$root.listOptions.${optKey}`, (newVal) => {
                this.options = newVal || [];
                this.syncValueFromModel();
            });
        }

        // 부모 모델 속성 변경 감시
        this.$watch(() => {
            return this.config.type === 'filter'
                ? this.$root.filters[this.config.filterIndex]?.value
                : this.$root[this.config.field.field_name];
        }, () => {
            this.syncValueFromModel();
        });

        // 드롭다운 개방 시 검색창 자동 포커스
        this.$watch('open', (newVal) => {
            if (newVal) {
                this.focusedIndex = -1;
                this.$nextTick(() => {
                    if (this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                });
            }
        });

        this.$watch('search', () => {
            this.focusedIndex = -1;
        });
    }

    public moveFocus = (step: number): void => {
        const max = this.filteredOptions.length - 1;
        if (max < 0) return;
        
        this.focusedIndex += step;
        if (this.focusedIndex < 0) this.focusedIndex = max;
        if (this.focusedIndex > max) this.focusedIndex = 0;
        
        this.$nextTick(() => {
            if (this.$refs.optionsList && this.$refs.optionsList.children[this.focusedIndex]) {
                const el = this.$refs.optionsList.children[this.focusedIndex] as HTMLElement;
                el.scrollIntoView({ block: 'nearest' });
            }
        });
    };

    public selectFocused = (): void => {
        if (this.focusedIndex >= 0 && this.focusedIndex < this.filteredOptions.length) {
            this.selectItem(this.filteredOptions[this.focusedIndex]);
        } else if (this.filteredOptions.length > 0) {
            this.selectItem(this.filteredOptions[0]);
        }
    };

    get filteredOptions(): any[] {
        if (this.config.autocomplete) {
            return this.options;
        }
        if (!this.search) {
            return this.options;
        }
        const q = this.search.toLowerCase();
        return this.options.filter(opt => {
            const nameStr = opt.text || opt.name || '';
            return nameStr.toLowerCase().includes(q);
        });
    }

    public syncValueFromModel = (): void => {
        const modelVal = this.config.type === 'filter'
            ? this.$root.filters[this.config.filterIndex]?.value
            : this.$root[this.config.field.field_name];

        let ids: string[] = [];
        if (Array.isArray(modelVal)) {
            ids = modelVal.map(String).filter(id => id !== '' && id !== 'null' && id !== 'undefined' && id !== '0');
        } else if (modelVal !== undefined && modelVal !== null && modelVal !== '' && modelVal !== 0 && modelVal !== '0') {
            ids = String(modelVal).split(',').map(s => s.trim()).filter(Boolean);
        }

        const autoKey = this.config.field.field_name + '_autocomplete';
        const autoData = this.$root.autocompleteData[autoKey] || {};

        this.selectedItems = ids.map(id => {
            if (autoData[id]) {
                return { id: id, text: autoData[id].text || autoData[id].name || id };
            }
            const found = this.options.find(opt => String(opt.id) === id);
            if (found) {
                return { id: id, text: found.text || found.name || id };
            }
            return { id: id, text: id };
        });
    };

    public fetchAutocomplete = async (): Promise<void> => {
        if (!this.config.autocomplete) return;
        this.loading = true;

        const data: Record<string, any> = {
            term: this.search,
            page: 1,
            field: this.config.field.field_name,
            type: this.config.type,
            constraints: {},
            selectedItems: this.config.type === 'filter'
                ? this.$root.filters[this.config.filterIndex].value
                : this.$root[this.config.field.field_name]
        };

        if (this.config.field.constraints) {
            Object.keys(this.config.field.constraints).forEach(key => {
                data.constraints[key] = this.$root[key];
            });
        }

        const url = `${window.base_url}${this.$root.modelName}/update_options`;

        try {
            // AdminController 내의 apiService 인스턴스를 직접 활용하여 통신
            const response = await this.$root.apiService.request<any>(url, {
                method: 'POST',
                data: { fields: [data] }
            });

            const results = response[this.config.field.field_name] || [];
            this.options = results;

            const autoKey = this.config.field.field_name + '_autocomplete';
            if (!this.$root.autocompleteData[autoKey]) {
                this.$root.autocompleteData[autoKey] = {};
            }
            results.forEach((item: any) => {
                this.$root.autocompleteData[autoKey][item.id] = item;
            });
        } catch (e) {
            console.error(e);
        } finally {
            this.loading = false;
        }
    };

    public selectItem = (item: any): void => {
        const fieldName = this.config.field.field_name;
        
        if (this.config.multiple) {
            const currentVal = this.config.type === 'filter'
                ? this.$root.filters[this.config.filterIndex].value
                : this.$root[fieldName];

            let ids: string[] = [];
            if (Array.isArray(currentVal)) {
                ids = currentVal.map(String);
            } else if (currentVal !== undefined && currentVal !== null && currentVal !== '') {
                ids = String(currentVal).split(',').map(s => s.trim()).filter(Boolean);
            }

            const idStr = String(item.id);
            if (!ids.includes(idStr)) {
                ids.push(idStr);
            }

            if (this.config.type === 'filter') {
                this.$root.filters[this.config.filterIndex].value = this.config.field.type === 'belongs_to_many' ? ids : ids.join(',');
            } else {
                this.$root[fieldName] = this.config.field.type === 'belongs_to_many' || this.config.field.type === 'has_many' ? ids : ids.join(',');
            }
        } else {
            if (this.config.type === 'filter') {
                this.$root.filters[this.config.filterIndex].value = item.id;
            } else {
                this.$root[fieldName] = item.id;
            }
            this.open = false;
        }
        this.search = '';
    };

    public removeItem = (item: any): void => {
        const fieldName = this.config.field.field_name;
        
        const currentVal = this.config.type === 'filter'
            ? this.$root.filters[this.config.filterIndex].value
            : this.$root[fieldName];

        let ids: string[] = [];
        if (Array.isArray(currentVal)) {
            ids = currentVal.map(String);
        } else if (currentVal !== undefined && currentVal !== null && currentVal !== '') {
            ids = String(currentVal).split(',').map(s => s.trim()).filter(Boolean);
        }

        ids = ids.filter(id => String(id) !== String(item.id));

        if (this.config.type === 'filter') {
            this.$root.filters[this.config.filterIndex].value = this.config.field.type === 'belongs_to_many' ? ids : ids.join(',');
        } else {
            this.$root[fieldName] = this.config.field.type === 'belongs_to_many' || this.config.field.type === 'has_many' ? ids : ids.join(',');
        }
    };

    public clearSelection = (): void => {
        const fieldName = this.config.field.field_name;
        if (this.config.type === 'filter') {
            this.$root.filters[this.config.filterIndex].value = '';
        } else {
            this.$root[fieldName] = '';
        }
        this.selectedItems = [];
        this.search = '';
        
        if (this.config.autocomplete) {
            this.options = [];
        }
    };
}
