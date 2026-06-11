// Tailwind CSS 엔트리를 포함하여 번들러가 함께 빌드할 수 있도록 임포트합니다.
import '../css/app.css';

// 모던 의존성 패키지들을 임포트하여 전역 바인딩하고 번들에 통합합니다.
import { marked } from 'marked';
import accounting from 'accounting';

// 레거시 호환성을 위해 window 객체에 전역 바인딩
window.marked = marked;
window.accounting = accounting;

// 기존 markdown.toHTML 호출 규격을 지원하는 호환 어댑터 탑재
window.markdown = {
    toHTML: (str) => marked.parse(str || '')
};

// Alpine.js 라이브러리를 임포트하고 브라우저 전역 범위(window)에 등록합니다.
import Alpine from 'alpinejs';

// Quill 에디터 코어 모듈 및 테마 CSS를 임포트하여 Vite 번들에 병합합니다.
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
window.Quill = Quill;

/**
 * 중첩된 객체를 x-www-form-urlencoded 쿼리 스트링 포맷으로 직렬화하기 위한 헬퍼 함수
 * jQuery.param()의 직렬화 로직과 호환되도록 설계되었습니다.
 */
function buildParams(prefix, obj, add) {
    if (Array.isArray(obj)) {
        obj.forEach((v, i) => {
            if (/\[\]$/.test(prefix)) {
                add(prefix, v);
            } else {
                buildParams(
                    prefix + "[" + (typeof v === "object" && v !== null ? i : "") + "]",
                    v,
                    add
                );
            }
        });
    } else if (typeof obj === "object" && obj !== null) {
        for (const name in obj) {
            buildParams(prefix + "[" + name + "]", obj[name], add);
        }
    } else {
        add(prefix, obj);
    }
}

function serializeData(obj) {
    const s = [];
    const add = (key, value) => {
        const val = value == null ? "" : value;
        s.push(encodeURIComponent(key) + "=" + encodeURIComponent(val));
    };

    for (const prefix in obj) {
        buildParams(prefix, obj[prefix], add);
    }
    return s.join("&");
}

/**
 * Fetch API 기반 비동기 통신을 지원하는 공통 요청 유틸리티 함수
 * CSRF 토큰 검증(X-CSRF-TOKEN) 및 JSON 응답 파싱을 일관되게 처리합니다.
 */
async function request(url, { method = 'GET', data = null, headers = {} } = {}) {
    const options = {
        method,
        headers: {
            'X-CSRF-TOKEN': window.csrf || (window.adminData && window.adminData.csrf) || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            ...headers
        }
    };

    if (data) {
        if (method === 'POST') {
            options.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
            options.body = serializeData(data);
        } else {
            const queryString = serializeData(data);
            if (queryString) {
                url += (url.includes('?') ? '&' : '?') + queryString;
            }
        }
    }

    const response = await fetch(url, options);
    
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return await response.json();
}

/**
 * Alpine.js 전역 컨트롤러: adminController
 * 글로벌 adminData 객체를 읽고, Alpine.js 반응형 상태 구조에 완전히 매핑합니다.
 */
