/**
 * API Helper for Module Communications
 * Handles CRUD operations for all modules
 */
class ApiHelper {
    constructor(baseUrl = '/api', csrfToken = null) {
        this.baseUrl = baseUrl;
        this.csrfToken = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    /**
     * Default headers for API requests
     */
    getHeaders(additionalHeaders = {}) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...additionalHeaders
        };

        if (this.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        return headers;
    }

    /**
     * Handle API response
     */
    async handleResponse(response) {
        const data = await response.json();
        
        if (!response.ok) {
            throw new ApiError(data.message || 'API Error', response.status, data);
        }
        
        return data;
    }

    /**
     * GET request
     */
    async get(endpoint, params = {}) {
        const url = new URL(`${this.baseUrl}${endpoint}`, window.location.origin);
        Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

        const response = await fetch(url, {
            method: 'GET',
            headers: this.getHeaders(),
        });

        return this.handleResponse(response);
    }

    /**
     * POST request
     */
    async post(endpoint, data = {}) {
        const response = await fetch(`${this.baseUrl}${endpoint}`, {
            method: 'POST',
            headers: this.getHeaders(),
            body: JSON.stringify(data),
        });

        return this.handleResponse(response);
    }

    /**
     * PUT request
     */
    async put(endpoint, data = {}) {
        const response = await fetch(`${this.baseUrl}${endpoint}`, {
            method: 'PUT',
            headers: this.getHeaders(),
            body: JSON.stringify(data),
        });

        return this.handleResponse(response);
    }

    /**
     * DELETE request
     */
    async delete(endpoint) {
        const response = await fetch(`${this.baseUrl}${endpoint}`, {
            method: 'DELETE',
            headers: this.getHeaders(),
        });

        return this.handleResponse(response);
    }

    /**
     * Upload file
     */
    async upload(endpoint, formData) {
        const headers = this.getHeaders();
        delete headers['Content-Type']; // Let browser set content-type for FormData

        const response = await fetch(`${this.baseUrl}${endpoint}`, {
            method: 'POST',
            headers: headers,
            body: formData,
        });

        return this.handleResponse(response);
    }
}

/**
 * Module CRUD Helper
 */
class ModuleCrud {
    constructor(moduleName, apiHelper = null) {
        this.moduleName = moduleName;
        this.api = apiHelper || new ApiHelper();
        this.baseEndpoint = `/${moduleName}`;
    }

    /**
     * Get all records with pagination and filters
     */
    async getAll(params = {}) {
        return this.api.get(this.baseEndpoint, params);
    }

    /**
     * Get single record by ID
     */
    async getById(id) {
        return this.api.get(`${this.baseEndpoint}/${id}`);
    }

    /**
     * Create new record
     */
    async create(data) {
        return this.api.post(this.baseEndpoint, data);
    }

    /**
     * Update existing record
     */
    async update(id, data) {
        return this.api.put(`${this.baseEndpoint}/${id}`, data);
    }

    /**
     * Delete record
     */
    async delete(id) {
        return this.api.delete(`${this.baseEndpoint}/${id}`);
    }

    /**
     * Bulk delete records
     */
    async bulkDelete(ids) {
        return this.api.post(`${this.baseEndpoint}/bulk-delete`, { ids });
    }

    /**
     * Toggle status
     */
    async toggleStatus(id) {
        return this.api.post(`${this.baseEndpoint}/${id}/toggle-status`);
    }

    /**
     * Search records
     */
    async search(query, params = {}) {
        return this.api.get(`${this.baseEndpoint}/search`, { q: query, ...params });
    }
}

/**
 * API Error class
 */
class ApiError extends Error {
    constructor(message, status, data = null) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.data = data;
    }
}

/**
 * UI Helper for common operations
 */
class UiHelper {
    /**
     * Show loading state
     */
    static showLoading(element, text = 'Loading...') {
        if (element) {
            element.disabled = true;
            element.textContent = text;
        }
    }

    /**
     * Hide loading state
     */
    static hideLoading(element, originalText = 'Submit') {
        if (element) {
            element.disabled = false;
            element.textContent = originalText;
        }
    }

    /**
     * Show success message
     */
    static showSuccess(message) {
        // Implementation depends on your notification system
        console.log('Success:', message);
        alert(`✅ ${message}`);
    }

    /**
     * Show error message
     */
    static showError(message, errors = null) {
        console.error('Error:', message, errors);
        alert(`❌ ${message}`);
    }

    /**
     * Confirm action
     */
    static async confirm(message) {
        return window.confirm(message);
    }

    /**
     * Update table row
     */
    static updateTableRow(tableId, rowId, data) {
        const table = document.getElementById(tableId);
        const row = table?.querySelector(`[data-id="${rowId}"]`);
        
        if (row && data) {
            // Update row data attributes or content
            Object.keys(data).forEach(key => {
                const cell = row.querySelector(`[data-field="${key}"]`);
                if (cell) {
                    cell.textContent = data[key];
                }
            });
        }
    }

    /**
     * Remove table row
     */
    static removeTableRow(tableId, rowId) {
        const table = document.getElementById(tableId);
        const row = table?.querySelector(`[data-id="${rowId}"]`);
        row?.remove();
    }
}

// Export for use in modules
window.ApiHelper = ApiHelper;
window.ModuleCrud = ModuleCrud;
window.ApiError = ApiError;
window.UiHelper = UiHelper;
