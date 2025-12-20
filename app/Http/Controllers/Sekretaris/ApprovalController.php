<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratRequest;

class ApprovalController extends Controller
{
    public function index()
    {
        return view('sekretaris.approval.index', [
            'surats' => SuratRequest::with(['user','suratType','files'])
                ->where('status', 'submitted')
                ->latest()
                ->get()
        ]);
    }

    public function show(SuratRequest $surat)
    {
        return view('sekretaris.approval.show', compact('surat'));
    }

    public function approve(SuratRequest $surat, Request $request)
    {
        $request->validate(['notes' => 'required']);
        $surat->update([
            'status' => 'approved_secretary',
            'notes' => $request->notes
        ]);

        // nanti kita sambungkan ke WhatsApp + Email
        return redirect()->route('sekretaris.approval.index')
            ->with('success', 'Surat disetujui');
    }

    public function reject(SuratRequest $surat, Request $request)
    {
        $request->validate(['notes' => 'required']);
        $surat->update([
            'status' => 'rejected_secretary',
            'notes' => $request->notes
        ]);

        // nanti kita sambungkan ke WhatsApp + Email
        return redirect()->route('sekretaris.approval.index')
            ->with('success', 'Surat ditolak');
    }
}
