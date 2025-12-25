<?php

namespace App\Http\Controllers;

use App\Models\SuratRequest;
use App\Services\DocxTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuratDownloadController extends Controller
{
    public function __construct(private DocxTemplateService $docxTemplateService)
    {
    }

    public function download(Request $request, SuratRequest $surat)
    {
        $user = $request->user();
        if ($user && $user->hasRole('warga') && $surat->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }

        $surat->loadMissing(['user', 'suratType', 'files']);
        $disk = Storage::disk('public');
        $downloadName = $this->downloadFileName($surat);

        if ($surat->signed_file && $disk->exists($surat->signed_file)) {
            $absolute = $disk->path($surat->signed_file);

            return response()->download($absolute, $downloadName, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        $docxInfo = $this->resolveDocxPath($surat);
        if (! $docxInfo) {
            abort(404, 'Template surat belum tersedia.');
        }

        $relativePdf = 'temp_exports/surat-' . $surat->id . '-' . time() . '.pdf';
        $converted = $this->docxTemplateService->convertDocxToPdf($docxInfo['path'], $relativePdf);
        if ($docxInfo['temporary']) {
            $disk->delete($docxInfo['path']);
        }

        if (! $converted || ! $disk->exists($converted)) {
            abort(500, 'Gagal mengekspor surat ke PDF.');
        }

        $absolutePdf = $disk->path($converted);

        return response()->download($absolutePdf, $downloadName, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

    private function resolveDocxPath(SuratRequest $surat): ?array
    {
        $disk = Storage::disk('public');
        $existing = $surat->files->firstWhere('file_type', 'generated_docx');
        if ($existing && $disk->exists($existing->file_path)) {
            return ['path' => $existing->file_path, 'temporary' => false];
        }

        $generated = $this->docxTemplateService->generateFilledDocx($surat);
        if (! $generated || ! $disk->exists($generated)) {
            return null;
        }

        return ['path' => $generated, 'temporary' => true];
    }

    private function downloadFileName(SuratRequest $surat): string
    {
        $typeName = $surat->suratType->name ?? 'surat';
        $slug = Str::slug($typeName, '-');

        return $slug . '-' . $surat->id . '.pdf';
    }
}
