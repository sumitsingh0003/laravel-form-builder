# Laravel Form Builder

A polished drag-and-drop form builder built directly inside the provided Laravel project. Users can add fields from the right-side palette, reorder fields on the canvas, edit field configuration live, duplicate or delete fields, preview the result, and export the current form schema as JSON from the Next button.

## Setup

```bash
composer install
npm install
php artisan key:generate
npm run build
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

If port 8000 is busy, run:

```bash
php artisan serve --port=8001
```

## Tooling

This project is pinned to versions that work with the local Node 14 environment:

- Laravel 10 / PHP 8.1
- Vite 4
- Laravel Vite Plugin 0.8
- Tailwind CSS 3
- SortableJS 1.15

## Drag And Drop Choice

SortableJS is used for canvas reordering because it is lightweight, stable, and handles ordered list drag behavior with fewer edge cases than a custom pointer implementation. Native HTML5 drag-and-drop is used for adding palette items to the canvas because the add flow is simple and keeps the code easy to read.

## Implemented Features

- Form title input with 200 character live counter
- Form Editor and Settings tabs
- Dashed drop canvas with empty state and drag-over feedback
- Right panel with Add Fields and Field Options sub-tabs
- All required field tiles in a two-column palette
- Field cards with move, edit, duplicate, and delete actions
- Live field configuration for labels, placeholders, length limits, options, required state, CSS class, and default value
- JSON schema alert and console log from Next
- Preview mode
- LocalStorage persistence
- Undo and redo with `Ctrl+Z` / `Ctrl+Y`

## Assumptions

- The Settings tab is intentionally non-functional because the assignment only requires the Form Editor tab to work.
- No backend persistence or real form submission is needed.
- The canvas field controls are rendered from the Laravel Blade form-field component templates, then updated on the client as state changes.
- The form submission URL is displayed as a static label for the assignment flow.

## Sample JSON Output

```json
{
  "title": "Customer Details Form",
  "submissionUrl": "/forms/customer-details/submit",
  "fields": [
    {
      "order": 1,
      "id": "field_1781539000000_abcd",
      "type": "text",
      "label": "Full Name",
      "placeholder": "Enter full name",
      "minLength": "2",
      "maxLength": "80",
      "required": true,
      "cssClass": "",
      "defaultValue": ""
    },
    {
      "order": 2,
      "id": "field_1781539000001_efgh",
      "type": "dropdown",
      "label": "Preferred Contact Method",
      "options": ["Email", "Phone", "SMS"],
      "required": false,
      "cssClass": ""
    }
  ]
}
```
