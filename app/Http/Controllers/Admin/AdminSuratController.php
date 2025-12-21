<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratRequest;
use App\Services\NotificationService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSuratController extends Controller
{
    private function baseQuery($status)
    {
        $query = SuratRequest::with(['user', 'suratType', 'files'])
            ->where('status', $status);

        if (request('q')) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . request('q') . '%');
            });
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function show($id)
    {
        $surat = SuratRequest::with(['user', 'suratType', 'files'])->findOrFail($id);
        return view('admin.surat.show', [
            'title' => 'Detail ' . $surat->suratType->name,
            'surat' => $surat
        ]);
    }

    public function approve($id)
    {
        $surat = SuratRequest::findOrFail($id);
        $surat->update([
            'status' => 'approved_secretary'
        ]);

        app(NotificationService::class)->emailOnSekretarisApproved($surat);

        return redirect()->back()->with('success', 'Surat berhasil disetujui dan diteruskan untuk TTD.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        $surat = SuratRequest::findOrFail($id);
        $surat->update([
            'status' => 'rejected',
            'notes' => $request->notes
        ]);

        app(NotificationService::class)->emailOnSekretarisRejected($surat);

        return redirect()->back()->with('success', 'Surat berhasil ditolak.');
    }

    public function uploadSigned(Request $request, $id)
    {
        $request->validate([
            'signed_file' => 'required|file|mimes:pdf|max:2048'
        ]);

        $surat = SuratRequest::findOrFail($id);

        if ($request->hasFile('signed_file')) {
            // Delete old file if exists
            if ($surat->signed_file) {
                Storage::disk('public')->delete($surat->signed_file);
            }

            $path = $request->file('signed_file')->store('signed_surat', 'public');
            $surat->update([
                'status' => 'signed',
                'signed_file' => $path
            ]);

            app(NotificationService::class)->emailOnKepalaDesaSigned($surat);
        }

        return redirect()->back()->with('success', 'Surat berhasil diupload dan ditandai selesai.');
    }

    public function masuk() {
        return view('admin.surat.index', [
            'title' => 'Surat Masuk',
            'surats' => $this->baseQuery('submitted')
        ]);
    }

    public function approved() {
        return view('admin.surat.index', [
            'title' => 'Surat Disetujui',
            'surats' => $this->baseQuery('approved_secretary')
        ]);
    }

    public function rejected() {
        return view('admin.surat.index', [
            'title' => 'Surat Ditolak',
            'surats' => $this->baseQuery('rejected')
        ]);
    }

    public function prosesTtd() {
        return view('admin.surat.index', [
            'title' => 'Surat Proses TTD',
            'surats' => $this->baseQuery('approved_secretary')
        ]);
    }

    public function selesai() {
        return view('admin.surat.index', [
            'title' => 'Surat Selesai',
            'surats' => $this->baseQuery('signed')
        ]);
    }
}
