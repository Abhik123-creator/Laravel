<div class="space-y-6">
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Entry #{{ $entry->id }} - {{ $contentType->name }}
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Submitted on {{ $entry->created_at->format('M d, Y \a\t g:i A') }}
        </p>
    </div>

    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
        <h4 class="font-medium mb-3 text-gray-900 dark:text-gray-100">Submitted Data:</h4>
        
        @if($entry->data && is_array($entry->data))
            <div class="space-y-3">
                @foreach($entry->data as $key => $value)
                    <div class="grid grid-cols-3 gap-4">
                        <div class="font-medium text-gray-700 dark:text-gray-300">
                            {{ $key }}:
                        </div>
                        <div class="col-span-2 text-gray-900 dark:text-gray-100">
                            @if(is_bool($value))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $value ? 'True' : 'False' }}
                                </span>
                            @elseif(is_array($value))
                                @if(count($value) > 0)
                                    <div class="space-y-1">
                                        @foreach($value as $item)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $item }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-500">No selections</span>
                                @endif
                            @elseif(is_object($value))
                                <pre class="text-xs bg-gray-100 dark:bg-gray-700 p-2 rounded overflow-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                            @else
                                {{ $value }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No data available</p>
        @endif
    </div>

    <div class="text-xs text-gray-500 dark:text-gray-400">
        <p><strong>Created:</strong> {{ $entry->created_at->format('M d, Y \a\t g:i:s A') }}</p>
        <p><strong>Updated:</strong> {{ $entry->updated_at->format('M d, Y \a\t g:i:s A') }}</p>
        @if($entry->created_at != $entry->updated_at)
            <p class="text-amber-600"><em>This entry has been modified</em></p>
        @endif
    </div>
</div>
