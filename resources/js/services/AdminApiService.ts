export class AdminApiService {
    private baseUrl: string;
    private csrfToken: string;

    constructor(baseUrl: string, csrfToken: string) {
        this.baseUrl = baseUrl;
        this.csrfToken = csrfToken;
    }

    /**
     * 중첩된 객체를 x-www-form-urlencoded 쿼리 스트링 포맷으로 직렬화하기 위한 헬퍼 메소드
     */
    private buildParams(prefix: string, obj: any, add: (key: string, value: any) => void): void {
        if (Array.isArray(obj)) {
            obj.forEach((v, i) => {
                if (/\[\]$/.test(prefix)) {
                    add(prefix, v);
                } else {
                    this.buildParams(
                        prefix + "[" + (typeof v === "object" && v !== null ? i : "") + "]",
                        v,
                        add
                    );
                }
            });
        } else if (typeof obj === "object" && obj !== null) {
            for (const name in obj) {
                if (Object.prototype.hasOwnProperty.call(obj, name)) {
                    this.buildParams(prefix + "[" + name + "]", obj[name], add);
                }
            }
        } else {
            add(prefix, obj);
        }
    }

    private serializeData(obj: any): string {
        const s: string[] = [];
        const add = (key: string, value: any) => {
            const val = value == null ? "" : value;
            s.push(encodeURIComponent(key) + "=" + encodeURIComponent(val));
        };

        for (const prefix in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, prefix)) {
                this.buildParams(prefix, obj[prefix], add);
            }
        }
        return s.join("&");
    }

    /**
     * 비동기 HTTP 요청 처리 메소드
     */
    public async request<T>(url: string, options: RequestInit & { data?: any } = {}): Promise<T> {
        const method = options.method || 'GET';
        const headers: Record<string, string> = {
            'X-CSRF-TOKEN': this.csrfToken || (window as any).csrf || ((window as any).adminData && (window as any).adminData.csrf) || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            ...(options.headers as Record<string, string> || {})
        };

        const fetchOptions: RequestInit = {
            method,
            headers
        };

        let targetUrl = url;

        if (options.data) {
            if (method === 'POST') {
                headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
                fetchOptions.body = this.serializeData(options.data);
            } else {
                const queryString = this.serializeData(options.data);
                if (queryString) {
                    targetUrl += (targetUrl.includes('?') ? '&' : '?') + queryString;
                }
            }
        }

        const response = await fetch(targetUrl, fetchOptions);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json() as T;
    }
}
