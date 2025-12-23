<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #111;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .info-table th,
        .info-table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .section-title {
            font-weight: bold;
            margin-top: 24px;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 12px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature .label {
            margin-bottom: 60px;
        }
        .signature .signature-location {
            margin-bottom: 16px;
            font-weight: 500;
        }
        .signature-image {
            max-width: 220px;
            max-height: 120px;
            display: block;
            margin-left: auto;
            margin-right: 0;
            object-fit: contain;
        }
        .signature-placeholder {
            width: 220px;
            height: 120px;
            border: 1px dashed #bbb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            font-style: italic;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ config('app.name') }}</h2>
        <p>Dokumen layanan: {{ $surat->suratType->name ?? '-' }}</p>
    </div>

    <table class="info-table">
        <tr>
            <th style="width: 30%">Nama Pemohon</th>
            <td>{{ $surat->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal Pengajuan</th>
            <td>{{ optional($surat->created_at)->translatedFormat('d F Y H:i') }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst(str_replace('_', ' ', $surat->status)) }}</td>
        </tr>
    </table>

    <div class="section-title">Data Permohonan</div>
    <table class="info-table">
        @forelse(($surat->data ?? []) as $key => $value)
            <tr>
                <th>{{ str_replace('_', ' ', ucfirst($key)) }}</th>
                <td>{{ $value }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2">Tidak ada data tambahan.</td>
            </tr>
        @endforelse
    </table>

    <div class="section-title">Lampiran</div>
    <table class="info-table">
        @forelse($surat->files as $file)
            <tr>
                <td>{{ basename($file->file_path) }}</td>
            </tr>
        @empty
            <tr>
                <td>Tidak ada lampiran.</td>
            </tr>
        @endforelse
    </table>

    <div class="signature">
        @if(!empty($signatureMeta['location']) || !empty($signatureMeta['date']))
            <div class="signature-location">
                {{ trim(implode(', ', array_filter([$signatureMeta['location'] ?? null, $signatureMeta['date'] ?? null]))) }}
            </div>
        @endif
        <div class="label">{{ $signatureMeta['role'] ?? 'Kepala Desa' }}</div>
        @if($signature)
            <img src="data:{{ $signature['mime'] }};base64,{{ $signature['data'] }}" alt="TTD Kepala Desa" class="signature-image">
        @else
            <div class="signature-placeholder">TTD belum diunggah</div>
        @endif
        <div class="signed-name" style="margin-top: 12px; font-weight: bold;">
            {{ $signatureMeta['name'] ?? 'Kepala Desa' }}
        </div>
    </div>
</body>
</html>
