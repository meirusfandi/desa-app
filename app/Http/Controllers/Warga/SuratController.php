<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratRequest;
use App\Models\SuratType;
use App\Models\SuratFile;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    public function index()
    {
        $surats = SuratRequest::with('suratType')
            ->where('user_id', auth()->id())
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

        DB::transaction(function () use ($request) {
            $surat = SuratRequest::create([
                'user_id' => auth()->id(),
                'surat_type_id' => $request->surat_type_id,
                'status' => 'submitted'
            ]);

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
        $surat = SuratRequest::where('user_id', auth()->id())->findOrFail($id);
        
        if ($surat->status !== 'submitted') {
            return redirect()->back()->with('error', 'Surat yang sudah diproses tidak dapat diedit.');
        }

        $suratTypes = SuratType::all();
        return view('warga.surat.edit', compact('surat', 'suratTypes'));
    }

    public function update(Request $request, $id)
    {
        $surat = SuratRequest::where('user_id', auth()->id())->findOrFail($id);

        if ($surat->status !== 'submitted') {
            return redirect()->back()->with('error', 'Surat yang sudah diproses tidak dapat diedit.');
        }

        $request->validate([
            'surat_type_id' => 'required|exists:surat_types,id',
            'files.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $surat->update([
            'surat_type_id' => $request->surat_type_id,
        ]);

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
        $surat = SuratRequest::where('user_id', auth()->id())->findOrFail($id);

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
