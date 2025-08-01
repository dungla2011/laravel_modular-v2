<form x-data="apiForm({
        endpoint: '{{ $endpoint ?? '' }}',
        method: '{{ $method ?? 'POST' }}',
        redirect: '{{ $redirect ?? '' }}',
        fields: @json($fields ?? []),
        data: @json($data ?? [])
      })" 
      @submit.prevent="submitForm()" 
      class="space-y-6">
    
    <!-- Form Fields -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="grid grid-cols-1 gap-6 {{ $columns ?? 'md:grid-cols-2' }}">
            <template x-for="field in fields" :key="field.name">
                <div :class="field.width || 'col-span-1'">
                    <!-- Field Label -->
                    <label :for="field.name" 
                           class="block text-sm font-medium text-gray-700 mb-1"
                           x-text="field.label">
                    </label>
                    
                    <!-- Text Input -->
                    <template x-if="field.type === 'text' || field.type === 'email' || field.type === 'password' || field.type === 'url'">
                        <input :type="field.type"
                               :name="field.name"
                               :id="field.name"
                               x-model="formData[field.name]"
                               :placeholder="field.placeholder"
                               :required="field.required"
                               :readonly="field.readonly"
                               :disabled="field.disabled"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               :class="errors[field.name] ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                    </template>
                    
                    <!-- Number Input -->
                    <template x-if="field.type === 'number'">
                        <input type="number"
                               :name="field.name"
                               :id="field.name"
                               x-model="formData[field.name]"
                               :placeholder="field.placeholder"
                               :required="field.required"
                               :readonly="field.readonly"
                               :disabled="field.disabled"
                               :min="field.min"
                               :max="field.max"
                               :step="field.step"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               :class="errors[field.name] ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                    </template>
                    
                    <!-- Textarea -->
                    <template x-if="field.type === 'textarea'">
                        <textarea :name="field.name"
                                  :id="field.name"
                                  x-model="formData[field.name]"
                                  :placeholder="field.placeholder"
                                  :required="field.required"
                                  :readonly="field.readonly"
                                  :disabled="field.disabled"
                                  :rows="field.rows || 3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  :class="errors[field.name] ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                        </textarea>
                    </template>
                    
                    <!-- Select -->
                    <template x-if="field.type === 'select'">
                        <select :name="field.name"
                                :id="field.name"
                                x-model="formData[field.name]"
                                :required="field.required"
                                :disabled="field.disabled"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                :class="errors[field.name] ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                            <option value="" x-show="!field.required" x-text="field.placeholder || 'Select an option'"></option>
                            <template x-for="option in field.options" :key="option.value">
                                <option :value="option.value" x-text="option.label"></option>
                            </template>
                        </select>
                    </template>
                    
                    <!-- Checkbox -->
                    <template x-if="field.type === 'checkbox'">
                        <div class="flex items-center mt-1">
                            <input type="checkbox"
                                   :name="field.name"
                                   :id="field.name"
                                   x-model="formData[field.name]"
                                   :disabled="field.disabled"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label :for="field.name" 
                                   class="ml-2 block text-sm text-gray-900"
                                   x-text="field.checkboxLabel || field.label">
                            </label>
                        </div>
                    </template>
                    
                    <!-- Radio Group -->
                    <template x-if="field.type === 'radio'">
                        <div class="mt-1 space-y-2">
                            <template x-for="option in field.options" :key="option.value">
                                <div class="flex items-center">
                                    <input :id="`${field.name}_${option.value}`"
                                           :name="field.name"
                                           type="radio"
                                           :value="option.value"
                                           x-model="formData[field.name]"
                                           :disabled="field.disabled"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <label :for="`${field.name}_${option.value}`" 
                                           class="ml-2 block text-sm text-gray-900"
                                           x-text="option.label">
                                    </label>
                                </div>
                            </template>
                        </div>
                    </template>
                    
                    <!-- File Input -->
                    <template x-if="field.type === 'file'">
                        <div>
                            <input type="file"
                                   :name="field.name"
                                   :id="field.name"
                                   @change="handleFileUpload($event, field.name)"
                                   :accept="field.accept"
                                   :multiple="field.multiple"
                                   :required="field.required"
                                   :disabled="field.disabled"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p x-show="field.help" class="mt-1 text-xs text-gray-500" x-text="field.help"></p>
                        </div>
                    </template>
                    
                    <!-- Date Input -->
                    <template x-if="field.type === 'date' || field.type === 'datetime-local' || field.type === 'time'">
                        <input :type="field.type"
                               :name="field.name"
                               :id="field.name"
                               x-model="formData[field.name]"
                               :required="field.required"
                               :readonly="field.readonly"
                               :disabled="field.disabled"
                               :min="field.min"
                               :max="field.max"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               :class="errors[field.name] ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                    </template>
                    
                    <!-- Rich Text Editor -->
                    <template x-if="field.type === 'editor'">
                        <div>
                            <div :id="`editor_${field.name}`" 
                                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm min-h-[200px]"
                                 :class="errors[field.name] ? 'border-red-300' : ''">
                            </div>
                            <input type="hidden" 
                                   :name="field.name"
                                   x-model="formData[field.name]">
                        </div>
                    </template>
                    
                    <!-- Field Help Text -->
                    <p x-show="field.help && field.type !== 'file'" 
                       class="mt-1 text-sm text-gray-500" 
                       x-text="field.help">
                    </p>
                    
                    <!-- Field Error -->
                    <p x-show="errors[field.name]" 
                       class="mt-1 text-sm text-red-600" 
                       x-text="errors[field.name]">
                    </p>
                </div>
            </template>
        </div>
    </div>
    
    <!-- Form Actions -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <!-- Additional Actions Slot -->
                @yield('form-actions-left')
            </div>
            
            <div class="flex items-center space-x-3">
                <!-- Cancel Button -->
                @if(isset($cancelRoute))
                    <a href="{{ $cancelRoute }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                @endif
                
                <!-- Save as Draft (if applicable) -->
                @if(isset($draftable) && $draftable)
                    <button type="button" 
                            @click="submitForm('draft')"
                            :disabled="submitting"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i x-show="submitting" class="fas fa-spinner fa-spin mr-2"></i>
                        Save as Draft
                    </button>
                @endif
                
                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="submitting"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i x-show="submitting" class="fas fa-spinner fa-spin mr-2"></i>
                    <span x-text="submitting ? 'Saving...' : '{{ $submitLabel ?? ($method === 'PUT' ? 'Update' : 'Create') }}'"></span>
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function apiForm(config) {
    return {
        endpoint: config.endpoint,
        method: config.method,
        redirect: config.redirect,
        fields: config.fields,
        formData: { ...config.data },
        errors: {},
        submitting: false,
        
        init() {
            // Initialize form data with default values
            this.fields.forEach(field => {
                if (this.formData[field.name] === undefined) {
                    this.formData[field.name] = this.getDefaultValue(field);
                }
            });
            
            // Initialize rich text editors
            this.$nextTick(() => {
                this.initializeEditors();
            });
        },
        
        getDefaultValue(field) {
            switch (field.type) {
                case 'checkbox':
                    return field.defaultValue || false;
                case 'number':
                    return field.defaultValue || '';
                case 'select':
                case 'radio':
                    return field.defaultValue || '';
                default:
                    return field.defaultValue || '';
            }
        },
        
        async submitForm(status = null) {
            if (this.submitting) return;
            
            this.submitting = true;
            this.errors = {};
            
            try {
                // Prepare form data
                const submitData = { ...this.formData };
                if (status) {
                    submitData.status = status;
                }
                
                // Update editor content
                this.updateEditorContent();
                
                // Submit form
                let response;
                if (this.method === 'PUT' || this.method === 'PATCH') {
                    response = await adminApi.put(this.endpoint, submitData);
                } else {
                    response = await adminApi.post(this.endpoint, submitData);
                }
                
                // Handle success
                showSuccess(response.message || 'Form submitted successfully');
                
                if (this.redirect) {
                    setTimeout(() => {
                        window.location.href = this.redirect;
                    }, 1000);
                } else {
                    // Reset form if no redirect
                    this.resetForm();
                }
                
            } catch (error) {
                if (error.status === 422 && error.errors) {
                    // Validation errors
                    this.errors = error.errors;
                    showError('Please fix the validation errors below');
                } else {
                    showError(error.message || 'An error occurred while submitting the form');
                }
            } finally {
                this.submitting = false;
            }
        },
        
        resetForm() {
            this.fields.forEach(field => {
                this.formData[field.name] = this.getDefaultValue(field);
            });
            this.errors = {};
        },
        
        handleFileUpload(event, fieldName) {
            const file = event.target.files[0];
            if (file) {
                // You can implement file upload logic here
                // For now, just store the file reference
                this.formData[fieldName] = file;
            }
        },
        
        initializeEditors() {
            this.fields.forEach(field => {
                if (field.type === 'editor') {
                    // Initialize rich text editor (you can use Quill, TinyMCE, etc.)
                    // This is a placeholder for editor initialization
                    const editorContainer = document.getElementById(`editor_${field.name}`);
                    if (editorContainer) {
                        // Example with a simple contenteditable div
                        editorContainer.contentEditable = true;
                        editorContainer.innerHTML = this.formData[field.name] || '';
                        editorContainer.style.minHeight = '200px';
                        editorContainer.style.padding = '12px';
                        editorContainer.style.border = '1px solid #d1d5db';
                        editorContainer.style.borderRadius = '6px';
                        
                        editorContainer.addEventListener('input', () => {
                            this.formData[field.name] = editorContainer.innerHTML;
                        });
                    }
                }
            });
        },
        
        updateEditorContent() {
            this.fields.forEach(field => {
                if (field.type === 'editor') {
                    const editorContainer = document.getElementById(`editor_${field.name}`);
                    if (editorContainer) {
                        this.formData[field.name] = editorContainer.innerHTML;
                    }
                }
            });
        }
    }
}
</script>
@endpush
