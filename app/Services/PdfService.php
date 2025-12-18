<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateSurat($surat)
    {
        $path = storage_path("app/public/surat-{$surat->id}.pdf");

        Pdf::loadView('pdf.surat', [
            'surat' => $surat
        ])->save($path);

        return $path;
    }
}
