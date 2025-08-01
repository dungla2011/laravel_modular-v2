<div x-show="open" 
     x-data="{ open: {{ $open ?? 'false' }} }"
     @open-modal.window="if ($event.detail.name === '{{ $name ?? 'default' }}') open = true"
     @close-modal.window="if ($event.detail.name === '{{ $name ?? 'default' }}') open = false"
     @keydown.escape.window="open = false"
     class="relative z-50" 
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
         @click="open = false">
    </div>

    <!-- Modal Container -->
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal Panel -->
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop
                 class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full {{ $maxWidth ?? 'sm:max-w-lg' }} sm:p-6">
                
                <!-- Modal Header -->
                @if(isset($title) || isset($closable))
                    <div class="flex items-center justify-between {{ isset($title) ? 'mb-4' : 'mb-2' }}">
                        @if(isset($title))
                            <h3 class="text-lg font-semibold leading-6 text-gray-900">
                                {{ $title }}
                            </h3>
                        @endif
                        
                        @if($closable ?? true)
                            <button @click="open = false" 
                                    type="button" 
                                    class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <span class="sr-only">Close</span>
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        @endif
                    </div>
                @endif

                <!-- Modal Body -->
                <div class="{{ isset($bodyClass) ? $bodyClass : '' }}">
                    {{ $slot }}
                </div>

                <!-- Modal Footer -->
                @if(isset($footer))
                    <div class="mt-5 sm:mt-6 {{ isset($footerClass) ? $footerClass : 'sm:flex sm:flex-row-reverse' }}">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(isset($triggerButton))
    <!-- Trigger Button -->
    <button @click="$dispatch('open-modal', { name: '{{ $name ?? 'default' }}' })"
            type="button"
            class="{{ $triggerClass ?? 'inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500' }}">
        @if(isset($triggerIcon))
            <i class="{{ $triggerIcon }} mr-2"></i>
        @endif
        {{ $triggerButton }}
    </button>
@endif

@push('scripts')
<script>
// Global modal helper functions
window.openModal = (name) => {
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name } }));
};

window.closeModal = (name) => {
    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name } }));
};

// Confirmation modal helper
window.confirmModal = (title, message, confirmText = 'Confirm', cancelText = 'Cancel') => {
    return new Promise((resolve) => {
        // Create dynamic confirmation modal
        const modalHtml = `
            <div x-data="{ open: true }" 
                 x-show="open" 
                 @keydown.escape.window="open = false; resolve(false)"
                 class="relative z-50 confirmation-modal">
                <div x-show="open" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
                </div>

                <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div x-show="open" 
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                            
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900">${title}</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">${message}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button @click="open = false; removeModal(); resolve(true)" 
                                        type="button" 
                                        class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                                    ${confirmText}
                                </button>
                                <button @click="open = false; removeModal(); resolve(false)" 
                                        type="button" 
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                    ${cancelText}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const modalElement = document.createElement('div');
        modalElement.innerHTML = modalHtml;
        modalElement.classList.add('confirmation-modal-container');
        
        // Add resolve function to the element for access in Alpine
        modalElement.resolve = resolve;
        modalElement.removeModal = () => {
            setTimeout(() => {
                document.body.removeChild(modalElement);
            }, 200);
        };
        
        document.body.appendChild(modalElement);
    });
};
</script>
@endpush
