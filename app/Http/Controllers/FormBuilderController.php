<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormBuilderController extends Controller
{
    public function index()
    {
        return view('form-builder.index');
    }

    public function renderCanvas(Request $request)
    {
        $fields = $request->input('fields', []);

        return view('form-builder._canvas', compact('fields'))->render();
    }

    public function getFieldOptions(Request $request)
    {
        $field = $request->input('field', []);
        $type = $field['type'] ?? 'text';

        return view('form-builder._field-options', compact('type', 'field'))->render();
    }

    public function export(Request $request)
    {
        $data = $request->json()->all();
        $filename = 'schema_' . time() . '_' . uniqid() . '.json';
        
        \Illuminate\Support\Facades\Storage::disk('local')->put('schemas/' . $filename, json_encode($data, JSON_PRETTY_PRINT));
        
        return response()->json(['success' => true, 'redirect' => route('schemas.index')]);
    }

    public function schemas()
    {
        $files = \Illuminate\Support\Facades\Storage::disk('local')->files('schemas');
        $schemas = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $content = json_decode(\Illuminate\Support\Facades\Storage::disk('local')->get($file), true);
                $schemas[] = [
                    'filename' => basename($file),
                    'title' => $content['title'] ?? 'Untitled Form',
                    'fields_count' => count($content['fields'] ?? []),
                    'date' => date('Y-m-d H:i:s', \Illuminate\Support\Facades\Storage::disk('local')->lastModified($file)),
                    'size' => round(\Illuminate\Support\Facades\Storage::disk('local')->size($file) / 1024, 2) . ' KB'
                ];
            }
        }

        // Sort by newest first
        usort($schemas, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return view('form-builder.schemas', compact('schemas'));
    }

    public function download($filename)
    {
        $path = 'schemas/' . $filename;
        
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($path);
    }

    public function edit($filename)
    {
        $path = 'schemas/' . $filename;
        
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $schemaContent = json_decode(\Illuminate\Support\Facades\Storage::disk('local')->get($path), true);
        return view('form-builder.index', compact('schemaContent'));
    }

    public function destroy($filename)
    {
        $path = 'schemas/' . $filename;
        
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($path);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
