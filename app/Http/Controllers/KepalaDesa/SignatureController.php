<?php

namespace App\Http\Controllers\KepalaDesa;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SuratRequest;
use App\Services\PdfService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    private const SIGNATURE_SETTING_KEY = 'kepala_signature';
    private const FILTERABLE_STATUSES = [
        'approved_secretary',
        'signed',
        'rejected',
        'all',
    ];

    public function __construct(private PdfService $pdfService)
    {
    }

    private function baseQuery(Request $request, string $status)
    {
        $query = SuratRequest::with(['user', 'suratType', 'files'])
            ->whereIn('status', ['approved_secretary', 'signed', 'rejected'])
            ->latest();

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
        $status = $request->get('status', 'approved_secretary');
        if (!in_array($status, self::FILTERABLE_STATUSES, true)) {
            $status = 'approved_secretary';
        }

        return view('kepala_desa.surat.index', [
            'surats' => $this->baseQuery($request, $status)->paginate(10)->withQueryString(),
            'currentStatus' => $status,
            'signatureReady' => $this->signatureReady(),
        ]);
    }

    public function show(SuratRequest $surat)
    {
        abort_unless(in_array($surat->status, ['approved_secretary', 'signed', 'rejected'], true), 404);

        $surat->loadMissing(['user', 'suratType', 'files']);

        return view('kepala_desa.surat.show', [
            'surat' => $surat,
            'signatureReady' => $this->signatureReady(),
        ]);
    }

    public function sign(Request $request, SuratRequest $surat)
    {
        abort_unless($surat->status === 'approved_secretary', 400);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);
        $signatureSetting = $this->getSignatureSetting();
        if (! $signatureSetting || ! $signatureSetting->value || ! Storage::disk('public')->exists($signatureSetting->value)) {
            return redirect()->route('kepala.signature.edit')
                ->with('error', 'Unggah file tanda tangan terlebih dahulu sebelum menandatangani surat.');
        }

        if ($surat->signed_file) {
            Storage::disk('public')->delete($surat->signed_file);
        }

        $path = $this->pdfService->generateSurat($surat, $signatureSetting->value, auth()->user()->name ?? 'Kepala Desa');

        $surat->update([
            'status' => 'signed',
            'signed_file' => $path,
            'notes' => $validated['notes'] ?? $surat->notes,
        ]);

        app(NotificationService::class)->emailOnKepalaDesaSigned($surat);

        return redirect()->route('kepala.surat.show', $surat)
            ->with('success', 'Surat berhasil ditandatangani dan dikirim ke warga.');
    }

    public function reject(Request $request, SuratRequest $surat)
    {
        abort_unless($surat->status === 'approved_secretary', 400);

        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $surat->update([
            'status' => 'rejected',
            'notes' => $validated['notes'],
            'signed_file' => null,
        ]);

        return redirect()->route('kepala.surat.index')
            ->with('success', 'Surat dikembalikan ke sekretaris dengan catatan.');
    }

    public function editSignature()
    {
        return view('kepala_desa.signature.edit', [
            'signaturePath' => $this->getSignatureSetting()?->value,
        ]);
    }

    public function updateSignature(Request $request)
    {
        $validated = $request->validate([
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $setting = $this->getSignatureSetting();
        if ($setting && $setting->value) {
            Storage::disk('public')->delete($setting->value);
        }

        $path = $request->file('signature')->store('signatures', 'public');

        Setting::updateOrCreate(
            ['key' => self::SIGNATURE_SETTING_KEY],
            ['value' => $path]
        );

        return redirect()->route('kepala.signature.edit')
            ->with('success', 'TTD Kepala Desa berhasil diperbarui.');
    }

    private function getSignatureSetting(): ?Setting
    {
        return Setting::where('key', self::SIGNATURE_SETTING_KEY)->first();
    }

    private function signatureReady(): bool
    {
        $setting = $this->getSignatureSetting();

        return $setting && $setting->value && Storage::disk('public')->exists($setting->value);
    }
}
