<div class="bg-white overflow-hidden shadow-sm rounded-lg {{ $class ?? '' }}">
    <!-- Card Header -->
    @if(isset($header) || isset($title) || isset($actions))
        <div class="px-4 py-5 sm:px-6 {{ isset($headerClass) ? $headerClass : 'border-b border-gray-200' }}">
            <div class="flex items-center justify-between">
                <div>
                    @if(isset($title))
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ $title }}
                        </h3>
                    @endif
                    @if(isset($subtitle))
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            {{ $subtitle }}
                        </p>
                    @endif
                    @if(isset($header))
                        {{ $header }}
                    @endif
                </div>
                @if(isset($actions))
                    <div class="flex items-center space-x-2">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Card Body -->
    <div class="{{ isset($bodyClass) ? $bodyClass : 'px-4 py-5 sm:p-6' }}">
        {{ $slot }}
    </div>

    <!-- Card Footer -->
    @if(isset($footer))
        <div class="px-4 py-4 sm:px-6 {{ isset($footerClass) ? $footerClass : 'bg-gray-50 border-t border-gray-200' }}">
            {{ $footer }}
        </div>
    @endif
</div>
