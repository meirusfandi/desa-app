<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratRequest;
use App\Services\NotificationService;

class ApprovalController extends Controller
{
    private const FILTER_STATUSES = [
        'all',
        'submitted',
        'approved_secretary',
        'signed',
        'rejected',
    ];

    private function buildQuery(Request $request, string $status)
    {
        $query = SuratRequest::with(['user', 'suratType', 'files'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('q')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'submitted');
        if (!in_array($status, self::FILTER_STATUSES, true)) {
            $status = 'submitted';
        }

        return view('sekretaris.approval.index', [
            'surats' => $this->buildQuery($request, $status)->paginate(10)->withQueryString(),
            'currentStatus' => $status,
        ]);
    }

    public function show(SuratRequest $surat)
    {
        $surat->loadMissing(['user', 'suratType', 'files']);

        return view('sekretaris.approval.show', [
            'surat' => $surat,
        ]);
    }

    public function approve(SuratRequest $surat, Request $request)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);
        $surat->update([
            'status' => 'approved_secretary',
            'notes' => $validated['notes'] ?? null,
        ]);

        app(NotificationService::class)->emailOnSekretarisApproved($surat);

        return redirect()->route('sekretaris.approval.index')
            ->with('success', 'Surat disetujui');
    }

    public function reject(SuratRequest $surat, Request $request)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
        ]);
        $surat->update([
            'status' => 'rejected',
            'notes' => $validated['notes'],
        ]);

        app(NotificationService::class)->emailOnSekretarisRejected($surat);

        return redirect()->route('sekretaris.approval.index')
            ->with('success', 'Surat ditolak');
    }
}
