@if(empty($fields))
    <div class="text-center text-gray-400 py-12">
        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 5h18a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        <p class="mt-2">Drag elements from the right panel to build your form →</p>
    </div>
@else
    @foreach($fields as $field)
        <div data-field-id="{{ $field['id'] }}" class="field-card mb-4 border border-gray-200 rounded-lg p-4 bg-white relative group">
            <div class="absolute top-2 right-2 flex gap-2 opacity-0 group-hover:opacity-100 transition">
                <div class="drag-handle cursor-move p-1 text-gray-400 hover:text-gray-600" title="Drag to reorder">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7 2a2 2 0 10-4 0 2 2 0 004 0zm0 6a2 2 0 10-4 0 2 2 0 004 0zm0 6a2 2 0 10-4 0 2 2 0 004 0zm6-12a2 2 0 10-4 0 2 2 0 004 0zm0 6a2 2 0 10-4 0 2 2 0 004 0zm0 6a2 2 0 10-4 0 2 2 0 004 0z"/>
                    </svg>
                </div>
                <button class="edit-field p-1 text-blue-400 hover:text-blue-600" data-id="{{ $field['id'] }}" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <button class="duplicate-field p-1 text-green-400 hover:text-green-600" data-id="{{ $field['id'] }}" title="Duplicate">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
                <button class="delete-field p-1 text-red-400 hover:text-red-600" data-id="{{ $field['id'] }}" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
            <x-form-field :type="$field['type']" :field="$field" />
        </div>
    @endforeach
@endif