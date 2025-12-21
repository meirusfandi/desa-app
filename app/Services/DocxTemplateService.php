<?php

namespace App\Services;

use App\Models\SuratRequest;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class DocxTemplateService
{
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

        $placeholders = [];
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
                    $placeholders[$key] = true;
                }
            }
        }

        $zip->close();

        $keys = array_keys($placeholders);
        sort($keys);

        return $keys;
    }

    /**
     * Generate a filled DOCX for a surat request and store it.
     *
     * @return string Stored relative path (public disk)
     */
    public function generateFilledDocx(SuratRequest $surat): ?string
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
            $processor->setValue((string) $key, (string) $value);
        }

        // Useful defaults
        $processor->setValue('nama_pemohon', (string) ($surat->user?->name ?? ''));
        $processor->setValue('tanggal_pengajuan', optional($surat->created_at)->format('Y-m-d'));

        $relativeOut = 'generated_surat/surat-' . $surat->id . '-' . time() . '.docx';
        $absoluteOut = $disk->path($relativeOut);

        // Ensure directory exists
        if (!is_dir(dirname($absoluteOut))) {
            @mkdir(dirname($absoluteOut), 0755, true);
        }

        $processor->saveAs($absoluteOut);

        return $relativeOut;
    }
}
