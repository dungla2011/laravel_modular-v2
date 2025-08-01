<div class="bg-white shadow-sm rounded-lg overflow-hidden" 
     x-data="dataTable({
        endpoint: '{{ $endpoint ?? '' }}',
        columns: @json($columns ?? []),
        actions: @json($actions ?? []),
        bulkActions: @json($bulkActions ?? []),
        searchable: {{ $searchable ?? 'true' }},
        sortable: {{ $sortable ?? 'true' }},
        perPage: {{ $perPage ?? 15 }}
     })">
    
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <!-- Title and Description -->
            <div class="mb-4 sm:mb-0">
                <h3 class="text-lg font-medium text-gray-900">{{ $title ?? 'Data Table' }}</h3>
                @if(isset($description))
                    <p class="mt-1 text-sm text-gray-600">{{ $description }}</p>
                @endif
            </div>
            
            <!-- Actions -->
            <div class="flex items-center space-x-3">
                <!-- Search -->
                @if($searchable ?? true)
                    <div class="relative">
                        <input type="text" 
                               x-model="search"
                               @input.debounce.300ms="loadData()"
                               placeholder="Search..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                @endif
                
                <!-- Bulk Actions -->
                <div x-show="selectedRows.length > 0" class="flex items-center space-x-2">
                    <span class="text-sm text-gray-700" x-text="`${selectedRows.length} selected`"></span>
                    <template x-for="bulkAction in bulkActions">
                        <button @click="executeBulkAction(bulkAction)" 
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i :class="bulkAction.icon" class="mr-2"></i>
                            <span x-text="bulkAction.label"></span>
                        </button>
                    </template>
                </div>
                
                <!-- Create Button -->
                @if(isset($createRoute))
                    <a href="{{ $createRoute }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        {{ $createLabel ?? 'Create New' }}
                    </a>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <!-- Table Head -->
            <thead class="bg-gray-50">
                <tr>
                    <!-- Select All Checkbox -->
                    @if(!empty($bulkActions))
                        <th scope="col" class="relative px-6 py-3">
                            <input type="checkbox" 
                                   x-model="selectAll"
                                   @change="toggleSelectAll()"
                                   class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                    @endif
                    
                    <!-- Column Headers -->
                    <template x-for="column in columns" :key="column.key">
                        <th scope="col" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            @click="column.sortable !== false ? sort(column.key) : null">
                            <div class="flex items-center space-x-1">
                                <span x-text="column.label"></span>
                                <template x-if="column.sortable !== false">
                                    <div class="flex flex-col">
                                        <i class="fas fa-caret-up text-xs" 
                                           :class="sortField === column.key && sortDirection === 'asc' ? 'text-blue-500' : 'text-gray-300'"></i>
                                        <i class="fas fa-caret-down text-xs -mt-1" 
                                           :class="sortField === column.key && sortDirection === 'desc' ? 'text-blue-500' : 'text-gray-300'"></i>
                                    </div>
                                </template>
                            </div>
                        </th>
                    </template>
                    
                    <!-- Actions Column -->
                    @if(!empty($actions))
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    @endif
                </tr>
            </thead>
            
            <!-- Table Body -->
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Loading State -->
                <template x-if="loading">
                    <tr>
                        <td :colspan="getColumnCount()" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                                <span class="text-gray-500">Loading...</span>
                            </div>
                        </td>
                    </tr>
                </template>
                
                <!-- No Data State -->
                <template x-if="!loading && data.length === 0">
                    <tr>
                        <td :colspan="getColumnCount()" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No data found</h3>
                                <p class="text-gray-500">{{ $emptyMessage ?? 'No records match your criteria.' }}</p>
                            </div>
                        </td>
                    </tr>
                </template>
                
                <!-- Data Rows -->
                <template x-for="(row, index) in data" :key="row.id || index">
                    <tr class="hover:bg-gray-50">
                        <!-- Select Checkbox -->
                        @if(!empty($bulkActions))
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" 
                                       :value="row.id"
                                       x-model="selectedRows"
                                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                        @endif
                        
                        <!-- Data Columns -->
                        <template x-for="column in columns" :key="column.key">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                x-html="formatCellValue(row, column)">
                            </td>
                        </template>
                        
                        <!-- Actions -->
                        @if(!empty($actions))
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <template x-for="action in actions" :key="action.key">
                                        <button @click="executeAction(action, row)"
                                                :class="action.class || 'text-blue-600 hover:text-blue-900'"
                                                :title="action.title || action.label">
                                            <i :class="action.icon"></i>
                                            <span x-show="action.showLabel" x-text="action.label" class="ml-1"></span>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        @endif
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div x-show="pagination && pagination.last_page > 1" 
         class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
            <button @click="goToPage(pagination.current_page - 1)"
                    :disabled="pagination.current_page <= 1"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                Previous
            </button>
            <button @click="goToPage(pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                Next
            </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium" x-text="pagination.from"></span> to 
                    <span class="font-medium" x-text="pagination.to"></span> of 
                    <span class="font-medium" x-text="pagination.total"></span> results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <!-- Previous Button -->
                    <button @click="goToPage(pagination.current_page - 1)"
                            :disabled="pagination.current_page <= 1"
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <!-- Page Numbers -->
                    <template x-for="page in getPageNumbers()" :key="page">
                        <button @click="page !== '...' ? goToPage(page) : null"
                                :class="page === pagination.current_page ? 
                                    'bg-blue-50 border-blue-500 text-blue-600' : 
                                    'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                                :disabled="page === '...'"
                                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                x-text="page">
                        </button>
                    </template>
                    
                    <!-- Next Button -->
                    <button @click="goToPage(pagination.current_page + 1)"
                            :disabled="pagination.current_page >= pagination.last_page"
                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function dataTable(config) {
    return {
        // Configuration
        endpoint: config.endpoint,
        columns: config.columns,
        actions: config.actions || [],
        bulkActions: config.bulkActions || [],
        searchable: config.searchable,
        sortable: config.sortable,
        perPage: config.perPage,
        
        // State
        data: [],
        pagination: null,
        loading: false,
        search: '',
        sortField: '',
        sortDirection: 'asc',
        selectedRows: [],
        selectAll: false,
        
        init() {
            this.loadData();
        },
        
        async loadData(page = 1) {
            if (!this.endpoint) return;
            
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: page,
                    per_page: this.perPage,
                    search: this.search,
                    sort_field: this.sortField,
                    sort_direction: this.sortDirection
                });
                
                const response = await fetch(`${this.endpoint}?${params}`);
                const result = await response.json();
                
                if (response.ok) {
                    this.data = result.data;
                    this.pagination = {
                        current_page: result.current_page,
                        last_page: result.last_page,
                        per_page: result.per_page,
                        total: result.total,
                        from: result.from,
                        to: result.to
                    };
                } else {
                    throw new Error(result.message || 'Failed to load data');
                }
            } catch (error) {
                console.error('Data loading error:', error);
                showError(error.message);
            } finally {
                this.loading = false;
            }
        },
        
        sort(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            this.loadData();
        },
        
        goToPage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.loadData(page);
            }
        },
        
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedRows = this.data.map(row => row.id);
            } else {
                this.selectedRows = [];
            }
        },
        
        async executeAction(action, row) {
            if (action.confirm) {
                if (!await confirmDelete(action.confirm)) return;
            }
            
            if (action.url) {
                if (action.method === 'DELETE') {
                    try {
                        const response = await adminApi.delete(action.url.replace(':id', row.id));
                        showSuccess(action.successMessage || 'Action completed successfully');
                        this.loadData();
                    } catch (error) {
                        showError(error.message);
                    }
                } else {
                    window.location.href = action.url.replace(':id', row.id);
                }
            }
        },
        
        async executeBulkAction(action) {
            if (action.confirm) {
                if (!await confirmDelete(action.confirm)) return;
            }
            
            try {
                const response = await adminApi.post(action.url, {
                    ids: this.selectedRows
                });
                showSuccess(action.successMessage || 'Bulk action completed successfully');
                this.selectedRows = [];
                this.selectAll = false;
                this.loadData();
            } catch (error) {
                showError(error.message);
            }
        },
        
        formatCellValue(row, column) {
            let value = this.getNestedValue(row, column.key);
            
            if (column.format) {
                switch (column.format) {
                    case 'date':
                        return value ? new Date(value).toLocaleDateString() : '-';
                    case 'datetime':
                        return value ? new Date(value).toLocaleString() : '-';
                    case 'currency':
                        return value ? `$${parseFloat(value).toFixed(2)}` : '$0.00';
                    case 'status':
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${this.getStatusClass(value)}">${value}</span>`;
                    case 'image':
                        return value ? `<img src="${value}" alt="" class="h-10 w-10 rounded-full object-cover">` : '<div class="h-10 w-10 bg-gray-200 rounded-full"></div>';
                    case 'link':
                        return value ? `<a href="${value}" target="_blank" class="text-blue-600 hover:text-blue-800">${value}</a>` : '-';
                    default:
                        if (typeof column.format === 'function') {
                            return column.format(value, row);
                        }
                        return value || '-';
                }
            }
            
            return value || '-';
        },
        
        getNestedValue(obj, path) {
            return path.split('.').reduce((current, key) => current?.[key], obj);
        },
        
        getStatusClass(status) {
            const statusClasses = {
                'active': 'bg-green-100 text-green-800',
                'inactive': 'bg-red-100 text-red-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'published': 'bg-green-100 text-green-800',
                'draft': 'bg-gray-100 text-gray-800',
                'archived': 'bg-red-100 text-red-800'
            };
            return statusClasses[status?.toLowerCase()] || 'bg-gray-100 text-gray-800';
        },
        
        getColumnCount() {
            let count = this.columns.length;
            if (this.bulkActions.length > 0) count++;
            if (this.actions.length > 0) count++;
            return count;
        },
        
        getPageNumbers() {
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            const delta = 2;
            const range = [];
            const rangeWithDots = [];
            
            for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
                range.push(i);
            }
            
            if (current - delta > 2) {
                rangeWithDots.push(1, '...');
            } else {
                rangeWithDots.push(1);
            }
            
            rangeWithDots.push(...range);
            
            if (current + delta < last - 1) {
                rangeWithDots.push('...', last);
            } else if (last > 1) {
                rangeWithDots.push(last);
            }
            
            return rangeWithDots;
        }
    }
}
</script>
@endpush
