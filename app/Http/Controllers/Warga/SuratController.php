<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    public function index()
    {
        return view('warga.surat.index', [
            'suratTypes' => SuratType::all(),
            'surats' => SuratRequest::where('user_id', auth()->id())->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'surat_type_id' => 'required|exists:surat_types,id',
            'files.*' => 'required|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $surat = SuratRequest::create([
            'user_id' => auth()->id(),
            'surat_type_id' => $request->surat_type_id,
            'status' => 'submitted'
        ]);

        foreach ($request->file('files') as $key => $file) {
            $path = $file->store('surat_files', 'public');

            SuratFile::create([
                'surat_request_id' => $surat->id,
                'file_path' => $path,
                'file_type' => $key
            ]);
        }

        app(NotificationService::class)
            ->notifySecretary("Surat baru diajukan");

        return redirect()->back()->with('success','Surat berhasil diajukan');
    }
}
