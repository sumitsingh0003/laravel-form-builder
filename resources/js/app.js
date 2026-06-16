import './bootstrap';
import Sortable from 'sortablejs';

const app = document.getElementById('form-builder-app');

if (app) {
    const fieldTypes = JSON.parse(app.dataset.fieldTypes || '[]');
    const storageKey = app.dataset.storageKey || 'laravel-form-builder-state';
    const fieldsList = document.getElementById('fields-list');
    const dropzone = document.getElementById('canvas-dropzone');
    const emptyState = document.getElementById('empty-state');
    const fieldCount = document.getElementById('field-count');
    const formTitle = document.getElementById('form-title');
    const titleCount = document.getElementById('title-count');
    const previewPanel = document.getElementById('preview-panel');
    const previewForm = document.getElementById('preview-form');
    const previewToggle = document.getElementById('preview-toggle');
    const sideTabs = document.querySelectorAll('.side-tab');
    const addFieldsPanel = document.getElementById('add-fields-panel');
    const fieldOptionsPanel = document.getElementById('field-options-panel');
    const noSelection = document.getElementById('no-selection');
    const optionsForm = document.getElementById('options-form');
    const optionRows = document.getElementById('option-rows');
    const undoButton = document.getElementById('undo-button');
    const redoButton = document.getElementById('redo-button');
    const mainTabs = document.querySelectorAll('.main-tab');
    const editorView = document.getElementById('editor-view');
    const settingsView = document.getElementById('settings-view');
    const settingsFormTitle = document.getElementById('settings-form-title');
    const settingsSubmissionUrl = document.getElementById('settings-submission-url');
    const headerSubmissionUrl = document.getElementById('header-submission-url');
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    const toastActions = document.getElementById('toast-actions');
    const toastCancel = document.getElementById('toast-cancel');
    const toastConfirm = document.getElementById('toast-confirm');

    let pendingDeleteId = null;

    const optionControls = {
        fieldId: document.getElementById('option-field-id'),
        label: document.getElementById('option-label'),
        placeholder: document.getElementById('option-placeholder'),
        minLength: document.getElementById('option-min-length'),
        maxLength: document.getElementById('option-max-length'),
        required: document.getElementById('option-required'),
        cssClass: document.getElementById('option-css-class'),
        defaultValue: document.getElementById('option-default-value'),
    };

    const sections = {
        placeholder: document.querySelector('[data-option-section="placeholder"]'),
        length: document.querySelector('[data-option-section="length"]'),
        options: document.querySelector('[data-option-section="options"]'),
        default: document.querySelector('[data-option-section="default"]'),
    };

    const config = {
        placeholder: ['text', 'number', 'email', 'phone', 'textarea'],
        length: ['text', 'textarea'],
        options: ['dropdown', 'radio', 'checkbox'],
        defaultValue: ['text', 'number', 'email', 'hidden'],
    };

    let state = loadState();
    let history = [snapshot(state)];
    let future = [];
    let draggedPaletteType = null;
    let sortable = null;

    function loadState() {
        if (app.dataset.initialState && app.dataset.initialState !== 'null') {
            try {
                const parsed = JSON.parse(app.dataset.initialState);
                return {
                    title: parsed.title || 'Customer Details Form',
                    submissionUrl: parsed.submissionUrl || '/forms/customer-details/submit',
                    selectedFieldId: null,
                    previewMode: false,
                    fields: validateFields(Array.isArray(parsed.fields) ? parsed.fields : []),
                };
            } catch (error) {
                console.error("Failed to parse initial state:", error);
            }
        }

        const saved = window.localStorage.getItem(storageKey);

        if (saved) {
            try {
                const parsed = JSON.parse(saved);

                return {
                    title: parsed.title || 'Customer Details Form',
                    submissionUrl: parsed.submissionUrl || '/forms/customer-details/submit',
                    selectedFieldId: parsed.selectedFieldId || null,
                    previewMode: Boolean(parsed.previewMode),
                    fields: validateFields(Array.isArray(parsed.fields) ? parsed.fields : []),
                };
            } catch (error) {
                window.localStorage.removeItem(storageKey);
            }
        }

        return {
            title: 'Customer Details Form',
            submissionUrl: '/forms/customer-details/submit',
            selectedFieldId: null,
            previewMode: false,
            fields: [],
        };
    }

    function validateFields(fields) {
        const validTypes = fieldTypes.map(ft => ft.type);
        return fields.filter(f => f && f.type && validTypes.includes(f.type));
    }

    function snapshot(value) {
        return JSON.stringify({
            title: value.title,
            submissionUrl: value.submissionUrl,
            selectedFieldId: value.selectedFieldId,
            previewMode: value.previewMode,
            fields: value.fields,
        });
    }

    function persist() {
        window.localStorage.setItem(storageKey, snapshot(state));
        undoButton.disabled = history.length <= 1;
        redoButton.disabled = future.length === 0;
    }

    function remember() {
        const current = snapshot(state);

        if (history[history.length - 1] !== current) {
            history.push(current);
        }

        future = [];
        persist();
    }

    function restore(serialized) {
        state = JSON.parse(serialized);
        render();
    }

    function fieldTypeLabel(type) {
        return fieldTypes.find((fieldType) => fieldType.type === type)?.label || type;
    }

    function fieldDefaults(type) {
        const label = fieldTypeLabel(type);
        const base = {
            id: `field_${Date.now()}_${Math.random().toString(16).slice(2)}`,
            type,
            label,
            placeholder: '',
            required: false,
            cssClass: '',
            defaultValue: '',
            options: ['Option 1', 'Option 2', 'Option 3'],
            minLength: '',
            maxLength: '',
        };

        if (['text', 'number', 'email', 'phone', 'textarea'].includes(type)) {
            base.placeholder = `Enter ${label.toLowerCase()}`;
        }

        if (type === 'hidden') {
            base.label = 'Hidden Field';
            base.defaultValue = 'hidden_value';
        }

        if (type === 'title') {
            base.label = 'Section Title';
        }

        if (type === 'description') {
            base.label = 'Helpful description text for this section.';
        }

        return base;
    }

    function addField(type, index = state.fields.length) {
        remember();
        const field = fieldDefaults(type);
        state.fields.splice(index, 0, field);
        state.selectedFieldId = field.id;
        switchSidePanel('field-options');
        render();
    }

    function selectField(id) {
        if (state.selectedFieldId === id) {
            state.selectedFieldId = null;
            switchSidePanel('add-fields');
        } else {
            state.selectedFieldId = id;
            switchSidePanel('field-options');
        }
        render();
    }

    function duplicateField(id) {
        const index = state.fields.findIndex((field) => field.id === id);

        if (index === -1) {
            return;
        }

        remember();
        const duplicate = JSON.parse(JSON.stringify(state.fields[index]));
        duplicate.id = `field_${Date.now()}_${Math.random().toString(16).slice(2)}`;
        duplicate.label = `${duplicate.label} Copy`;
        state.fields.splice(index + 1, 0, duplicate);
        state.selectedFieldId = duplicate.id;
        render();
    }

    function requestDeleteField(id) {
        pendingDeleteId = id;
        const field = state.fields.find((f) => f.id === id);
        if (!field) return;
        
        toastMessage.textContent = `Are you sure you want to delete "${field.label || 'this field'}"?`;
        toast.classList.remove('hidden');
        toast.classList.remove('pointer-events-none');
        toastActions.classList.remove('hidden');
        toastActions.classList.add('flex');
    }

    function hideToast() {
        toast.classList.add('hidden');
        toast.classList.add('pointer-events-none');
        toastActions.classList.add('hidden');
        toastActions.classList.remove('flex');
        pendingDeleteId = null;
    }

    toastCancel.addEventListener('click', hideToast);
    
    const toastClose = document.getElementById('toast-close');
    if (toastClose) {
        toastClose.addEventListener('click', hideToast);
    }

    toastConfirm.addEventListener('click', () => {
        if (pendingDeleteId) {
            deleteField(pendingDeleteId);
            hideToast();
        }
    });

    function deleteField(id) {
        const index = state.fields.findIndex((field) => field.id === id);

        if (index === -1) {
            return;
        }

        remember();
        state.fields.splice(index, 1);

        if (state.selectedFieldId === id) {
            state.selectedFieldId = state.fields[index]?.id || state.fields[index - 1]?.id || null;
        }

        render();
    }

    function updateSelectedField(changes, shouldRenderOptions = true) {
        const field = selectedField();

        if (!field) {
            return;
        }

        Object.assign(field, changes);
        if (shouldRenderOptions) {
            render();
            return;
        }

        renderFields();
        renderPreview();
        persist();
    }

    function selectedField() {
        return state.fields.find((field) => field.id === state.selectedFieldId) || null;
    }

    function render() {
        formTitle.value = state.title;
        titleCount.textContent = String(state.title.length);
        if (settingsFormTitle) settingsFormTitle.value = state.title;
        if (settingsSubmissionUrl) settingsSubmissionUrl.value = state.submissionUrl;
        if (headerSubmissionUrl) headerSubmissionUrl.textContent = state.submissionUrl;
        renderFields();
        renderOptionsPanel();
        renderPreview();
        previewPanel.classList.toggle('hidden', !state.previewMode);
        previewToggle.setAttribute('aria-pressed', String(state.previewMode));
        previewToggle.classList.toggle('bg-blue-50', state.previewMode);
        previewToggle.classList.toggle('border-blue-300', state.previewMode);
        previewToggle.classList.toggle('text-blue-700', state.previewMode);
        fieldCount.textContent = `${state.fields.length} ${state.fields.length === 1 ? 'field' : 'fields'}`;
        persist();
    }

    function renderFields() {
        fieldsList.innerHTML = '';
        emptyState.classList.toggle('hidden', state.fields.length > 0);

        state.fields.forEach((field) => {
            const card = document.getElementById('field-card-template').content.firstElementChild.cloneNode(true);
            card.dataset.fieldId = field.id;
            card.classList.toggle('selected', field.id === state.selectedFieldId);
            renderFieldInto(card.querySelector('.field-content'), field, false);
            fieldsList.appendChild(card);
        });
    }

    function renderPreview() {
        previewForm.innerHTML = '';

        state.fields.forEach((field) => {
            const wrapper = document.createElement('div');
            renderFieldInto(wrapper, field, true);
            previewForm.appendChild(wrapper);
        });
    }

    function renderFieldInto(container, field, isPreview) {
        const template = document.querySelector(`[data-render-template="${field.type}"]`);

        if (!template) {
            container.textContent = field.label;
            return;
        }

        container.innerHTML = '';
        const content = template.content.cloneNode(true);
        container.appendChild(content);
        applyFieldConfig(container, field, isPreview);
    }

    function applyFieldConfig(container, field, isPreview) {
        const formField = container.querySelector('.form-field');

        if (formField) {
            formField.classList.remove('mb-4');
            formField.classList.add('mb-0');
        }

        const label = container.querySelector('label');

        if (label && !['title', 'description', 'newline', 'pagebreak', 'hidden'].includes(field.type)) {
            label.innerHTML = '';
            label.append(document.createTextNode(field.label || fieldTypeLabel(field.type)));

            if (field.required) {
                const star = document.createElement('span');
                star.className = 'text-red-500';
                star.textContent = ' *';
                label.append(star);
            }
        }

        const title = container.querySelector('h3');
        const description = container.querySelector('p');

        if (title) {
            title.textContent = field.label || 'Section Title';
        }

        if (field.type === 'description' && description) {
            description.textContent = field.label || 'Description text';
        }

        if (field.type === 'hidden' && !isPreview) {
            container.innerHTML = `<div class="rounded-md border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-500">Hidden Field: ${escapeHtml(field.label || 'Hidden Field')}</div>`;
            return;
        }

        const mainControl = container.querySelector('input:not([type="radio"]):not([type="checkbox"]), textarea, select');

        if (mainControl) {
            mainControl.name = fieldName(field);
            mainControl.placeholder = field.placeholder || '';
            mainControl.required = Boolean(field.required);
            mainControl.className = mergeClasses(mainControl.className, field.cssClass);

            if (['text', 'textarea'].includes(field.type)) {
                setOptionalAttribute(mainControl, 'minlength', field.minLength);
                setOptionalAttribute(mainControl, 'maxlength', field.maxLength);
            }

            if (['number'].includes(field.type)) {
                mainControl.type = 'number';
            }

            if (['text', 'number', 'email', 'hidden'].includes(field.type)) {
                mainControl.value = field.defaultValue || '';
            }
        }

        if (['dropdown', 'radio', 'checkbox'].includes(field.type)) {
            renderChoiceOptions(container, field);
        }

        if (field.type === 'state_city') {
            const inputs = container.querySelectorAll('input');
            inputs.forEach((input, index) => {
                input.className = mergeClasses(input.className, field.cssClass);
                input.required = Boolean(field.required);
                input.name = index === 0 ? `${fieldName(field)}_state` : `${fieldName(field)}_city`;
            });
        }

        if (!isPreview) {
            container.querySelectorAll('input, textarea, select, button').forEach((element) => {
                if (!element.closest('.field-action')) {
                    element.tabIndex = -1;
                }
            });
        }
    }

    function renderChoiceOptions(container, field) {
        const options = cleanOptions(field.options);

        if (field.type === 'dropdown') {
            const select = container.querySelector('select');

            if (!select) {
                return;
            }

            select.innerHTML = '<option value="" class="text-gray-500">Select</option>';
            options.forEach((option) => {
                const optionElement = document.createElement('option');
                optionElement.value = option;
                optionElement.textContent = option;
                optionElement.selected = field.defaultValue === option;
                select.appendChild(optionElement);
            });

            return;
        }

        const holder = container.querySelector('.space-y-2');
        const firstLabel = holder?.querySelector('label');

        if (!holder || !firstLabel) {
            return;
        }

        holder.innerHTML = '';
        options.forEach((option, index) => {
            const row = firstLabel.cloneNode(true);
            const input = row.querySelector('input');
            const text = row.querySelector('span');

            input.name = field.type === 'checkbox' ? `${fieldName(field)}[]` : fieldName(field);
            input.value = option;
            input.id = `${field.id}_${index}`;
            input.required = Boolean(field.required) && index === 0 && field.type === 'radio';
            input.checked = field.defaultValue === option;
            text.textContent = option;
            holder.appendChild(row);
        });
    }

    function renderOptionsPanel() {
        const field = selectedField();
        noSelection.classList.toggle('hidden', Boolean(field));
        optionsForm.classList.toggle('hidden', !field);

        if (!field) {
            return;
        }

        const metaText = document.getElementById('selected-field-meta');
        if (metaText) {
            metaText.textContent = field.label || field.type || 'Field';
        }

        optionControls.fieldId.value = field.id;
        optionControls.label.value = field.label || '';
        optionControls.placeholder.value = field.placeholder || '';
        optionControls.minLength.value = field.minLength || '';
        optionControls.maxLength.value = field.maxLength || '';
        optionControls.required.checked = Boolean(field.required);
        optionControls.cssClass.value = field.cssClass || '';
        optionControls.defaultValue.value = field.defaultValue || '';

        sections.placeholder.classList.toggle('hidden', !config.placeholder.includes(field.type));
        sections.length.classList.toggle('hidden', !config.length.includes(field.type));
        sections.options.classList.toggle('hidden', !config.options.includes(field.type));
        sections.default.classList.toggle('hidden', !config.defaultValue.includes(field.type));

        renderOptionRows(field);
    }

    function renderOptionRows(field) {
        optionRows.innerHTML = '';

        cleanOptions(field.options).forEach((option, index) => {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2';
            row.innerHTML = `
                <input type="text" class="option-input option-row-input" value="${escapeAttribute(option)}" data-option-index="${index}">
                <button type="button" class="remove-option-row rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" data-option-index="${index}">Remove</button>
            `;
            optionRows.appendChild(row);
        });
    }

    function switchSidePanel(panel) {
        sideTabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.panel === panel));
        addFieldsPanel.classList.toggle('hidden', panel !== 'add-fields');
        fieldOptionsPanel.classList.toggle('hidden', panel !== 'field-options');
    }

    function cleanOptions(options) {
        const cleaned = (Array.isArray(options) ? options : [])
            .map((option) => String(option).trim())
            .filter(Boolean);

        return cleaned.length ? cleaned : ['Option 1'];
    }

    function fieldName(field) {
        return `field_${field.id.replace(/[^a-zA-Z0-9_]/g, '_')}`;
    }

    function setOptionalAttribute(element, attribute, value) {
        if (value !== undefined && value !== null && value !== '') {
            element.setAttribute(attribute, value);
        } else {
            element.removeAttribute(attribute);
        }
    }

    function mergeClasses(base, extra) {
        return [base.replace(/\s+/g, ' ').trim(), extra || ''].filter(Boolean).join(' ');
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value;
        return div.innerHTML;
    }

    function escapeAttribute(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function schema() {
        return {
            title: state.title,
            submissionUrl: state.submissionUrl,
            fields: state.fields.map((field, index) => ({
                order: index + 1,
                id: field.id,
                type: field.type,
                label: field.label,
                placeholder: config.placeholder.includes(field.type) ? field.placeholder : undefined,
                minLength: config.length.includes(field.type) ? field.minLength : undefined,
                maxLength: config.length.includes(field.type) ? field.maxLength : undefined,
                options: config.options.includes(field.type) ? cleanOptions(field.options) : undefined,
                required: Boolean(field.required),
                cssClass: field.cssClass || '',
                defaultValue: config.defaultValue.includes(field.type) ? field.defaultValue || '' : undefined,
            })),
        };
    }

    document.querySelectorAll('.field-tile').forEach((tile) => {
        tile.addEventListener('click', () => addField(tile.dataset.fieldType));
    });

    fieldsList.addEventListener('click', (event) => {
        const card = event.target.closest('.field-card');

        if (!card) {
            return;
        }

        const id = card.dataset.fieldId;

        if (event.target.closest('.edit-field')) {
            selectField(id);
        } else if (event.target.closest('.duplicate-field')) {
            duplicateField(id);
        } else if (event.target.closest('.delete-field')) {
            requestDeleteField(id);
        } else {
            selectField(id);
        }
    });

    sideTabs.forEach((tab) => {
        tab.addEventListener('click', () => switchSidePanel(tab.dataset.panel));
    });

    if (mainTabs) {
        mainTabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                mainTabs.forEach(t => {
                    t.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
                    t.classList.add('border-transparent', 'text-gray-500');
                });
                
                tab.classList.remove('border-transparent', 'text-gray-500');
                tab.classList.add('active', 'border-indigo-500', 'text-indigo-600');

                if (tab.dataset.tab === 'editor') {
                    editorView.classList.remove('hidden');
                    editorView.classList.add('grid');
                    settingsView.classList.add('hidden');
                    settingsView.classList.remove('flex');
                } else {
                    editorView.classList.add('hidden');
                    editorView.classList.remove('grid');
                    settingsView.classList.remove('hidden');
                    settingsView.classList.add('flex');
                }
            });
        });
    }

    if (settingsFormTitle) {
        settingsFormTitle.addEventListener('input', () => {
            state.title = settingsFormTitle.value.slice(0, 200);
            render();
        });
    }

    if (settingsSubmissionUrl) {
        settingsSubmissionUrl.addEventListener('input', () => {
            state.submissionUrl = settingsSubmissionUrl.value;
            render();
        });
    }

    formTitle.addEventListener('input', () => {
        state.title = formTitle.value.slice(0, 200);
        render();
    });

    optionControls.label.addEventListener('input', () => updateSelectedField({ label: optionControls.label.value }));
    optionControls.placeholder.addEventListener('input', () => updateSelectedField({ placeholder: optionControls.placeholder.value }));
    optionControls.minLength.addEventListener('input', () => updateSelectedField({ minLength: optionControls.minLength.value }));
    optionControls.maxLength.addEventListener('input', () => updateSelectedField({ maxLength: optionControls.maxLength.value }));
    optionControls.required.addEventListener('change', () => updateSelectedField({ required: optionControls.required.checked }));
    optionControls.cssClass.addEventListener('input', () => updateSelectedField({ cssClass: optionControls.cssClass.value }));
    optionControls.defaultValue.addEventListener('input', () => updateSelectedField({ defaultValue: optionControls.defaultValue.value }));

    document.getElementById('add-option-button').addEventListener('click', () => {
        const field = selectedField();

        if (!field) {
            return;
        }

        const options = cleanOptions(field.options);
        options.push(`Option ${options.length + 1}`);
        updateSelectedField({ options });
    });

    optionRows.addEventListener('input', (event) => {
        if (!event.target.matches('.option-row-input')) {
            return;
        }

        const field = selectedField();

        if (!field) {
            return;
        }

        const options = cleanOptions(field.options);
        options[Number(event.target.dataset.optionIndex)] = event.target.value;
        updateSelectedField({ options }, false);
    });

    optionRows.addEventListener('click', (event) => {
        if (!event.target.matches('.remove-option-row')) {
            return;
        }

        const field = selectedField();

        if (!field) {
            return;
        }

        const options = cleanOptions(field.options);
        options.splice(Number(event.target.dataset.optionIndex), 1);
        updateSelectedField({ options: cleanOptions(options) });
    });

    document.getElementById('remove-field-button').addEventListener('click', () => {
        const field = selectedField();

        if (field) {
            requestDeleteField(field.id);
        }
    });

    document.getElementById('cancel-button').addEventListener('click', () => {
        if (!state.fields.length || window.confirm('Clear the current form builder state?')) {
            remember();
            state = {
                title: 'Customer Details Form',
                submissionUrl: '/forms/customer-details/submit',
                selectedFieldId: null,
                previewMode: false,
                fields: [],
            };
            render();
        }
    });

    document.getElementById('next-button').addEventListener('click', async () => {
        const nextButton = document.getElementById('next-button');
        const originalText = nextButton.textContent;
        nextButton.textContent = 'Saving...';
        nextButton.disabled = true;

        try {
            const response = await fetch('/schemas/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(schema())
            });

            if (response.ok) {
                const data = await response.json();
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } else {
                window.alert('Failed to save schema.');
                nextButton.textContent = originalText;
                nextButton.disabled = false;
            }
        } catch (error) {
            console.error('Error saving schema:', error);
            window.alert('An error occurred while saving the schema.');
            nextButton.textContent = originalText;
            nextButton.disabled = false;
        }
    });

    previewToggle.addEventListener('click', () => {
        state.previewMode = !state.previewMode;
        render();
    });

    const previewJsonButton = document.getElementById('preview-json-button');
    const jsonModal = document.getElementById('json-modal');
    const jsonModalContent = document.getElementById('json-modal-content');
    const closeJsonModal = document.getElementById('close-json-modal');

    if (previewJsonButton && jsonModal) {
        previewJsonButton.addEventListener('click', () => {
            jsonModalContent.textContent = JSON.stringify(schema(), null, 2);
            jsonModal.classList.remove('hidden');
        });

        closeJsonModal.addEventListener('click', () => {
            jsonModal.classList.add('hidden');
        });
    }

    undoButton.addEventListener('click', () => {
        if (history.length <= 1) {
            return;
        }

        future.push(history.pop());
        restore(history[history.length - 1]);
    });

    redoButton.addEventListener('click', () => {
        if (!future.length) {
            return;
        }

        const next = future.pop();
        history.push(next);
        restore(next);
    });

    document.addEventListener('keydown', (event) => {
        const isInput = ['input', 'textarea', 'select'].includes(document.activeElement.tagName.toLowerCase());
        if (isInput) return;

        const key = event.key.toLowerCase();

        if ((event.ctrlKey || event.metaKey) && key === 'z') {
            event.preventDefault();
            undoButton.click();
        }

        if ((event.ctrlKey || event.metaKey) && key === 'y') {
            event.preventDefault();
            redoButton.click();
        }
    });

    Sortable.create(document.querySelector('.grid.grid-cols-2'), {
        group: {
            name: 'shared',
            pull: 'clone',
            put: false
        },
        sort: false,
        animation: 150,
    });

    sortable = Sortable.create(fieldsList, {
        group: 'shared',
        animation: 150,
        ghostClass: 'bg-indigo-50',
        dragClass: 'ring-2',
        onAdd(event) {
            const item = event.item;
            const type = item.dataset.fieldType;
            
            // Remove the cloned DOM node from Sortable since Vue/React/Our State loop will re-render it
            item.parentNode.removeChild(item);

            if (type) {
                addField(type, event.newIndex);
            }
        },
        onEnd(event) {
            if (event.from !== event.to) return;
            if (event.oldIndex === event.newIndex) return;

            remember();
            const [moved] = state.fields.splice(event.oldIndex, 1);
            state.fields.splice(event.newIndex, 0, moved);
            render();
        },
    });

    render();
}
