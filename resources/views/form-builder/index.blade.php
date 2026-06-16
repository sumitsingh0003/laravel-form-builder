@php
    $fieldTypes = [
        ['type' => 'text', 'label' => 'Text Input', 'icon' => 'TXT'],
        ['type' => 'textarea', 'label' => 'Text Area', 'icon' => 'T/A'],
        ['type' => 'number', 'label' => 'Number Input', 'icon' => '123'],
        ['type' => 'email', 'label' => 'Email Input', 'icon' => '@'],
        ['type' => 'phone', 'label' => 'Phone Input', 'icon' => 'TEL'],
        ['type' => 'dropdown', 'label' => 'Dropdown', 'icon' => 'SEL'],
        ['type' => 'radio', 'label' => 'Radio Buttons', 'icon' => 'RAD'],
        ['type' => 'checkbox', 'label' => 'Checkboxes', 'icon' => 'CHK'],
        ['type' => 'date', 'label' => 'Date Picker', 'icon' => 'CAL'],
        ['type' => 'file', 'label' => 'File Upload', 'icon' => 'UP'],
        ['type' => 'title', 'label' => 'Title', 'icon' => 'H1'],
        ['type' => 'description', 'label' => 'Description', 'icon' => 'TXT'],
        ['type' => 'newline', 'label' => 'New Line', 'icon' => 'BR'],
        ['type' => 'pagebreak', 'label' => 'Page Break', 'icon' => 'PG'],
        ['type' => 'hidden', 'label' => 'Hidden Field', 'icon' => 'HID'],
        ['type' => 'state', 'label' => 'State', 'icon' => 'ST'],
        ['type' => 'city', 'label' => 'City', 'icon' => 'CT'],
        ['type' => 'state_city', 'label' => 'State & City', 'icon' => 'S+C'],
    ];
@endphp

