@props(['type', 'field', 'value' => null])

@php
    $commonClass = 'builder-field-control ' . ($field['cssClass'] ?? '');
    $isRequired = ($field['required'] ?? false) ? 'required' : '';
@endphp

<div class="form-field mb-5 group/field">
    @if(in_array($type, ['text', 'textarea', 'number', 'email', 'phone', 'date', 'file', 'dropdown', 'radio', 'checkbox', 'state', 'city', 'state_city']))
        @if($type !== 'hidden')
            <label class="builder-field-label mb-1.5 flex items-center gap-1">
                {{ $field['label'] ?? ucfirst($type) }}
                @if($isRequired) <span class="text-red-500 font-bold">*</span> @endif
            </label>
        @endif
    @endif

    @switch($type)
        @case('text')
        @case('number')
        @case('email')
        @case('phone')
        @case('date')
            <input type="{{ $type === 'phone' ? 'tel' : $type }}" 
                   name="{{ $field['name'] ?? 'field_'.($field['id'] ?? 'template') }}"
                   value="{{ $value ?? ($field['defaultValue'] ?? '') }}"
                   placeholder="{{ $field['placeholder'] ?? '' }}"
                   min="{{ $field['minLength'] ?? '' }}"
                   max="{{ $field['maxLength'] ?? '' }}"
                   class="{{ $commonClass }}"
                   {{ $isRequired }}>
            @break

        @case('textarea')
            <textarea name="{{ $field['name'] ?? 'field_'.($field['id'] ?? 'template') }}"
                      placeholder="{{ $field['placeholder'] ?? '' }}"
                      rows="3"
                      class="{{ $commonClass }}"
                      {{ $isRequired }}>{{ $value ?? ($field['defaultValue'] ?? '') }}</textarea>
            @break

        @case('dropdown')
            <div class="relative">
                <select name="{{ $field['name'] ?? 'field_'.($field['id'] ?? 'template') }}"
                        class="{{ $commonClass }} appearance-none pr-10"
                        {{ $isRequired }}>
                    <option value="" class="text-gray-500">Select</option>
                    @foreach($field['options'] ?? [] as $option)
                        <option value="{{ $option }}" {{ ($value ?? ($field['defaultValue'] ?? '')) == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            @break

        @case('radio')
            <div class="space-y-2 mt-2">
                @foreach($field['options'] ?? [] as $option)
                    <label class="group/radio flex cursor-pointer items-center transition-colors hover:bg-gray-50 p-1.5 -ml-1.5 rounded-md">
                        <div class="relative flex items-center">
                            <input type="radio" 
                                   name="{{ $field['name'] ?? 'field_'.($field['id'] ?? 'template') }}"
                                   value="{{ $option }}"
                                   {{ ($value ?? ($field['defaultValue'] ?? '')) == $option ? 'checked' : '' }}
                                   class="peer h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        </div>
                        <span class="ml-2 text-sm text-gray-700">{{ $option }}</span>
                    </label>
                @endforeach
            </div>
            @break

        @case('checkbox')
            <div class="space-y-2 mt-2">
                @foreach($field['options'] ?? [] as $option)
                    <label class="group/checkbox flex cursor-pointer items-center transition-colors hover:bg-gray-50 p-1.5 -ml-1.5 rounded-md">
                        <div class="relative flex items-center">
                            <input type="checkbox" 
                                   name="{{ $field['name'] ?? 'field_'.($field['id'] ?? 'template') }}[]"
                                   value="{{ $option }}"
                                   class="peer h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        </div>
                        <span class="ml-2 text-sm text-gray-700">{{ $option }}</span>
                    </label>
                @endforeach
            </div>
            @break

        @case('file')
            <div class="mt-1 flex w-full justify-center rounded-lg border-2 border-dashed border-gray-300 bg-white px-6 py-8 transition-colors hover:border-indigo-400 hover:bg-indigo-50/50">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-10 w-10 text-gray-300 group-hover/field:text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 justify-center">
                        <label class="relative cursor-pointer rounded-md font-semibold text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2">
                            <span>Upload a file</span>
                            <input type="file" 
                                   name="{{ $field['name'] ?? 'field_'.($field['id'] ?? 'template') }}"
                                   class="sr-only"
                                   {{ $isRequired }}>
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs leading-5 text-gray-500">PNG, JPG, PDF up to 10MB</p>
                </div>
            </div>
            @break

        @case('title')
            <h3 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-2 mb-2">{{ $field['label'] ?? 'Title' }}</h3>
            @break

        @case('description')
            <p class="text-sm text-gray-600 bg-gray-50 border-l-4 border-indigo-500 p-4 rounded-r-md leading-relaxed">{{ $field['label'] ?? 'Description text' }}</p>
            @break

        @case('newline')
            <hr class="my-8 border-gray-200">
            @break

        @case('pagebreak')
            <div class="page-break my-8 flex items-center justify-center gap-4">
                <div class="h-px flex-1 bg-gray-200"></div>
                <span class="rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-500 shadow-sm">Page Break</span>
                <div class="h-px flex-1 bg-gray-200"></div>
            </div>
            @break

        @case('hidden')
            <input type="hidden" name="{{ $field['name'] ?? 'field_'.($field['id'] ?? 'template') }}" value="{{ $field['defaultValue'] ?? '' }}">
            @break

        @case('state')
            <input type="text" name="state" placeholder="State" class="{{ $commonClass }}">
            @break

        @case('city')
            <input type="text" name="city" placeholder="City" class="{{ $commonClass }}">
            @break

        @case('state_city')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <input type="text" name="state" placeholder="State" class="{{ $commonClass }}">
                <input type="text" name="city" placeholder="City" class="{{ $commonClass }}">
            </div>
            @break
    @endswitch

    @if(in_array($type, ['text', 'textarea']) && isset($field['minLength']) && isset($field['maxLength']))
        <p class="mt-2 text-xs text-gray-500">Min: <span class="font-medium text-gray-700">{{ $field['minLength'] }}</span> | Max: <span class="font-medium text-gray-700">{{ $field['maxLength'] }}</span> chars</p>
    @endif
</div>
