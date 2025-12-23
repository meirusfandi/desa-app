<?php

namespace App\Services;

use App\Models\SuratRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generateSurat(SuratRequest $surat, ?string $signaturePath = null, ?string $signedBy = null): string
    {
        $relativePath = 'signed_surat/surat-' . $surat->id . '-' . time() . '.pdf';

        $signature = null;
        if ($signaturePath && Storage::disk('public')->exists($signaturePath)) {
            $signature = [
                'data' => base64_encode(Storage::disk('public')->get($signaturePath)),
                'mime' => Storage::disk('public')->mimeType($signaturePath) ?? 'image/png',
            ];
        }

        $signatureMeta = $this->signatureMeta($surat, $signedBy);

        $pdf = Pdf::loadView('pdf.surat', [
            'surat' => $surat,
            'signature' => $signature,
            'signatureMeta' => $signatureMeta,
        ])->output();

        Storage::disk('public')->put($relativePath, $pdf);

        return $relativePath;
    }

        private function signatureMeta(SuratRequest $surat, ?string $signedBy): array
        {
            $desaName = app_setting('desa_name');
            $location = app_setting('signature_location', $desaName);
            $roleDefault = $desaName ? 'Kepala Desa ' . $desaName : 'Kepala Desa';
            $signedAt = $surat->signed_at ?? now();

            return [
                'location' => $location,
                'date' => optional($signedAt)->translatedFormat('d F Y'),
                'role' => app_setting('signature_role', $roleDefault),
                'name' => app_setting('signature_name', $signedBy ?? $roleDefault),
            ];
        }
}