<!doctype html>
<html lang="en" class="bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Builder</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased selection:bg-indigo-100 selection:text-indigo-900 overflow-x-hidden">

    <div
        id="form-builder-app"
        class="relative flex min-h-screen flex-col"
        data-field-types='@json($fieldTypes)'
        data-storage-key="laravel-form-builder-state"
        data-initial-state='@json($schemaContent ?? null)'
    >
        <!-- Header -->
        <header class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
            <div class="mx-auto flex max-w-[1600px] flex-col gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <!-- Title Area -->
                    <div class="min-w-0 flex-1">
                        <div class="mb-2 flex items-center gap-2">
                            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">Form Builder</span>
                            <span class="text-sm text-gray-500">Design your schema visually</span>
                            <a href="{{ route('schemas.index') }}" class="ml-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                &larr; Back to Schemas
                            </a>
                        </div>
                        <input
                            id="form-title"
                            type="text"
                            maxlength="200"
                            value="Customer Details Form"
                            class="block w-full max-w-2xl border-0 border-b border-transparent bg-transparent p-0 text-3xl font-bold text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-0 sm:text-4xl transition-colors"
                        >
                        <div class="mt-2 flex items-center gap-3 text-sm text-gray-500">
                            <span class="flex items-center gap-1.5">
                                Endpoint: <span id="header-submission-url" class="font-mono text-xs font-medium text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded">/forms/customer-details/submit</span>
                            </span>
                            <span class="text-gray-400">&bull;</span>
                            <span><span id="title-count" class="font-medium text-gray-700">21</span>/200</span>
                        </div>
                    </div>

                    <!-- Toolbar -->
                    <div class="flex items-center gap-2">
                        <button type="button" id="undo-button" class="toolbar-button" title="Undo (Ctrl+Z)">
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg> Undo
                        </button>
                        <button type="button" id="redo-button" class="toolbar-button" title="Redo (Ctrl+Y)">
                            Redo <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
                        </button>
                        <button type="button" id="preview-toggle" class="toolbar-button" aria-pressed="false">
                            <svg class="h-4 w-4 mr-1 text-gray-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> Preview
                        </button>
                    </div>
                </div>

                <!-- Main Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button type="button" class="main-tab active border-indigo-500 text-indigo-600 whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium" data-tab="editor">Form Editor</button>
                        <button type="button" class="main-tab border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium" data-tab="settings">Settings</button>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Form Editor View -->
        <main id="editor-view" class="mx-auto grid w-full max-w-[1600px] flex-1 gap-6 p-4 sm:p-6 lg:grid-cols-[minmax(0,1fr)_380px] lg:px-8">
            <!-- Canvas Section -->
            <section class="flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between border-b border-gray-100 bg-white px-6 py-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Drop Canvas</h2>
                        <p class="text-sm text-gray-500">Drag fields from the right palette and drop them below.</p>
                    </div>
                    <span id="field-count" class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">0 fields</span>
                </div>

                <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50">
                    <div id="canvas-dropzone" class="dropzone h-[500px] overflow-y-auto rounded-xl border-2 border-dashed border-gray-300 bg-white p-6 transition-all relative shadow-sm">
                        
                        <div id="empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900">Build your form</h3>
                            <p class="mt-1 max-w-sm text-sm text-gray-500">Drag elements from the right panel to build your form &rarr;</p>
                        </div>

                        <div id="fields-list" class="space-y-4 relative z-10 min-h-[450px] pb-12"></div>
                    </div>

                    <div id="preview-panel" class="mt-6 hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 bg-white px-6 py-4">
                            <h2 class="text-base font-semibold text-gray-900">Live Preview</h2>
                        </div>
                        <form id="preview-form" class="space-y-6 p-6"></form>
                    </div>
                </div>
            </section>

            <!-- Sidebar Section -->
            <aside class="flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 lg:sticky lg:top-[180px] lg:h-[calc(100vh-220px)]">
                <div class="border-b border-gray-100 bg-white p-4">
                    <div class="flex space-x-1 rounded-lg bg-gray-100 p-1">
                        <button type="button" class="side-tab active" data-panel="add-fields">Add Fields</button>
                        <button type="button" class="side-tab" data-panel="field-options">Properties</button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-5 bg-gray-50/30">
                    <!-- Add Fields Palette -->
                    <div id="add-fields-panel">
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Component Palette</h3>
                            <p class="text-xs text-gray-500">Drag a tile or click to append.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($fieldTypes as $fieldType)
                                <button
                                    type="button"
                                    class="field-tile group"
                                    data-field-type="{{ $fieldType['type'] }}"
                                >
                                    <span class="field-tile-icon">{{ $fieldType['icon'] }}</span>
                                    <span class="text-gray-700 group-hover:text-indigo-600">{{ $fieldType['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Field Options -->
                    <div id="field-options-panel" class="hidden">
                        <div id="no-selection" class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-white p-8 text-center">
                            <svg class="mb-3 h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                            <p class="text-sm text-gray-500">Select a field on the canvas to configure its properties.</p>
                        </div>

                        <form id="options-form" class="hidden space-y-5 pb-4">
                            <input type="hidden" id="option-field-id">

                            <div class="rounded-lg bg-indigo-50 px-4 py-3 ring-1 ring-inset ring-indigo-600/10">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Currently Editing</p>
                                <p id="selected-field-meta" class="mt-0.5 font-semibold text-indigo-900">No field selected</p>
                            </div>

                            <div class="space-y-1.5">
                                <label for="option-label" class="option-label">Field Label</label>
                                <input id="option-label" type="text" class="option-input">
                            </div>

                            <div data-option-section="placeholder" class="space-y-1.5">
                                <label for="option-placeholder" class="option-label">Placeholder Text</label>
                                <input id="option-placeholder" type="text" class="option-input">
                            </div>

                            <div data-option-section="length" class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="option-min-length" class="option-label">Min Chars</label>
                                    <input id="option-min-length" type="number" min="0" class="option-input">
                                </div>
                                <div class="space-y-1.5">
                                    <label for="option-max-length" class="option-label">Max Chars</label>
                                    <input id="option-max-length" type="number" min="0" class="option-input">
                                </div>
                            </div>

                            <div data-option-section="options" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="mb-3 flex items-center justify-between">
                                    <label class="option-label !mb-0">Options List</label>
                                    <button type="button" id="add-option-button" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">+ Add Row</button>
                                </div>
                                <div id="option-rows" class="space-y-2"></div>
                            </div>

                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-indigo-300 transition-colors">
                                <input id="option-required" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <span class="text-sm font-medium text-gray-900">Required Field</span>
                            </label>

                            <div class="space-y-1.5">
                                <label for="option-css-class" class="option-label">Custom CSS Class</label>
                                <input id="option-css-class" type="text" class="option-input font-mono text-xs" placeholder="e.g. col-span-2">
                            </div>

                            <div data-option-section="default" class="space-y-1.5">
                                <label for="option-default-value" class="option-label">Default Value</label>
                                <input id="option-default-value" type="text" class="option-input">
                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <button type="button" id="remove-field-button" class="w-full rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-300 hover:bg-red-50">
                                    Delete Component
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </aside>
        </main>

        <!-- Settings View (Hidden by default) -->
        <main id="settings-view" class="hidden mx-auto w-full max-w-[800px] flex-1 p-4 sm:p-6 lg:p-8">
            <div class="rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Form Settings</h2>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label for="settings-form-title" class="block text-sm font-medium leading-6 text-gray-900">Form Title</label>
                        <p class="text-sm text-gray-500">The main heading displayed at the top of your form.</p>
                        <input type="text" id="settings-form-title" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>

                    <div class="space-y-2">
                        <label for="settings-submission-url" class="block text-sm font-medium leading-6 text-gray-900">Submission URL (Endpoint)</label>
                        <p class="text-sm text-gray-500">Where the form data will be sent upon submission.</p>
                        <input type="text" id="settings-submission-url" class="block w-full rounded-md border-0 py-2 font-mono text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:leading-6" placeholder="https://api.example.com/submit">
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="sticky bottom-0 z-40 mt-auto border-t border-gray-200 bg-white">
            <div class="mx-auto flex max-w-[1600px] items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <button type="button" id="cancel-button" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Clear Form</button>
                <div class="flex gap-4">
                    <button type="button" id="preview-json-button" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Preview JSON</button>
                    <button type="button" id="next-button" class="rounded-md bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Export Schema</button>
                </div>
            </div>
        </footer>

        <!-- Toast Notification -->
        <div id="toast" class="pointer-events-none fixed right-4 top-4 z-50 hidden w-[min(400px,calc(100vw-2rem))] rounded-lg bg-white p-4 shadow-lg ring-1 ring-black ring-opacity-5 transition-all duration-300 sm:right-6 sm:top-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="w-0 flex-1 pt-0.5">
                    <p id="toast-message" class="text-sm font-medium text-gray-900"></p>
                    <div id="toast-actions" class="mt-3 hidden items-center gap-3">
                        <button type="button" id="toast-confirm" class="pointer-events-auto rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">Confirm Delete</button>
                        <button type="button" id="toast-cancel" class="pointer-events-auto rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
                    </div>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                    <button type="button" id="toast-close" class="pointer-events-auto inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- JSON Preview Modal -->
        <div id="json-modal" class="fixed inset-0 z-50 hidden bg-gray-500 bg-opacity-75 transition-opacity flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">JSON Schema Preview</h3>
                    <button type="button" id="close-json-modal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto bg-gray-900">
                    <pre><code id="json-modal-content" class="text-sm text-green-400 font-mono"></code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates -->
    <template id="option-row-template">
        <div class="flex items-center gap-2 option-row">
            <svg class="h-4 w-4 cursor-move text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
            <input type="text" class="option-row-input option-input">
            <button type="button" class="remove-option-row flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md text-gray-400 hover:bg-red-50 hover:text-red-500">
                <svg class="pointer-events-none h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>

    <template id="field-card-template">
        <div class="field-card group" draggable="true">
            <div class="absolute inset-y-0 left-0 w-1 bg-indigo-500 rounded-l-xl opacity-0 transition-opacity group-hover:opacity-100"></div>
            <div class="absolute right-4 top-4 flex items-center gap-1 opacity-100 lg:opacity-0 lg:transition-opacity lg:group-hover:opacity-100">
                <button type="button" class="field-action drag-handle cursor-grab active:cursor-grabbing" title="Drag to reorder">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                </button>
                <button type="button" class="field-action edit-field" title="Edit Properties">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <button type="button" class="field-action duplicate-field" title="Duplicate Field">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
                <button type="button" class="field-action danger delete-field" title="Delete Field">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            <div class="pointer-events-none mt-1 field-content">
                <!-- Field HTML injected here -->
            </div>
        </div>
    </template>

    @foreach($fieldTypes as $fieldType)
        <template data-render-template="{{ $fieldType['type'] }}">
            <x-form-field :type="$fieldType['type']" :field="[]" />
        </template>
    @endforeach
</body>
</html>
