<?php

namespace App\Http\Controllers\KepalaDesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    public function sign(SuratRequest $surat)
    {
        $pdf = app(PdfService::class)->generate($surat);

        $surat->update([
            'status' => 'signed',
            'signed_file' => $pdf
        ]);

        NotificationService::notifyWarga(
            $surat->user,
            'Surat Anda telah selesai'
        );

        return back();
    }
}
