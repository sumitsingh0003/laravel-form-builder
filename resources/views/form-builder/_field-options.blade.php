
<form id="field-options-form" class="space-y-4">
    <input type="hidden" name="field_id" value="{{ $field['id'] }}">
    
    <div>
        <label class="block text-sm font-medium text-gray-700">Label</label>
        <input type="text" name="label" value="{{ $field['label'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
    </div>
    
    @if(in_array($type, ['text', 'textarea', 'number', 'email', 'phone', 'date', 'file']))
    <div>
        <label class="block text-sm font-medium text-gray-700">Placeholder</label>
        <input type="text" name="placeholder" value="{{ $field['placeholder'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
    </div>
    @endif
    
    @if(in_array($type, ['text', 'textarea']))
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Min Length</label>
            <input type="number" name="minLength" value="{{ $field['minLength'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Max Length</label>
            <input type="number" name="maxLength" value="{{ $field['maxLength'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
    </div>
    @endif
    
    @if(in_array($type, ['dropdown', 'radio', 'checkbox']))
    <div>
        <label class="block text-sm font-medium text-gray-700">Options (one per line)</label>
        <textarea name="options" id="options-textarea" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm font-mono text-sm">{{ isset($field['options']) ? implode("\n", $field['options']) : '' }}</textarea>
        <button type="button" id="add-option-row" class="mt-2 text-sm text-indigo-600 hover:text-indigo-700">+ Add Option</button>
    </div>
    @endif
    
    <div>
        <label class="inline-flex items-center">
            <input type="checkbox" name="required" {{ ($field['required'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
            <span class="ml-2 text-sm text-gray-700">Required field</span>
        </label>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700">CSS Class</label>
        <input type="text" name="cssClass" value="{{ $field['cssClass'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    </div>
    
    @if(!in_array($type, ['title', 'description', 'newline', 'pagebreak']))
    <div>
        <label class="block text-sm font-medium text-gray-700">Default Value</label>
        <input type="text" name="defaultValue" value="{{ $field['defaultValue'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    </div>
    @endif
    
    <div class="pt-4">
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
            Apply Changes
        </button>
    </div>
</form>