<?php

namespace App\Services;

use App\Models\SuratRequest;

class PdfService
{
    public function __construct(private DocxTemplateService $docxTemplateService)
    {
    }

    public function generateSurat(SuratRequest $surat, ?string $signaturePath = null): string
    {
        $relativePath = 'signed_surat/surat-' . $surat->id . '-' . time() . '.pdf';

        $docxPath = $this->docxTemplateService->generateFilledDocx($surat, [], $signaturePath);
        if (! $docxPath) {
            throw new \RuntimeException('Template surat tidak ditemukan.');
        }

        $converted = $this->docxTemplateService->convertDocxToPdf($docxPath, $relativePath);
        if (! $converted) {
            throw new \RuntimeException('Gagal mengonversi template menjadi PDF.');
        }

        return $converted;
    }
}
