<?php

namespace App\Services;

use App\Models\SuratRequest;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\Process\Process;
use ZipArchive;

class DocxTemplateService
{
    private const RESERVED_PLACEHOLDERS = [
        'nama_pemohon',
        'tanggal_pengajuan',
        'signature_location',
        'signature_date',
        'signature_role',
        'signature_name',
        'signature_image',
    ];

    /**
     * Extract placeholder keys from a DOCX file.
     *
     * Placeholders should be written in Word as: ${field_key}
     *
     * @return array<int, string>
     */
    public function extractPlaceholdersFromDocx(string $absolutePath): array
    {
        $zip = new ZipArchive();
        $opened = $zip->open($absolutePath);
        if ($opened !== true) {
            return [];
        }

        $xmlFiles = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (!$name) {
                continue;
            }

            // main doc + headers/footers
            if (preg_match('#^word/(document|header\d+|footer\d+)\.xml$#', $name) === 1) {
                $xmlFiles[] = $name;
            }
        }

        $seen = [];
        $orderedKeys = [];
        foreach ($xmlFiles as $name) {
            $xml = $zip->getFromName($name);
            if (!is_string($xml) || $xml === '') {
                continue;
            }

            // Reconstruct text: placeholders might be split across runs.
            $text = '';
            if (preg_match_all('#<w:t[^>]*>(.*?)</w:t>#s', $xml, $matches) === 1) {
                // (will not happen; preg_match_all returns int >= 0)
            }
            if (!empty($matches[1])) {
                foreach ($matches[1] as $t) {
                    $text .= html_entity_decode(strip_tags((string) $t), ENT_QUOTES | ENT_XML1, 'UTF-8');
                }
            }

            if ($text === '') {
                continue;
            }

            if (preg_match_all('/\$\{([A-Za-z0-9_\-\.]+)\}/', $text, $m)) {
                foreach ($m[1] as $key) {
                    if (!isset($seen[$key])) {
                        $seen[$key] = true;
                        $orderedKeys[] = $key;
                    }
                }
            }
        }

        $zip->close();

        return $orderedKeys;
    }

    /**
     * Generate a filled DOCX for a surat request and store it.
     *
     * @param array<string, string> $overrides
     * @return string|null Stored relative path (public disk)
     */
    public function generateFilledDocx(SuratRequest $surat, array $overrides = [], ?string $signaturePath = null): ?string
    {
        $type = $surat->suratType;
        if (!$type || !$type->template_doc_path) {
            return null;
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($type->template_doc_path)) {
            return null;
        }

        $templateAbsolute = $disk->path($type->template_doc_path);

        $processor = new TemplateProcessor($templateAbsolute);

        $data = (array) ($surat->data ?? []);
        foreach ($data as $key => $value) {
            if (in_array($key, self::RESERVED_PLACEHOLDERS, true)) {
                continue;
            }
            $processor->setValue((string) $key, (string) $value);
        }

        foreach ($this->systemPlaceholderValues($surat) as $key => $value) {
            $processor->setValue($key, (string) $value);
        }

        foreach ($overrides as $key => $value) {
            $processor->setValue((string) $key, (string) $value);
        }

        $this->applySignatureImage($processor, $signaturePath);

        $relativeOut = 'generated_surat/surat-' . $surat->id . '-' . time() . '.docx';
        $absoluteOut = $disk->path($relativeOut);

        // Ensure directory exists
        if (!is_dir(dirname($absoluteOut))) {
            @mkdir(dirname($absoluteOut), 0755, true);
        }

        $processor->saveAs($absoluteOut);

        return $relativeOut;
    }

    /**
     * Convert a generated DOCX (stored on the public disk) into a PDF on the same disk.
     */
    public function convertDocxToPdf(string $relativeDocxPath, ?string $relativePdfPath = null): ?string
    {
        $disk = Storage::disk('public');
        if (! $disk->exists($relativeDocxPath)) {
            return null;
        }

        $absoluteIn = $disk->path($relativeDocxPath);
        $relativePdfPath ??= $this->defaultPdfPath($relativeDocxPath);
        $absoluteOut = $disk->path($relativePdfPath);

        if (!is_dir(dirname($absoluteOut))) {
            @mkdir(dirname($absoluteOut), 0755, true);
        }

        if ($this->convertWithLibreOffice($absoluteIn, $absoluteOut)) {
            return $relativePdfPath;
        }

        if ($this->convertWithPhpWord($absoluteIn, $absoluteOut)) {
            return $relativePdfPath;
        }

        return null;
    }

    public static function reservedPlaceholders(): array
    {
        return self::RESERVED_PLACEHOLDERS;
    }

    private function systemPlaceholderValues(SuratRequest $surat): array
    {
        $desaName = app_setting('desa_name');
        $defaultRole = $desaName ? 'Kepala Desa ' . $desaName : 'Kepala Desa';

        return [
            'nama_pemohon' => (string) ($surat->user?->name ?? ''),
            'tanggal_pengajuan' => optional($surat->created_at)->translatedFormat('d F Y'),
            'signature_location' => (string) app_setting('signature_location', $desaName ?? ''),
            'signature_date' => optional($surat->signed_at)->translatedFormat('d F Y'),
            'signature_role' => (string) app_setting('signature_role', $defaultRole),
            'signature_name' => (string) app_setting('signature_name', ''),
        ];
    }

    private function applySignatureImage(TemplateProcessor $processor, ?string $signaturePath): void
    {
        if (! $signaturePath) {
            return;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($signaturePath)) {
            return;
        }

        try {
            $processor->setImageValue('signature_image', [
                'path' => $disk->path($signaturePath),
                'ratio' => true,
                'width' => 220,
                'height' => 120,
            ]);
        } catch (\Throwable $e) {
            // Ignore missing placeholder or renderer issues to keep generation resilient.
        }
    }

    private function defaultPdfPath(string $relativeDocxPath): string
    {
        $basename = pathinfo($relativeDocxPath, PATHINFO_FILENAME) ?: ('surat-' . uniqid());
        return 'signed_surat/' . $basename . '.pdf';
    }

    private function convertWithLibreOffice(string $absoluteIn, string $absoluteOut): bool
    {
        $binary = config('services.libreoffice.binary')
            ?? env('LIBREOFFICE_BINARY')
            ?? 'soffice';

        $process = new Process([
            $binary,
            '--headless',
            '--convert-to',
            'pdf:writer_pdf_Export',
            '--outdir',
            dirname($absoluteOut),
            $absoluteIn,
        ]);
        $process->setTimeout((int) (config('services.libreoffice.timeout', 30)));

        try {
            $process->run();
        } catch (\Throwable $e) {
            return false;
        }

        if (! $process->isSuccessful()) {
            return false;
        }

        $generated = dirname($absoluteOut) . DIRECTORY_SEPARATOR . (pathinfo($absoluteIn, PATHINFO_FILENAME) . '.pdf');
        if (! file_exists($generated)) {
            return false;
        }

        if ($generated !== $absoluteOut) {
            @unlink($absoluteOut);
            if (! @rename($generated, $absoluteOut)) {
                return false;
            }
        }

        return true;
    }

    private function convertWithPhpWord(string $absoluteIn, string $absoluteOut): bool
    {
        Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);
        Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));

        try {
            $phpWord = IOFactory::load($absoluteIn);
            $writer = IOFactory::createWriter($phpWord, 'PDF');
            $writer->save($absoluteOut);
        } catch (\Throwable $e) {
            return false;
        }

        return file_exists($absoluteOut);
    }
}
