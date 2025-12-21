<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DocxTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminSuratTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = \App\Models\SuratType::latest()->paginate(10);
        return view('admin.surat_type.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.surat_type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_html' => 'nullable|string',
            'template_doc' => 'nullable|file|mimes:docx|max:5120',
            'required_documents' => 'nullable|array',
            'input_fields' => 'nullable|array',
        ]);

        $templateDocPath = null;
        $templateDocOriginalName = null;
        $inputFields = $request->input_fields;

        if ($request->hasFile('template_doc')) {
            $uploaded = $request->file('template_doc');
            $templateDocOriginalName = $uploaded->getClientOriginalName();
            $templateDocPath = $uploaded->store('surat_templates', 'public');

            $keys = app(DocxTemplateService::class)
                ->extractPlaceholdersFromDocx(Storage::disk('public')->path($templateDocPath));

            $inputFields = array_values(array_map(function (string $key) {
                $label = ucwords(str_replace('_', ' ', $key));
                return [
                    'label' => $label,
                    'key' => $key,
                    'type' => 'text',
                    'required' => true,
                ];
            }, $keys));
        } elseif (is_array($inputFields)) {
            $inputFields = array_values(array_map(function ($field) {
                $label = is_array($field) ? ($field['label'] ?? '') : '';
                $key = is_array($field) ? ($field['key'] ?? null) : null;

                return [
                    'label' => $label,
                    'key' => $key ?: Str::slug((string) $label, '_'),
                    'type' => is_array($field) ? ($field['type'] ?? 'text') : 'text',
                    'required' => is_array($field) && isset($field['required']) ? (bool) $field['required'] : false,
                ];
            }, $inputFields));
        }

        \App\Models\SuratType::create([
            'name' => $request->name,
            'description' => $request->description,
            'template_html' => $request->template_html,
            'template_doc_path' => $templateDocPath,
            'template_doc_original_name' => $templateDocOriginalName,
            'required_documents' => $request->required_documents,
            'input_fields' => $inputFields,
        ]);

        return redirect()->route('admin.master.jenis-surat.index')
            ->with('success', 'Jenis Surat berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $type = \App\Models\SuratType::findOrFail($id);
        return view('admin.surat_type.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_html' => 'nullable|string',
            'template_doc' => 'nullable|file|mimes:docx|max:5120',
            'required_documents' => 'nullable|array',
            'input_fields' => 'nullable|array',
        ]);

        $type = \App\Models\SuratType::findOrFail($id);

        $templateDocPath = $type->template_doc_path;
        $templateDocOriginalName = $type->template_doc_original_name;
        $inputFields = $request->input_fields;

        if ($request->hasFile('template_doc')) {
            if ($templateDocPath) {
                Storage::disk('public')->delete($templateDocPath);
            }

            $uploaded = $request->file('template_doc');
            $templateDocOriginalName = $uploaded->getClientOriginalName();
            $templateDocPath = $uploaded->store('surat_templates', 'public');

            $keys = app(DocxTemplateService::class)
                ->extractPlaceholdersFromDocx(Storage::disk('public')->path($templateDocPath));

            $inputFields = array_values(array_map(function (string $key) {
                $label = ucwords(str_replace('_', ' ', $key));
                return [
                    'label' => $label,
                    'key' => $key,
                    'type' => 'text',
                    'required' => true,
                ];
            }, $keys));
        } elseif (is_array($inputFields)) {
            $inputFields = array_values(array_map(function ($field) {
                $label = is_array($field) ? ($field['label'] ?? '') : '';
                $key = is_array($field) ? ($field['key'] ?? null) : null;

                return [
                    'label' => $label,
                    'key' => $key ?: Str::slug((string) $label, '_'),
                    'type' => is_array($field) ? ($field['type'] ?? 'text') : 'text',
                    'required' => is_array($field) && isset($field['required']) ? (bool) $field['required'] : false,
                ];
            }, $inputFields));
        }

        $type->update([
            'name' => $request->name,
            'description' => $request->description,
            'template_html' => $request->template_html,
            'template_doc_path' => $templateDocPath,
            'template_doc_original_name' => $templateDocOriginalName,
            'required_documents' => $request->required_documents,
            'input_fields' => $inputFields,
        ]);

        return redirect()->route('admin.master.jenis-surat.index')
            ->with('success', 'Jenis Surat berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $type = \App\Models\SuratType::findOrFail($id);

        if ($type->template_doc_path) {
            Storage::disk('public')->delete($type->template_doc_path);
        }

        $type->delete();

        return redirect()->route('admin.master.jenis-surat.index')
            ->with('success', 'Jenis Surat berhasil dihapus');
    }
}
