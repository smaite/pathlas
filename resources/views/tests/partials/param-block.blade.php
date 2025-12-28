<div class="param-block flex items-center gap-3 bg-white border border-gray-200 rounded-lg px-4 py-3 hover:shadow-sm transition-shadow" 
     data-id="{{ $param->id }}">
    <!-- Drag Handle -->
    <div class="drag-handle cursor-grab text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
        </svg>
    </div>
    
    <!-- Parameter Info -->
    <div class="flex-1 grid grid-cols-5 gap-4 items-center">
        <div>
            <span class="font-medium text-gray-800">{{ $param->name }}</span>
            @if($param->code)
            <span class="text-xs text-gray-400 ml-1">({{ $param->code }})</span>
            @endif
            @if($param->is_calculated)
            <span class="text-xs text-green-600 ml-1" title="Formula: {{ $param->formula }}">⚡ Auto</span>
            @endif
        </div>
        <div class="text-sm text-gray-600">{{ $param->unit ?: '-' }}</div>
        <div class="text-sm text-gray-600">
            <span class="text-blue-600">M:</span> {{ $param->getNormalRange('male') }}
        </div>
        <div class="text-sm text-gray-600">
            <span class="text-pink-600">F:</span> {{ $param->getNormalRange('female') }}
        </div>
        <div class="flex items-center justify-end gap-2">
            @if(!$param->is_active)
            <span class="px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded">Inactive</span>
            @endif
            <button onclick="editParam({{ json_encode($param) }})" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </button>
            <form action="{{ route('tests.parameters.destroy', [$test, $param]) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button onclick="return confirm('Delete this parameter?')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>
