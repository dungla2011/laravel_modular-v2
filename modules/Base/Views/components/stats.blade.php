<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-{{ $columns ?? '4' }}">
    @foreach($stats as $stat)
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md {{ $stat['iconBg'] ?? 'bg-blue-500' }} text-white">
                            <i class="{{ $stat['icon'] ?? 'fas fa-chart-bar' }} text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                {{ $stat['label'] }}
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $stat['value'] }}
                                </div>
                                @if(isset($stat['change']))
                                    <div class="ml-2 flex items-baseline text-sm font-semibold {{ $stat['changeType'] === 'increase' ? 'text-green-600' : 'text-red-600' }}">
                                        <i class="fas fa-{{ $stat['changeType'] === 'increase' ? 'arrow-up' : 'arrow-down' }} text-xs mr-1"></i>
                                        {{ $stat['change'] }}
                                    </div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
                
                @if(isset($stat['description']))
                    <div class="mt-3">
                        <p class="text-sm text-gray-600">{{ $stat['description'] }}</p>
                    </div>
                @endif
                
                @if(isset($stat['link']))
                    <div class="mt-3">
                        <a href="{{ $stat['link']['url'] }}" 
                           class="text-sm font-medium text-blue-600 hover:text-blue-500 transition duration-150 ease-in-out">
                            {{ $stat['link']['text'] ?? 'View details' }}
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
