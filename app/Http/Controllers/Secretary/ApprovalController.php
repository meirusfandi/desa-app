<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function approve(SuratRequest $surat)
    {
        $surat->update(['status' => 'approved_secretary']);

        NotificationService::sendToKepalaDesa(
            "Surat {$surat->id} siap ditandatangani"
        );

        return back();
    }
}