function adminController() {
    // data_model의 기본 키/값 쌍을 미리 추출하여 Alpine.js가 초기 렌더링 시 반응형으로 추적할 수 있도록 구성합니다.
    const defaultModel = (window.adminData && window.adminData.data_model) || {};

    return {
        // 기존 Laravel Administrator 데이터 모델의 속성들을 최상위에 바인딩
        ...defaultModel,

        // 코어 제어 변수 및 상태 필드
        initialized: false,
        freezeUpdateRows: false,
        modelName: '',
        modelTitle: '',
        modelSingle: '',
        baseUrl: '',
        primaryKey: 'id',
        expandWidth: null,
        rows: [],
        rowsPerPage: 20,
        rowsPerPageOptions: [],
        boolOptions: [
            { id: 'true', text: 'Yes' },
            { id: 'false', text: 'No' }
        ],
        file_url: '',
        columns: [],
        listOptions: {},
        sortOptions: {
            field: null,
            direction: null
        },
        pagination: {
            page: 1,
            last: 1,
            total: 0,
            per_page: 20
        },
        filters: [],
        editFields: [],
        originalEditFields: [],
        originalData: {},
        activeItem: null,
        lastItem: null,
        loadingItem: false,
        itemLoadingId: null,
        loadingRows: false,
        rowLoadingId: 0,
        freezeForm: false,
        freezeActions: false,
        freezeConstraints: false,
        constraintsQueue: {},
        holdConstraintsQueue: true,
        actions: [],
        globalActions: [],
        actionPermissions: {},
        languages: {},
        statusMessage: '',
        statusMessageType: '',
        globalStatusMessage: '',
        globalStatusMessageType: '',
        itemLink: null,
        autocompleteData: {},
        columnHidePoints: {},
        dataTableScrollable: false,
        historyStarted: false,
        showFilters: true,

        // 계산된 속성 (Getters - 타입 불일치 방지를 위해 정수 변환 비교)
        get isFirstPage() {
            return parseInt(this.pagination.page) === 1;
        },
        get isLastPage() {
            return parseInt(this.pagination.page) === parseInt(this.pagination.last);
        },

        /**
         * Alpine.js 컴포넌트가 초기화될 때 실행되는 부트스트랩 함수
         */
        init() {
            if (!window.adminData) {
                console.error('글로벌 adminData 객체를 찾을 수 없습니다.');
                return;
            }

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

            // 2.2. reset_storage URL 쿼리 파라미터 감지 및 sessionStorage 리셋 처리
            let resetStorageKey = null;
            let needsResetStorageUpdate = false;
            try {
                const urlParams = new URLSearchParams(window.location.search);
                resetStorageKey = urlParams.get('reset_storage');
                if (resetStorageKey) {
                    sessionStorage.removeItem(resetStorageKey);
                    needsResetStorageUpdate = true;
                    urlParams.delete('reset_storage');
                    const newSearch = urlParams.toString();
                    const newUrl = window.location.pathname + (newSearch ? '?' + newSearch : '') + window.location.hash;
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState(null, '', newUrl);
                    }
                }
            } catch (e) {
                console.error(e);
            }

            // 3. columns, filters, editFields 속성 변환 및 전처리
            this.columns = this.prepareColumns();
            this.filters = this.prepareFilters();

            // reset_storage 키와 일치하는 필터의 UI 상태(값) 클리어
            if (resetStorageKey && this.filters) {
                const targetBind = 'sessionStorage:' + resetStorageKey;
                this.filters.forEach(filter => {
                    if (filter.storage_bind === targetBind) {
                        filter.value = null;
                        if ('min_value' in filter) filter.min_value = null;
                        if ('max_value' in filter) filter.max_value = null;
                    }
                });
            }
            this.originalEditFields = window.adminData.edit_fields || [];
            this.editFields = this.prepareEditFields(this.originalEditFields);

            // editFields의 필드명이 컨트롤러 속성에 없는 경우 반응형 멤버로 초기화합니다.
            if (this.editFields) {
                this.editFields.forEach(field => {
                    if (field && field.field_name && !(field.field_name in this)) {
                        this[field.field_name] = field.type === 'belongs_to_many' || field.type === 'has_many' ? [] : '';
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
                    // sessionStorage 연동이 설정된 경우 sessionStorage 갱신
                    if (filter.storage_bind) {
                        const parts = filter.storage_bind.split(':');
                        if (parts.length === 2 && parts[0] === 'sessionStorage') {
                            const key = parts[1];
                            if (val !== null && val !== undefined && val !== '') {
                                // 배열 형태인 경우 JSON 문자열로 직렬화하여 저장
                                if (Array.isArray(val)) {
                                    sessionStorage.setItem(key, JSON.stringify(val));
                                } else {
                                    sessionStorage.setItem(key, val);
                                }
                            } else {
                                sessionStorage.removeItem(key);
                            }
                        }
                    }

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

            // 컴포넌트 활성화 대기 시간 적용
            setTimeout(() => {
                this.initialized = true;

                // sessionStorage에서 로드된 필터 값이나 storage_bind가 설정되어 있고 값이 존재하는 경우,
                // 최초 뷰 렌더링 후 백엔드에 강제 데이터를 요청합니다.
                let hasBoundStorageValue = false;
                this.filters.forEach(f => {
                    if (f.storage_bind) {
                        const parts = f.storage_bind.split(':');
                        if (parts.length === 2 && parts[0] === 'sessionStorage') {
                            const key = parts[1];
                            const val = sessionStorage.getItem(key);
                            if (val !== null && val !== undefined && val !== '') {
                                try {
                                    // JSON 문자열인 경우 파싱하여 빈 배열이 아닌지 검증
                                    const parsed = JSON.parse(val);
                                    if (Array.isArray(parsed) && parsed.length === 0) {
                                        // 빈 배열인 경우 로드 대상으로 판단하지 않음
                                    } else {
                                        hasBoundStorageValue = true;
                                    }
                                } catch (e) {
                                    hasBoundStorageValue = true;
                                }
                            }
                        }
                    }
                });

                if (hasBoundStorageValue || needsResetStorageUpdate) {
                    this.updateRows();
                }

                // 엘리먼트 오프셋이 확실히 확보된 활성화 시점에 리사이즈를 재호출하여 렌더링 오차를 보정합니다.
                this.resizePage();
            }, 1000);

            // 문서 로딩이 완전히 완료된 온로드 시점에 한 번 더 레이아웃을 보정합니다.
            window.addEventListener('load', () => {
                this.resizePage();
            });
        },

        // columns visible 속성 래핑
        prepareColumns() {
            const columns = [];
            const colModel = window.adminData.column_model || [];
            colModel.forEach(column => {
                columns.push({
                    ...column,
                    visible: !!column.visible
                });
            });
            return columns;
        },

        // filters 전처리 및 loading 상태 맵핑
        prepareFilters() {
            const filters = [];
            const rawFilters = window.adminData.filters || {};
            const filterItems = Array.isArray(rawFilters) ? rawFilters : Object.values(rawFilters);
            
            filterItems.forEach(filter => {
                const prepared = { ...filter };
                
                // sessionStorage 연동이 설정된 경우 초기값 로드
                if (filter.storage_bind) {
                    const parts = filter.storage_bind.split(':');
                    if (parts.length === 2 && parts[0] === 'sessionStorage') {
                        const key = parts[1];
                        const val = sessionStorage.getItem(key);
                        if (val !== null && val !== undefined) {
                            try {
                                // JSON 파싱 시도 (배열 형식 복구 목적)
                                prepared.value = JSON.parse(val);
                            } catch (e) {
                                // JSON 형식이 아니면 일반 단일 문자열로 폴백 복구
                                prepared.value = val;
                            }
                        }
                    }
                }
                
                if (prepared.value === undefined) {
                    prepared.value = filter.value !== undefined ? filter.value : null;
                }
                
                if ('min_value' in filter) prepared.min_value = filter.min_value !== undefined ? filter.min_value : null;
                if ('max_value' in filter) prepared.max_value = filter.max_value !== undefined ? filter.max_value : null;
                
                if (filter.relationship) {
                    prepared.loadingOptions = false;
                }
                prepared.field_id = 'filter_field_' + filter.field_name;
                filters.push(prepared);
            });
            return filters;
        },

        // editFields 준비 및 미디어 업로드 상태 정의
        prepareEditFields(editFieldsSrc) {
            const fields = [];
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
        },

        // relationships 옵션 및 autocomplete 데이터 초기화
        initRelationships() {
            this.filters.forEach((filter, ind) => {
                if (filter.relationship) {
                    this.listOptions[ind] = filter.options || [];
                    this.listOptions['filter_' + filter.field_name] = filter.options || [];
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
                        field.options.forEach(option => {
                            this.autocompleteData[autoKey][option.id] = option;
                        });
                    }
                }
            });
        },

        /**
         * 파일 및 이미지 비동기 업로드 처리 (Fetch 및 XHR 연계형)
         */
        async uploadFile(event, field) {
            const file = event.target.files[0];
            if (!file) return;

            // 파일 크기 제한 검증
            if (field.size_limit && file.size > field.size_limit * 1024 * 1024) {
                alert((this.languages['file_too_large'] || '파일 용량이 너무 큽니다. 제한: ') + field.size_limit + 'MB');
                event.target.value = ''; // input 초기화
                return;
            }

            field.uploading = true;
            field.upload_percentage = 0;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', window.csrf || (window.adminData && window.adminData.csrf) || '');

            const url = `${window.base_url}${this.modelName}/${field.field_name}/file_upload`;

            try {
                // XHR을 사용하여 업로드 퍼센트 진행률 추적
                const response = await new Promise((resolve, reject) => {
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
                event.target.value = ''; // input 초기화

                if (response.filename && (!response.errors || Object.keys(response.errors).length === 0)) {
                    // 모델 상태 변수에 파일명 실시간 매핑
                    this[field.field_name] = response.filename;
                } else {
                    alert(response.errors ? JSON.stringify(response.errors) : '업로드 실패');
                }
            } catch (error) {
                field.uploading = false;
                event.target.value = '';
                console.error('[어드민 에러] 파일 업로드 실패:', error);
                alert('파일 업로드 중 네트워크 오류가 발생했습니다.');
            }
        },

        /**
         * 항목 저장 (POST)
         */
        async saveItem() {
            const saveData = {};
            const modelKeys = Object.keys(window.adminData.data_model || {});
            
            modelKeys.forEach(key => {
                saveData[key] = this[key];
            });

            // editFields 내부의 관계 필드를 포함한 모든 입력 대상 필드의 값을 saveData에 추가 수집합니다.
            this.editFields.forEach(field => {
                if (field && field.field_name) {
                    let val = this[field.field_name];
                    
                    // belongs_to_many, has_many 타입 릴레이션이 배열 형태일 경우 쉼표 구분 스트링으로 전처리합니다.
                    if (Array.isArray(val)) {
                        val = val.join(',');
                    }
                    
                    saveData[field.field_name] = val;
                }
            });

            saveData._token = window.csrf || (window.adminData && window.adminData.csrf);

            if (!saveData[this.primaryKey]) {
                delete saveData[this.primaryKey];
            }

            this.editFields.forEach(field => {
                if (field.relationship && !field.external && saveData[field.field_name] === '') {
                    saveData[field.field_name] = false;
                }
            });

            this.statusMessage = this.languages['saving'] || 'Saving...';
            this.statusMessageType = '';
            this.freezeForm = true;

            const url = `${window.base_url}${this.modelName}/${this[this.primaryKey] || 0}/save`;

            try {
                const response = await request(url, {
                    method: 'POST',
                    data: saveData
                });

                this.freezeForm = false;
                this.resizePage();

                if (response.success) {
                    const savedMsg = this.languages['saved'] || 'Item saved.';
                    this.statusMessage = savedMsg;
                    this.statusMessageType = 'success';
                    
                    setTimeout(() => {
                        if (this.statusMessage === savedMsg) {
                            this.statusMessage = '';
                            this.statusMessageType = '';
                        }
                    }, 3000);
                    
                    // 상세 데이터 주입 시의 미세한 Alpine.js 출렁임으로 인한 필터 오작동 리스트 갱신을 차단하는 락을 작동시킵니다.
                    this.freezeUpdateRows = true;
                    
                    this.setData(response.data);
                    await this.updateSelfRelationships();
                    
                    // Alpine.js의 데이터 주입 및 제약조건 실행에 의한 비동기 반응형 $watch 틱 파동이 완전히 잠잠해지도록 락 해제를 50ms간 지연 유예합니다.
                    setTimeout(async () => {
                        this.freezeUpdateRows = false;
                        // 마지막에 단 한 번, 정확히 현재 사용자가 설정해둔 원래의 정렬/필터 조건으로 메인 리스트를 안전하게 갱신합니다.
                        await this.updateRows();
                    }, 50);

                    setTimeout(() => {
                        if (window.History && window.History.pushState) {
                            window.History.pushState({ modelName: this.modelName }, null, window.route + this.modelName);
                        }
                    }, 200);
                } else {
                    this.statusMessage = response.errors || 'Save failed';
                    this.statusMessageType = 'error';
                }
            } catch (error) {
                this.freezeForm = false;
                this.statusMessage = 'A network error occurred.';
                this.statusMessageType = 'error';
                this.resizePage();
            }
        },

        /**
         * 항목 삭제 (POST)
         */
        async deleteItem() {
            const conf = confirm(this.languages['delete_active_item'] || 'Are you sure you want to delete this item?');
            if (!conf) return false;

            this.statusMessage = this.languages['deleting'] || 'Deleting...';
            this.statusMessageType = '';
            this.freezeForm = true;

            const url = `${window.base_url}${this.modelName}/${this[this.primaryKey]}/delete`;

            try {
                const response = await request(url, {
                    method: 'POST',
                    data: { _token: window.csrf || (window.adminData && window.adminData.csrf) }
                });

                this.freezeForm = false;
                this.resizePage();

                if (response.success) {
                    const deletedMsg = this.languages['deleted'] || 'Item deleted.';
                    this.statusMessage = deletedMsg;
                    this.statusMessageType = 'success';
                    
                    setTimeout(() => {
                        if (this.statusMessage === deletedMsg) {
                            this.statusMessage = '';
                            this.statusMessageType = '';
                        }
                    }, 3000);
                    
                    await this.updateRows();
                    await this.updateSelfRelationships();

                    setTimeout(() => {
                        this.clearItem(); // 📌 물리적인 상세 서랍 닫기를 강제로 보장합니다.
                        if (window.History && window.History.pushState) {
                            window.History.pushState({ modelName: this.modelName }, null, window.route + this.modelName);
                        }
                    }, 500);
                } else {
                    this.statusMessage = response.error || 'Delete failed';
                    this.statusMessageType = 'error';
                }
            } catch (error) {
                this.freezeForm = false;
                this.statusMessage = 'A network error occurred.';
                this.statusMessageType = 'error';
                this.resizePage();
            }
        },

        /**
         * 그리드 행 클릭 이벤트 콜백
         */
        clickItem(id) {
            console.log('[디버그] clickItem 호출됨 - id:', id, 'loadingItem:', this.loadingItem, 'activeItem:', this.activeItem, 'viewPermission:', this.actionPermissions.view);
            if (!this.loadingItem && this.activeItem !== id && this.actionPermissions.view) {
                // 히스토리 어댑터 유실/오작동 여부와 전혀 무관하게 즉각 상세 조회를 다이렉트로 가동
                this.getItem(id);

                if (window.History && window.History.pushState) {
                    window.History.pushState({ modelName: this.modelName, id: id }, null, window.route + this.modelName + '/' + id);
                }
            }
        },

        /**
         * 상세 항목 로딩 (GET)
         */
        async getItem(id) {
            this.loadingItem = true;

            window.adminData.edit_fields = this.originalEditFields;
            this.editFields = this.prepareEditFields(this.originalEditFields);

            this.holdConstraintsQueue = true;

            const defaultModel = window.adminData.data_model || {};
            Object.keys(defaultModel).forEach(key => {
                this[key] = defaultModel[key];
            });
            this.originalData = {};

            window.scrollTo({ top: 0, behavior: 'smooth' });

            if (!id) {
                this.setUpNewItem();
                return;
            }

            this.freezeConstraints = true;
            this.itemLoadingId = id;

            const url = `${window.base_url}${this.modelName}/${id}`;

            try {
                const data = await request(url);

                if (data.success === false && data.errors) {
                    alert(data.errors);
                    return;
                }

                if (this.itemLoadingId !== id) {
                    if (this.itemLoadingId === null) {
                        this.loadingItem = false;
                        this.clearItem();
                    }
                } else {
                    this.setData(data);
                }
            } catch (error) {
                this.loadingItem = false;
                console.error('[어드민 에러] 상세 조회 실패:', error);
                alert('데이터를 가져오는 중 오류가 발생했습니다.');
            }
        },

        setUpNewItem() {
            this.itemLoadingId = null;
            this.activeItem = 0;
            this.lastItem = 0;
            this.loadingItem = false;
            
            // 신규 아이템 생성 시 글로벌 기본 권한을 주입하여 Create 버튼 활성화
            this.actionPermissions = (window.adminData && window.adminData.action_permissions) || {};

            this.runConstraintsQueue();
        },

        setData(data) {
            this.activeItem = data[this.primaryKey];
            this.loadingItem = false;

            window.adminData.edit_fields = data.administrator_edit_fields || [];
            this.editFields = this.prepareEditFields(window.adminData.edit_fields);

            // 중요: editFields 속성을 컨트롤러 최상위 멤버로 보장하여 반응성을 활성화합니다.
            if (this.editFields) {
                this.editFields.forEach(field => {
                    if (field && field.field_name && !(field.field_name in this)) {
                        this[field.field_name] = field.type === 'belongs_to_many' || field.type === 'has_many' ? [] : '';
                    }
                });
            }

            this.actions = data.administrator_actions || [];
            this.actionPermissions = data.administrator_action_permissions || {};

            this.originalData = data;

            const fields = window.adminData.edit_fields || [];
            const fieldsArray = Array.isArray(fields) ? fields : Object.values(fields);
            fieldsArray.forEach(el => {
                if (el && el.relationship && el.autocomplete) {
                    const autoKey = el.field_name + '_autocomplete';
                    this.autocompleteData[autoKey] = data[autoKey] || {};
                }
            });

            if (data.admin_item_link) {
                this.itemLink = data.admin_item_link;
            }

            this.lastItem = data[this.primaryKey];

            const defaultModel = window.adminData.data_model || {};
            Object.keys(defaultModel).forEach(key => {
                this[key] = defaultModel[key];
            });

            // 데이터 동기화 루프 수정: defaultModel, editFields 목록, primaryKey 등을 통틀어서 동기화합니다.
            Object.keys(data).forEach(key => {
                const isField = key in defaultModel || key === this.primaryKey || (this.editFields && this.editFields.some(f => f.field_name === key));
                if (isField) {
                    this[key] = data[key];
                }
            });

            // 상세 아이템에 특화된 개별 릴레이션 옵션 리스트를 동적으로 격리 갱신합니다.
            if (this.editFields) {
                this.editFields.forEach((field, ind) => {
                    if (field && field.relationship) {
                        const optKey = field.field_name + '_options';
                        if (data[optKey]) {
                            // 상세 창 전용 격리 공간인 edit_ 키와 기존 하위 호환성용 키를 동시에 업데이트합니다.
                            this.listOptions['edit_' + field.field_name] = data[optKey] || [];
                            this.listOptions[field.field_name] = data[optKey] || [];
                            this.listOptions[ind] = data[optKey] || [];
                        }
                    }
                });
            }
            // datetime 및 time 타입의 초 단위 노출을 억제하고 input[type=datetime-local] 규격에 맞게 포맷팅합니다.
            if (this.editFields) {
                this.editFields.forEach(field => {
                    if (field && field.field_name && this[field.field_name]) {
                        let val = String(this[field.field_name]);
                        if (field.type === 'datetime') {
                            val = val.replace(' ', 'T');
                            if (val.length > 16) {
                                val = val.substring(0, 16);
                            }
                            this[field.field_name] = val;
                        } else if (field.type === 'time') {
                            if (val.length > 5) {
                                val = val.substring(0, 5);
                            }
                            this[field.field_name] = val;
                        }
                    }
                });
            }

            this.freezeConstraints = false;
            this.resizePage();
            this.runConstraintsQueue();
        },

        closeItem() {
            // 히스토리 어댑터 오작동 여부와 무관하게 즉각 상세 서랍 닫기를 보장하기 위해 직접 클리어 호출
            this.clearItem();

            if (window.History && window.History.pushState) {
                window.History.pushState({ modelName: this.modelName }, null, window.route + this.modelName);
            }
        },

        clearItem() {
            this.freezeForm = false;
            this.statusMessage = '';
            this.statusMessageType = '';
            this.itemLink = null;
            this.itemLoadingId = null;
            this.activeItem = null;
            this.lastItem = null;
        },

        addNewItem() {
            this.getItem(0);
        },

        /**
         * 커스텀 액션 처리 (POST)
         */
        async customAction(isItem, action, messages, confirmation, reload) {
            const data = {
                _token: window.csrf || (window.adminData && window.adminData.csrf),
                action_name: action
            };
            let url;

            if (confirmation) {
                if (!confirm(confirmation)) return false;
            }

            if (isItem) {
                url = `${window.base_url}${this.modelName}/${this[this.primaryKey]}/custom_action`;
                this.statusMessage = messages.active || 'Performing action...';
                this.statusMessageType = '';
            } else {
                url = `${window.base_url}${this.modelName}/custom_action`;
                data.sortOptions = this.sortOptions;
                data.filters = this.getFilters();
                data.page = this.pagination.page;
                this.globalStatusMessage = messages.active || 'Performing action...';
                this.globalStatusMessageType = '';
            }

            this.freezeForm = true;

            try {
                const response = await request(url, {
                    method: 'POST',
                    data: data
                });

                this.freezeForm = false;

                if (response.success) {
                    const actionSuccessMsg = messages.success || 'Action completed';
                    if (isItem) {
                        this.statusMessage = actionSuccessMsg;
                        this.statusMessageType = 'success';
                        this.setData(response.data);
                        
                        setTimeout(() => {
                            if (this.statusMessage === actionSuccessMsg) {
                                this.statusMessage = '';
                                this.statusMessageType = '';
                            }
                        }, 3000);
                    } else {
                        this.globalStatusMessage = actionSuccessMsg;
                        this.globalStatusMessageType = 'success';
                        
                        setTimeout(() => {
                            if (this.globalStatusMessage === actionSuccessMsg) {
                                this.globalStatusMessage = '';
                                this.globalStatusMessageType = '';
                            }
                        }, 3000);
                    }

                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }

                    if (response.download) {
                        this.downloadFile(response.download);
                    }

                    await this.updateRows();

                    if (reload) {
                        this.page(this.pagination.page);
                    }
                } else {
                    if (isItem) {
                        this.statusMessage = response.error || '작업 실패';
                        this.statusMessageType = 'error';
                    } else {
                        this.globalStatusMessage = response.error || '작업 실패';
                        this.globalStatusMessageType = 'error';
                    }
                }
            } catch (error) {
                this.freezeForm = false;
                const errText = '오류가 발생했습니다.';
                if (isItem) {
                    this.statusMessage = errText;
                    this.statusMessageType = 'error';
                } else {
                    this.globalStatusMessage = errText;
                    this.globalStatusMessageType = 'error';
                }
            }
        },

        downloadFile(url) {
            const hiddenIFrameId = 'hiddenDownloader';
            let iframe = document.getElementById(hiddenIFrameId);

            if (iframe === null) {
                iframe = document.createElement('iframe');
                iframe.id = hiddenIFrameId;
                iframe.style.display = 'none';
                document.body.appendChild(iframe);
            }

            iframe.src = url;
        },

        /**
         * 그리드 리스트 갱신 (POST)
         */
        async updateRows() {
            if (this.freezeUpdateRows) return;

            const id = ++this.rowLoadingId;
            const data = {
                _token: window.csrf || (window.adminData && window.adminData.csrf),
                sortOptions: this.sortOptions,
                filters: this.getFilters(),
                page: this.pagination.page
            };

            if (!this.initialized) return;

            if (!data.page) {
                data.page = 1;
            }

            this.loadingRows = true;

            const url = `${window.base_url}${this.modelName}/results`;

            try {
                const response = await request(url, {
                    method: 'POST',
                    data: data
                });

                if (this.rowLoadingId !== id) {
                    return;
                }

                this.pagination.page = parseInt(response.last ? response.page : response.last) || 1;
                this.pagination.last = parseInt(response.last) || 1;
                this.pagination.total = parseInt(response.total) || 0;
                this.rows = response.results || [];
                this.loadingRows = false;
            } catch (error) {
                if (this.rowLoadingId === id) {
                    this.loadingRows = false;
                }
            }
        },

        /**
         * 정렬 칼럼 및 방향 업데이트
         */
        setSortOptions(field) {
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
        },

        /**
         * 페이지 네비게이션
         */
        page(page) {
            const currPage = parseInt(this.pagination.page) || 1;
            let newPage = 1;
            const lastPage = parseInt(this.pagination.last) || 1;

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

            this.pagination.page = parseInt(newPage);
            this.updateRows();
        },

        /**
         * 페이지당 노출 행 수 업데이트 (POST)
         */
        async updateRowsPerPage(rows) {
            const url = window.rows_per_page_url;
            try {
                await request(url, {
                    method: 'POST',
                    data: {
                        _token: window.csrf || (window.adminData && window.adminData.csrf),
                        rows: rows
                    }
                });
            } catch (error) {
                console.error(error);
            } finally {
                this.updateRows();
            }
        },

        getFilters() {
            const filters = [];
            const observables = ['value', 'min_value', 'max_value'];

            this.filters.forEach(el => {
                const filter = {
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
        },

        fieldIsDirty(field) {
            return this.originalData[field] !== this[field];
        },

        /**
         * 셀프 릴레이션 관계 정보 갱신 (POST)
         */
        async updateSelfRelationships() {
            const filterPromises = this.filters.map(async (filter, ind) => {
                const fieldName = filter.field_name;

                if ((!filter.constraints || !filter.constraints.length) && filter.self_relationship) {
                    this.filters[ind].loadingOptions = true;

                    const url = `${window.base_url}${this.modelName}/update_options`;
                    try {
                        const response = await request(url, {
                            method: 'POST',
                            data: {
                                fields: [{
                                    type: 'filter',
                                    field: fieldName,
                                    selectedItems: filter.value
                                }]
                            }
                        });

                        this.listOptions['filter_' + fieldName] = response[fieldName];
                    } catch (error) {
                        console.error(error);
                    } finally {
                        this.filters[ind].loadingOptions = false;
                    }
                }
            });

            const fieldPromises = this.editFields.map(async (field, ind) => {
                const fieldName = field.field_name;

                if ((!field.constraints || !field.constraints.length) && field.self_relationship) {
                    this.editFields[ind].loadingOptions = true;

                    const url = `${window.base_url}${this.modelName}/update_options`;
                    try {
                        const response = await request(url, {
                            method: 'POST',
                            data: {
                                fields: [{
                                    type: 'edit',
                                    field: fieldName,
                                    selectedItems: this[fieldName]
                                }]
                            }
                        });

                        this.listOptions['edit_' + fieldName] = response[fieldName];
                        this.listOptions[fieldName] = response[fieldName];
                    } catch (error) {
                        console.error(error);
                    } finally {
                        this.editFields[ind].loadingOptions = false;
                    }
                }
            });

            await Promise.all([...filterPromises, ...fieldPromises]);
        },

        establishFieldConstraints(field) {
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
        },

        getFieldConstraintsLength(key) {
            let length = 0;
            this.editFields.forEach(field => {
                if (field.constraints && field.constraints[key]) {
                    length++;
                }
            });
            return length;
        },

        setConstrainerFreeze(key, freeze) {
            this.editFields.forEach((field, ind) => {
                if (field.field_name === key) {
                    this.editFields[ind].constraintLoading = freeze;
                }
            });
        },

        setFieldLoadingOptions(fieldName, type) {
            this.editFields.forEach((field, ind) => {
                if (field.field_name === fieldName) {
                    this.editFields[ind].loadingOptions = type;
                }
            });
        },

        /**
         * 연관 관계 제약 조건 큐 처리 (POST)
         */
        async runConstraintsQueue() {
            const fields = this.buildConstraintsFromQueue();

            if (!fields.length) return;

            this.freezeActions = true;

            const url = `${window.base_url}${this.modelName}/update_options`;

            try {
                const response = await request(url, {
                    method: 'POST',
                    data: { fields: fields }
                });

                Object.keys(response).forEach(fieldName => {
                    const el = response[fieldName] || [];
                    const data = {};

                    el.forEach(e => {
                        data[e.id] = e;
                    });

                    const autoKey = fieldName + '_autocomplete';
                    this.autocompleteData[autoKey] = data;
                    this.listOptions[fieldName] = el;
                });
            } catch (error) {
                console.error(error);
            } finally {
                this.freezeActions = false;

                Object.keys(this.constraintsQueue).forEach(key => {
                    const fieldConstraints = this.constraintsQueue[key];
                    Object.keys(fieldConstraints).forEach(fieldName => {
                        this.setFieldLoadingOptions(fieldName, false);
                        this.setConstrainerFreeze(key, false);
                    });
                });

                this.constraintsQueue = {};
                this.holdConstraintsQueue = false;
            }
        },

        buildConstraintsFromQueue() {
            const allConstraints = [];

            Object.keys(this.constraintsQueue).forEach(key => {
                const fieldConstraints = this.constraintsQueue[key];
                Object.keys(fieldConstraints).forEach(fieldName => {
                    const field = fieldConstraints[fieldName];
                    const constraints = {};

                    this.setFieldLoadingOptions(fieldName, true);
                    this.setConstrainerFreeze(key, true);

                    Object.keys(field.constraints).forEach(ckey => {
                        constraints[ckey] = this[ckey];
                    });

                    allConstraints.push({
                        constraints: constraints,
                        type: 'edit',
                        field: fieldName,
                        selectedItems: this[fieldName]
                    });
                });
            });

            return allConstraints;
        },

        initEvents() {
            // 바닐라 자바스크립트 기반 동적 이벤트 위임 및 리스너 바인딩
            document.addEventListener('click', (e) => {
                const target = e.target.closest('div.results_header a.new_item');
                if (target) {
                    e.preventDefault();
                    if (window.History && window.History.pushState) {
                        window.History.pushState({ modelName: this.modelName, id: 0 }, null, window.route + this.modelName + '/new');
                    }
                }
            });

            window.addEventListener('resize', this.resizePage.bind(this));
            document.body.addEventListener('mouseup', this.resizePage.bind(this));
            document.body.addEventListener('keypress', this.resizePage.bind(this));

            if (window.History && window.History.Adapter) {
                window.History.Adapter.bind(window, 'statechange', () => {
                    const state = window.History.getState();
                    console.log('[디버그] statechange 발생 - state.data:', state.data);

                    if (state.data.ignore || (state.data.init && !this.historyStarted)) return;

                    if ('modelName' in state.data) {
                        if (state.data.modelName !== this.modelName) {
                            window.location.reload();
                        }
                    }

                    if ('id' in state.data) {
                        if (state.data.id !== this.activeItem) {
                            console.log('[디버그] getItem 비동기 패치 시작 - id:', state.data.id);
                            this.getItem(state.data.id);
                        }
                    } else {
                        console.log('[디버그] id 속성 없음. clearItem() 기동');
                        this.clearItem();
                    }
                });
            }
        },

        initHistory() {
            const historyData = {
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
        },

        resizePage() {
            setTimeout(() => {
                const winHeight = window.innerHeight;
                const itemEdit = document.querySelector('div.item_edit');
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
        },

        scrollDataTable() {
            const tableContainer = document.querySelector('div.table_container');
            const dataTable = tableContainer ? tableContainer.querySelector('table.results') : null;

            if (!dataTable) return;
            if (dataTable.parentElement.classList.contains('table_scrollable')) return;

            const wrapper = document.createElement('div');
            wrapper.classList.add('table_scrollable');
            dataTable.parentNode.insertBefore(wrapper, dataTable);
            wrapper.appendChild(dataTable);
        },

        resizeDataTable() {
            const winWidth = window.innerWidth;
            const tableContainer = document.querySelector('div.table_container');
            const dataTable = tableContainer ? tableContainer.querySelector('table.results') : null;

            if (!dataTable || !tableContainer) return;

            this.columns.forEach((col, i) => {
                const hidePoint = this.columnHidePoints[i];
                if (hidePoint && hidePoint < winWidth) {
                    col.visible = true;
                }
            });

            for (let i = this.columns.length - 1; i >= 2; i--) {
                const containerWidth = tableContainer.offsetWidth;
                const tableWidth = dataTable.offsetWidth;

                if (this.columns.length >= 2 && dataTable.offsetWidth > 0 && containerWidth < tableWidth) {
                    if (i <= 1) return;
                    if (this.columns[i].visible) {
                        this.columns[i].visible = false;
                        this.columnHidePoints[i] = winWidth;
                        break;
                    }
                }
            }
        }
    };
}

window.adminController = adminController;

// Alpine.js의 $root 매직 프로퍼티를 오버라이드하여, 루트 DOM 노드 자체가 아니라 최상위 x-data 반응성 상태 프록시를 리턴하도록 패치합니다.
Alpine.magic('root', (el) => {
    const rootEl = el.closest('[x-data]');
    return rootEl ? Alpine.$data(rootEl) : {};
});

// Alpine.js에 전역 컨트롤러 등록
Alpine.data('adminController', adminController);

// Alpine.js 엔진을 초기화 및 실행합니다.
Alpine.start();

/**
 * Alpine.js 전역 컴포넌트 함수: relationSelect
 * 기존 jQuery 및 Select2 의존성을 완전히 걷어내고, 100% 순수 바닐라 자바스크립트와 Alpine.js 반응성
 * 엔진만을 활용하여 제작된 초경량 프리미엄 Combobox 및 Multi-Select 컴포넌트입니다.
 */
function relationSelect(config) {
    return {
        // config: { field: field, type: 'edit'|'filter', multiple: true|false, autocomplete: true|false, filterIndex: index }
        open: false,
        search: '',
        options: [],
        selectedItems: [], // { id, text } 배지 목록 객체의 배열
        loading: false,
        focusedIndex: -1,

        init() {
            // 1. 초기 모델 값 동기화 및 렌더링 배지 구성
            this.syncValueFromModel();

            // 2. 일반 관계 필드(autocomplete가 아님)의 경우 listOptions 데이터 변경 와칭 동기화
            if (!config.autocomplete) {
                const optKey = config.type === 'filter' ? 'filter_' + config.field.field_name : 'edit_' + config.field.field_name;
                this.options = this.$root.listOptions[optKey] || config.field.options || [];

                this.$watch('$root.listOptions.' + optKey, (newVal) => {
                    this.options = newVal || [];
                    this.syncValueFromModel();
                });
            }

            // 3. 부모 스코프($root) 내 원래 모델의 상태 데이터 변화 와칭 동기화
            // Getter 함수 방식을 사용하여 Alpine.js가 중첩된 경로의 반응성 의존성을 안정적으로 강제 추적하게 함
            this.$watch(() => {
                return config.type === 'filter'
                    ? this.$root.filters[config.filterIndex]?.value
                    : this.$root[config.field.field_name];
            }, (newVal) => {
                this.syncValueFromModel();
            });

            // 4. 드롭다운 개방 시 검색창 자동 포커스 처리
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

            // 5. 검색어 변경 시 포커스 인덱스 초기화
            this.$watch('search', () => {
                this.focusedIndex = -1;
            });
        },

        // 키보드 방향키 이동
        moveFocus(step) {
            const max = this.filteredOptions.length - 1;
            if (max < 0) return;
            
            this.focusedIndex += step;
            if (this.focusedIndex < 0) this.focusedIndex = max;
            if (this.focusedIndex > max) this.focusedIndex = 0;
            
            // 자동 스크롤
            this.$nextTick(() => {
                if (this.$refs.optionsList && this.$refs.optionsList.children[this.focusedIndex]) {
                    const el = this.$refs.optionsList.children[this.focusedIndex];
                    el.scrollIntoView({ block: 'nearest' });
                }
            });
        },

        // 키보드 엔터 선택
        selectFocused() {
            if (this.focusedIndex >= 0 && this.focusedIndex < this.filteredOptions.length) {
                this.selectItem(this.filteredOptions[this.focusedIndex]);
            } else if (this.filteredOptions.length > 0) {
                // 아무것도 포커스 되지 않았을 경우 첫번째 항목 선택
                this.selectItem(this.filteredOptions[0]);
            }
        },

        // 입력값에 맞게 로컬 옵션을 실시간 필터링합니다. (Case-insensitive)
        get filteredOptions() {
            if (config.autocomplete) {
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
        },

        // 부모 모델에 엮여 있는 실제 ID 데이터를 바탕으로 상세 배지 목록(selectedItems)을 정밀 싱크합니다.
        syncValueFromModel() {
            const modelVal = config.type === 'filter'
                ? this.$root.filters[config.filterIndex]?.value
                : this.$root[config.field.field_name];

            let ids = [];
            if (Array.isArray(modelVal)) {
                // 배열 요소 중 빈 값, null, undefined, 0, '0'에 해당하는 원소를 엄격하게 필터링합니다.
                ids = modelVal.map(String).filter(id => id !== '' && id !== 'null' && id !== 'undefined' && id !== '0');
            } else if (modelVal !== undefined && modelVal !== null && modelVal !== '' && modelVal !== 0 && modelVal !== '0') {
                ids = String(modelVal).split(',').map(s => s.trim()).filter(Boolean);
            }

            const autoKey = config.field.field_name + '_autocomplete';
            const autoData = this.$root.autocompleteData[autoKey] || {};

            this.selectedItems = ids.map(id => {
                // 1. 자동완성 맵(autocompleteData)에서 먼저 매핑 복원 시도
                if (autoData[id]) {
                    return { id: id, text: autoData[id].text || autoData[id].name || id };
                }
                // 2. 로컬 옵션 목록(options)에서 찾아서 복원 시도
                const found = this.options.find(opt => String(opt.id) === id);
                if (found) {
                    return { id: id, text: found.text || found.name || id };
                }
                // 3. 최종 예외 폴백: id 그대로 배지에 표출
                return { id: id, text: id };
            });
        },

        // 사용자가 Autocomplete 타이핑 시 백엔드 API로 비동기 자동완성 옵션을 요청합니다.
        async fetchAutocomplete() {
            if (!config.autocomplete) return;
            this.loading = true;

            const data = {
                term: this.search,
                page: 1,
                field: config.field.field_name,
                type: config.type,
                constraints: {},
                selectedItems: config.type === 'filter'
                    ? this.$root.filters[config.filterIndex].value
                    : this.$root[config.field.field_name]
            };

            // 관계 필드의 제약 조건이 묶여 있다면 값 적재
            if (config.field.constraints) {
                Object.keys(config.field.constraints).forEach(key => {
                    data.constraints[key] = this.$root[key];
                });
            }

            const url = `${window.base_url}${this.$root.modelName}/update_options`;

            try {
                const response = await request(url, {
                    method: 'POST',
                    data: { fields: [data] }
                });

                const results = response[config.field.field_name] || [];
                this.options = results;

                // 자동완성 맵(autocompleteData) 전역 최신화
                const autoKey = config.field.field_name + '_autocomplete';
                if (!this.$root.autocompleteData[autoKey]) {
                    this.$root.autocompleteData[autoKey] = {};
                }
                results.forEach(item => {
                    this.$root.autocompleteData[autoKey][item.id] = item;
                });
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        // 드롭다운에서 특정 옵션을 선택 시 실행됩니다.
        selectItem(item) {
            const fieldName = config.field.field_name;
            
            if (config.multiple) {
                let currentVal = config.type === 'filter'
                    ? this.$root.filters[config.filterIndex].value
                    : this.$root[fieldName];

                let ids = [];
                if (Array.isArray(currentVal)) {
                    ids = currentVal.map(String);
                } else if (currentVal !== undefined && currentVal !== null && currentVal !== '') {
                    ids = String(currentVal).split(',').map(s => s.trim()).filter(Boolean);
                }

                const idStr = String(item.id);
                if (!ids.includes(idStr)) {
                    ids.push(idStr);
                }

                // 부모 상태 데이터 갱신
                if (config.type === 'filter') {
                    this.$root.filters[config.filterIndex].value = config.field.type === 'belongs_to_many' ? ids : ids.join(',');
                } else {
                    this.$root[fieldName] = config.field.type === 'belongs_to_many' || config.field.type === 'has_many' ? ids : ids.join(',');
                }
            } else {
                // 단일 선택의 경우 값을 즉시 넣고 드롭다운을 닫습니다.
                if (config.type === 'filter') {
                    this.$root.filters[config.filterIndex].value = item.id;
                } else {
                    this.$root[fieldName] = item.id;
                }
                this.open = false;
            }
            this.search = '';
        },

        // 다중 선택 배지 목록에서 X 마크를 눌러 특정 선택 값을 제거합니다.
        removeItem(item) {
            const fieldName = config.field.field_name;
            
            let currentVal = config.type === 'filter'
                ? this.$root.filters[config.filterIndex].value
                : this.$root[fieldName];

            let ids = [];
            if (Array.isArray(currentVal)) {
                ids = currentVal.map(String);
            } else if (currentVal !== undefined && currentVal !== null && currentVal !== '') {
                ids = String(currentVal).split(',').map(s => s.trim()).filter(Boolean);
            }

            ids = ids.filter(id => String(id) !== String(item.id));

            // 부모 상태 데이터 갱신
            if (config.type === 'filter') {
                this.$root.filters[config.filterIndex].value = config.field.type === 'belongs_to_many' ? ids : ids.join(',');
            } else {
                this.$root[fieldName] = config.field.type === 'belongs_to_many' || config.field.type === 'has_many' ? ids : ids.join(',');
            }
        },

        // 선택값 초기화 기능 (x 마크 클릭 시 호출)
        clearSelection() {
            const fieldName = config.field.field_name;
            if (config.type === 'filter') {
                this.$root.filters[config.filterIndex].value = '';
            } else {
                this.$root[fieldName] = '';
            }
            this.selectedItems = [];
            this.search = '';
            
            // Autocomplete 검색 결과 목록 초기화
            if (config.autocomplete) {
                this.options = [];
            }
        }
    };
}

// 글로벌 윈도우 스코프 및 Alpine 전역 컴포넌트 레지스트리에 이중으로 안전 노출
window.relationSelect = relationSelect;
Alpine.data('relationSelect', relationSelect);

