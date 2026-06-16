<!doctype html>
<html lang="en" class="bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Saved Form Schemas</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased selection:bg-indigo-100 selection:text-indigo-900">

    <div class="relative flex min-h-screen flex-col">
        <header class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
            <div class="mx-auto flex max-w-[1200px] items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">Form Builder</span>
                    <h1 class="text-xl font-bold text-gray-900">Projects</h1>
                </div>
                <a href="{{ route('home') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    + New Form
                </a>
            </div>
        </header>

        <main class="mx-auto w-full max-w-[1200px] flex-1 p-4 sm:p-6 lg:p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Your Saved Schemas</h2>
                <p class="mt-1 text-sm text-gray-500">A list of all form schemas you have exported. Download them directly as JSON.</p>
            </div>

            @if(count($schemas) > 0)
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <ul role="list" class="divide-y divide-gray-100">
                        @foreach($schemas as $schema)
                            <li class="flex items-center justify-between gap-x-6 px-6 py-5 hover:bg-gray-50 transition-colors">
                                <div class="min-w-0">
                                    <div class="flex items-start gap-x-3">
                                        <p class="text-sm font-semibold leading-6 text-gray-900">{{ $schema['title'] }}</p>
                                        <p class="rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset text-indigo-700 bg-indigo-50 ring-indigo-600/20">{{ $schema['fields_count'] }} Fields</p>
                                    </div>
                                    <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                                        <p class="whitespace-nowrap font-mono text-[10px]">{{ $schema['filename'] }}</p>
                                        <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current"><circle cx="1" cy="1" r="1" /></svg>
                                        <p class="truncate">{{ $schema['date'] }}</p>
                                        <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current"><circle cx="1" cy="1" r="1" /></svg>
                                        <p>{{ $schema['size'] }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-none items-center gap-x-2">
                                    <button type="button" onclick="previewSchema('{{ $schema['filename'] }}')" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        Preview
                                    </button>
                                    <a href="{{ route('schemas.edit', $schema['filename']) }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        Edit
                                    </a>
                                    <a href="{{ route('schemas.download', $schema['filename']) }}" class="hidden sm:block rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        Download
                                    </a>
                                    <button type="button" onclick="confirmDelete('{{ $schema['filename'] }}')" class="rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 shadow-sm ring-1 ring-inset ring-red-600/20 hover:bg-red-100">
                                        Delete
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-center rounded-xl border-2 border-dashed border-gray-300 bg-white p-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No projects found</h3>
                    <p class="mt-1 text-sm text-gray-500">You haven't exported any form schemas yet.</p>
                    <div class="mt-6">
                        <a href="{{ route('home') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Create a Form
                        </a>
                    </div>
                </div>
            @endif
        </main>

        <!-- JSON Preview Modal -->
        <div id="json-modal" class="fixed inset-0 z-50 hidden bg-gray-500 bg-opacity-75 transition-opacity flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">JSON Schema Preview</h3>
                    <button type="button" onclick="document.getElementById('json-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto bg-gray-900">
                    <pre><code id="json-modal-content" class="text-sm text-green-400 font-mono"></code></pre>
                </div>
            </div>
        </div>

    </div>

    <!-- Delete Confirmation Toast -->
    <div id="delete-toast" class="pointer-events-none fixed right-4 top-4 z-50 hidden w-[min(400px,calc(100vw-2rem))] rounded-lg bg-white p-4 shadow-lg ring-1 ring-black ring-opacity-5 sm:right-6 sm:top-6">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="w-0 flex-1 pt-0.5 pointer-events-auto">
                <p class="text-sm font-medium text-gray-900">Delete this schema?</p>
                <div class="mt-3 flex items-center gap-3">
                    <button type="button" id="confirm-delete-btn" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">Confirm</button>
                    <button type="button" onclick="closeDeleteToast()" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let schemaToDelete = null;

        function confirmDelete(filename) {
            schemaToDelete = filename;
            document.getElementById('delete-toast').classList.remove('hidden');
        }

        function closeDeleteToast() {
            document.getElementById('delete-toast').classList.add('hidden');
            schemaToDelete = null;
        }

        document.getElementById('confirm-delete-btn').addEventListener('click', () => {
            if (!schemaToDelete) return;
            const filename = schemaToDelete;
            closeDeleteToast(); // Hide instantly

            fetch('/schemas/' + filename, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to delete schema');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred.');
            });
        });

        function previewSchema(filename) {
            fetch('/schemas/' + filename + '/download')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('json-modal-content').textContent = JSON.stringify(data, null, 2);
                    document.getElementById('json-modal').classList.remove('hidden');
                })
                .catch(err => alert('Error loading schema preview'));
        }
    </script>
</body>
</html>
