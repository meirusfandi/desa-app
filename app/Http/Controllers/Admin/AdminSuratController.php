<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratRequest;

class AdminSuratController extends Controller
{
    private function baseQuery($status)
    {
        $query = SuratRequest::with(['user', 'suratType'])
            ->where('status', $status);

        if (request('q')) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . request('q') . '%');
            });
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function masuk() {
        return view('admin.surat.index', [
            'title' => 'Surat Masuk',
            'surats' => $this->baseQuery('submitted')
        ]);
    }

    public function approved() {
        return view('admin.surat.index', [
            'title' => 'Surat Disetujui',
            'surats' => $this->baseQuery('approved_secretary')
        ]);
    }

    public function rejected() {
        return view('admin.surat.index', [
            'title' => 'Surat Ditolak',
            'surats' => $this->baseQuery('rejected')
        ]);
    }

    public function prosesTtd() {
        return view('admin.surat.index', [
            'title' => 'Surat Proses TTD',
            'surats' => $this->baseQuery('approved_secretary')
        ]);
    }

    public function selesai() {
        return view('admin.surat.index', [
            'title' => 'Surat Selesai',
            'surats' => $this->baseQuery('signed')
        ]);
    }
}
