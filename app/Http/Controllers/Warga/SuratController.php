<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    public function store(Request $request)
    {
        $surat = SuratRequest::create([
            'user_id' => auth()->id(),
            'surat_type_id' => $request->surat_type_id,
        ]);

        app(NotificationService::class)
            ->notifySecretary("Surat baru diajukan");

        return redirect()->back()->with('success','Surat berhasil diajukan');
    }
}
