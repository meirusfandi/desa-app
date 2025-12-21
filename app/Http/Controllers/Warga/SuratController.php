<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratRequest;
use App\Models\SuratType;
use App\Models\SuratFile;
use App\Services\NotificationService;
use App\Services\DocxTemplateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SuratController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $surats = SuratRequest::with('suratType')
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return view('warga.surat.index', compact('surats'));
    }

    public function create()
    {
        $suratTypes = SuratType::all();
        return view('warga.surat.create', compact('suratTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'surat_type_id' => 'required|exists:surat_types,id',
            'files.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $suratType = SuratType::findOrFail($request->surat_type_id);

        // Validation for Dynamic Fields
        $dynamicRules = [];
        if ($suratType->input_fields) {
            foreach ($suratType->input_fields as $key => $field) {
                if (isset($field['required']) && $field['required']) {
                    $fieldKey = $field['key'] ?? Str::slug($field['label'], '_');
                    $dynamicRules["data.{$fieldKey}"] = 'required';
                }
            }
        }
        $request->validate($dynamicRules);

        DB::transaction(function () use ($request, $suratType) {
            // Prepare Data JSON
            $data = [];
            if ($suratType->input_fields) {
                foreach ($suratType->input_fields as $field) {
                    $fieldKey = $field['key'] ?? Str::slug($field['label'], '_');
                    $data[$fieldKey] = $request->input("data.{$fieldKey}");
                }
            }

            $surat = SuratRequest::create([
                'user_id' => Auth::id(),
                'surat_type_id' => $request->surat_type_id,
                'status' => 'submitted',
                'data' => $data
            ]);

            // Generate filled DOCX if template exists
            $surat->loadMissing(['user', 'suratType']);
            $docxPath = app(DocxTemplateService::class)->generateFilledDocx($surat);
            if ($docxPath) {
                SuratFile::create([
                    'surat_request_id' => $surat->id,
                    'file_path' => $docxPath,
                    'file_type' => 'generated_docx',
                ]);
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    $path = $file->store('surat_files', 'public');
                    SuratFile::create([
                        'surat_request_id' => $surat->id,
                        'file_path' => $path,
                        'file_type' => $key
                    ]);
                }
            }
        });

        // Notification logic (commented out if service not available yet, but kept per existing code)
        // app(NotificationService::class)->notifySecretary("Surat baru diajukan");

        return redirect()->route('warga.surat.index')->with('success', 'Pengajuan surat berhasil dikirim.');
    }

    public function edit($id)
    {
        $surat = SuratRequest::where('user_id', Auth::id())->findOrFail($id);

        if ($surat->status !== 'submitted') {
            return redirect()->back()->with('error', 'Surat yang sudah diproses tidak dapat diedit.');
        }

        $suratTypes = SuratType::all();
        return view('warga.surat.edit', compact('surat', 'suratTypes'));
    }

    public function update(Request $request, $id)
    {
        $surat = SuratRequest::where('user_id', Auth::id())->findOrFail($id);

        if ($surat->status !== 'submitted') {
            return redirect()->back()->with('error', 'Surat yang sudah diproses tidak dapat diedit.');
        }

        $request->validate([
            'surat_type_id' => 'required|exists:surat_types,id',
            'files.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $suratType = SuratType::findOrFail($request->surat_type_id);

        // Validation for Dynamic Fields
        $dynamicRules = [];
        if ($suratType->input_fields) {
            foreach ($suratType->input_fields as $field) {
                if (isset($field['required']) && $field['required']) {
                    $fieldKey = $field['key'] ?? Str::slug($field['label'], '_');
                    $dynamicRules["data.{$fieldKey}"] = 'required';
                }
            }
        }
        $request->validate($dynamicRules);

        // Prepare Data JSON
        $data = [];
        if ($suratType->input_fields) {
            foreach ($suratType->input_fields as $field) {
                $fieldKey = $field['key'] ?? Str::slug($field['label'], '_');
                $data[$fieldKey] = $request->input("data.{$fieldKey}");
            }
        }

        $surat->update([
            'surat_type_id' => $request->surat_type_id,
            'data' => $data
        ]);

        // Re-generate filled DOCX (replace old generated docx if any)
        $surat->loadMissing(['user', 'suratType', 'files']);
        $existingGenerated = $surat->files->firstWhere('file_type', 'generated_docx');
        if ($existingGenerated) {
            Storage::disk('public')->delete($existingGenerated->file_path);
            $existingGenerated->delete();
        }

        $docxPath = app(DocxTemplateService::class)->generateFilledDocx($surat);
        if ($docxPath) {
            SuratFile::create([
                'surat_request_id' => $surat->id,
                'file_path' => $docxPath,
                'file_type' => 'generated_docx',
            ]);
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $key => $file) {
                // Delete old file if exists for this type? Or just append?
                // Simple logic: Add new files. For stricter replace, need logic to delete old SuratFile by type.
                // Assuming append for now or handled by UI keying.
                $path = $file->store('surat_files', 'public');
                SuratFile::create([
                    'surat_request_id' => $surat->id,
                    'file_path' => $path,
                    'file_type' => $key
                ]);
            }
        }

        return redirect()->route('warga.surat.index')->with('success', 'Pengajuan surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $surat = SuratRequest::where('user_id', Auth::id())->findOrFail($id);

        if ($surat->status !== 'submitted') {
            return redirect()->back()->with('error', 'Hanya surat dengan status "submitted" yang dapat dihapus.');
        }

        // Delete files
        foreach ($surat->suratFiles as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $surat->delete();

        return redirect()->route('warga.surat.index')->with('success', 'Pengajuan surat berhasil dihapus.');
    }
}
