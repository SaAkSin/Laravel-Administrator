export class RelationSelectController {
    public open = false;
    public search = '';
    public options: any[] = [];
    public selectedItems: any[] = [];
    public loading = false;
    public focusedIndex = -1;
    private searchTimer: any = null;

    private config: any;
    private selfProxy: any = null;

    // Alpine.js 주입 매직 속성 타입 지정
    private $root!: any;
    private $watch!: (path: string | (() => any), callback: (val: any) => void) => void;
    private $nextTick!: (callback: () => void) => void;
    private $refs!: Record<string, HTMLElement>;
    private $el!: HTMLElement;

    constructor(config: any) {
        this.config = config;
        Object.defineProperty(this, 'filteredOptions', {
            get: function(this: any) {
                const self = this.selfProxy || this;
                if (self.config.autocomplete) {
                    return self.options;
                }
                if (!self.search) {
                    return self.options;
                }
                const q = self.search.toLowerCase();
                return self.options.filter((opt: any) => {
                    const nameStr = opt.text || opt.name || '';
                    return nameStr.toLowerCase().includes(q);
                });
            },
            enumerable: true,
            configurable: true
        });
    }

    public init(): void {
        this.selfProxy = (window as any).Alpine ? (window as any).Alpine.$data(this.$el) : this;
        const self = this.selfProxy || this;

        console.log(`[RelationSelect:init] field_name: ${self.config.field.field_name}, type: ${self.config.type}, autocomplete: ${self.config.autocomplete}, selfProxy: ${this.selfProxy !== this}`);

        // 부모 모델 속성 변경 및 autocompleteData 변경 감시 (Alpine.effect로 반응성 완전 보장)
        if ((window as any).Alpine && typeof (window as any).Alpine.effect === 'function') {
            (window as any).Alpine.effect(() => {
                const innerSelf = this.selfProxy || this;
                
                // 의존성 추적을 위한 부모 속성 접근
                const modelVal = innerSelf.config.type === 'filter'
                    ? innerSelf.$root.filters[innerSelf.config.filterIndex]?.value
                    : innerSelf.$root[innerSelf.config.field.field_name];
                
                const autoKey = innerSelf.config.field.field_name + '_autocomplete';
                const autoData = innerSelf.$root.autocompleteData[autoKey];
                
                console.log(`[RelationSelect:effect] Triggered. field: ${innerSelf.config.field.field_name}, modelVal:`, modelVal, `autoData keys:`, autoData ? Object.keys(autoData) : 'null');
                innerSelf.syncValueFromModel();
            });
        } else {
            self.syncValueFromModel();
        }

        if (!self.config.autocomplete) {
            const optKey = self.config.type === 'filter' 
                ? 'filter_' + self.config.field.field_name 
                : 'edit_' + self.config.field.field_name;
            
            self.options = self.$root.listOptions[optKey] || self.config.field.options || [];

            self.$watch(`$root.listOptions.${optKey}`, (newVal) => {
                console.log(`[RelationSelect:$watch listOptions] key: ${optKey}`, newVal);
                self.options = newVal || [];
                self.syncValueFromModel();
            });
        }

        // 드롭다운 개방 시 검색창 자동 포커스
        self.$watch('open', (newVal) => {
            if (newVal) {
                self.focusedIndex = -1;
                self.$nextTick(() => {
                    if (self.$refs.searchInput) {
                        self.$refs.searchInput.focus();
                    }
                });
            }
        });

        self.$watch('search', (newVal) => {
            self.focusedIndex = -1;
            if (self.config.autocomplete) {
                // 검색어가 빈 값인 경우 불필요한 API 호출을 방지합니다.
                if (!self.search) {
                    return;
                }
                if (self.searchTimer) {
                    clearTimeout(self.searchTimer);
                }
                self.searchTimer = setTimeout(() => {
                    self.fetchAutocomplete();
                }, 250);
            }
        });
    }

    public moveFocus(step: number): void {
        const self = this.selfProxy || this;
        const max = (self as any).filteredOptions.length - 1;
        if (max < 0) return;
        
        self.focusedIndex += step;
        if (self.focusedIndex < 0) self.focusedIndex = max;
        if (self.focusedIndex > max) self.focusedIndex = 0;
        
        self.$nextTick(() => {
            if (self.$refs.optionsList && self.$refs.optionsList.children[self.focusedIndex]) {
                const el = self.$refs.optionsList.children[self.focusedIndex] as HTMLElement;
                el.scrollIntoView({ block: 'nearest' });
            }
        });
    }

    public selectFocused(): void {
        const self = this.selfProxy || this;
        const opts = (self as any).filteredOptions;
        if (self.focusedIndex >= 0 && self.focusedIndex < opts.length) {
            self.selectItem(opts[self.focusedIndex]);
        } else if (opts.length > 0) {
            self.selectItem(opts[0]);
        }
    }

    public syncValueFromModel(): void {
        const self = this.selfProxy || this;
        const modelVal = self.config.type === 'filter'
            ? self.$root.filters[self.config.filterIndex]?.value
            : self.$root[self.config.field.field_name];

        console.log(`[RelationSelect:syncValueFromModel] field: ${self.config.field.field_name}, modelVal:`, modelVal);

        let ids: string[] = [];
        if (Array.isArray(modelVal)) {
            ids = modelVal.map(String).filter(id => id !== '' && id !== 'null' && id !== 'undefined' && id !== '0');
        } else if (modelVal !== undefined && modelVal !== null && modelVal !== '' && modelVal !== 0 && modelVal !== '0') {
            ids = String(modelVal).split(',').map(s => s.trim()).filter(Boolean);
        }

        const autoKey = self.config.field.field_name + '_autocomplete';
        const autoData = self.$root.autocompleteData[autoKey] || {};

        console.log(`[RelationSelect:syncValueFromModel] parsed ids:`, ids, `autoData keys:`, Object.keys(autoData));

        self.selectedItems = ids.map(id => {
            if (autoData[id]) {
                console.log(`[RelationSelect:syncValueFromModel] Found in autoData:`, id, autoData[id]);
                return { id: id, text: autoData[id].text || autoData[id].name || id };
            }
            const found = self.options.find(opt => String(opt.id) === id);
            if (found) {
                console.log(`[RelationSelect:syncValueFromModel] Found in options:`, id, found);
                return { id: id, text: found.text || found.name || id };
            }
            console.log(`[RelationSelect:syncValueFromModel] Not found anywhere, fallback to id:`, id);
            return { id: id, text: id };
        });

        console.log(`[RelationSelect:syncValueFromModel] Result selectedItems:`, JSON.stringify(self.selectedItems));
    }

    public async fetchAutocomplete(): Promise<void> {
        const self = this.selfProxy || this;
        if (!self.config.autocomplete) return;
        // 검색어가 빈 값인 경우 비동기 요청을 전송하지 않습니다.
        if (!self.search) {
            return;
        }
        self.loading = true;

        const data: Record<string, any> = {
            term: self.search,
            page: 1,
            field: self.config.field.field_name,
            type: self.config.type,
            constraints: {},
            selectedItems: self.config.type === 'filter'
                ? self.$root.filters[self.config.filterIndex].value
                : self.$root[self.config.field.field_name]
        };

        if (self.config.field.constraints) {
            Object.keys(self.config.field.constraints).forEach(key => {
                data.constraints[key] = self.$root[key];
            });
        }

        const url = `${window.base_url}${self.$root.modelName}/update_options`;

        try {
            // AdminController 내의 apiService 인스턴스를 직접 활용하여 통신
            const response = await self.$root.apiService.request<any>(url, {
                method: 'POST',
                data: { fields: [data] }
            });

            const results = response[self.config.field.field_name] || [];
            self.options = results;

            const autoKey = self.config.field.field_name + '_autocomplete';
            if (!self.$root.autocompleteData[autoKey]) {
                self.$root.autocompleteData[autoKey] = {};
            }
            results.forEach((item: any) => {
                self.$root.autocompleteData[autoKey][item.id] = item;
            });
        } catch (e) {
            console.error(e);
        } finally {
            self.loading = false;
        }
    }

    public selectItem(item: any): void {
        const self = this.selfProxy || this;
        const fieldName = self.config.field.field_name;
        
        if (self.config.multiple) {
            const currentVal = self.config.type === 'filter'
                ? self.$root.filters[self.config.filterIndex].value
                : self.$root[fieldName];

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

            if (self.config.type === 'filter') {
                // 부모의 filters[index].value 감시자에서 updateRows()를 자동 호출하므로 할당만 진행합니다.
                self.$root.filters[self.config.filterIndex].value = self.config.field.type === 'belongs_to_many' ? ids : ids.join(',');
            } else {
                self.$root[fieldName] = self.config.field.type === 'belongs_to_many' || self.config.field.type === 'has_many' ? ids : ids.join(',');
            }
        } else {
            if (self.config.type === 'filter') {
                // 부모의 filters[index].value 감시자에서 updateRows()를 자동 호출하므로 할당만 진행합니다.
                self.$root.filters[self.config.filterIndex].value = item.id;
            } else {
                self.$root[fieldName] = item.id;
            }
            self.open = false;
        }
        self.search = '';
    }

    public removeItem(item: any): void {
        const self = this.selfProxy || this;
        const fieldName = self.config.field.field_name;
        
        const currentVal = self.config.type === 'filter'
            ? self.$root.filters[self.config.filterIndex].value
            : self.$root[fieldName];

        let ids: string[] = [];
        if (Array.isArray(currentVal)) {
            ids = currentVal.map(String);
        } else if (currentVal !== undefined && currentVal !== null && currentVal !== '') {
            ids = String(currentVal).split(',').map(s => s.trim()).filter(Boolean);
        }

        ids = ids.filter(id => String(id) !== String(item.id));

        if (self.config.type === 'filter') {
            // 부모의 filters[index].value 감시자에서 updateRows()를 자동 호출하므로 할당만 진행합니다.
            self.$root.filters[self.config.filterIndex].value = self.config.field.type === 'belongs_to_many' ? ids : ids.join(',');
        } else {
            self.$root[fieldName] = self.config.field.type === 'belongs_to_many' || self.config.field.type === 'has_many' ? ids : ids.join(',');
        }
    }

    public clearSelection(): void {
        const self = this.selfProxy || this;
        const fieldName = self.config.field.field_name;
        if (self.config.type === 'filter') {
            // 부모의 filters[index].value 감시자에서 updateRows()를 자동 호출하므로 할당만 진행합니다.
            self.$root.filters[self.config.filterIndex].value = '';
        } else {
            self.$root[fieldName] = '';
        }
        self.selectedItems = [];
        self.search = '';
        
        if (self.config.autocomplete) {
            self.options = [];
        }
    }
}
