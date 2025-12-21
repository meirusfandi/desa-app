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

        $pdf = Pdf::loadView('pdf.surat', [
            'surat' => $surat,
            'signature' => $signature,
            'signedBy' => $signedBy,
        ])->output();

        Storage::disk('public')->put($relativePath, $pdf);

        return $relativePath;
    }
}
