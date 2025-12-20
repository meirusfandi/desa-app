<?php

namespace App\Http\Controllers;

use App\Models\{User, SuratRequest, SuratType};
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole('warga')) {
            $totalSurat = SuratRequest::where('user_id', $user->id)->count();
            $totalSuratSelesai = SuratRequest::where('user_id', $user->id)->where('status', 'signed')->count();
            return view('warga.dashboard', compact('totalSurat', 'totalSuratSelesai'));
        } else if ($user->hasRole('admin')) {
            $totalSuratMasuk = SuratRequest::where('status', 'submitted')->count();
            $totalSuratSelesai = SuratRequest::where('status', 'signed')->count();
            $totalJenisSurat = SuratType::count();
            $totalUser = User::count();
            $totalRole = Role::count();
            return view('admin.dashboard', compact('totalSuratMasuk', 'totalSuratSelesai', 'totalJenisSurat', 'totalUser', 'totalRole'));
        } else if ($user->hasRole('sekretaris')) {
            return view('sekretaris.dashboard');
        } else if ($user->hasRole('kepala_desa')) {
            return view('kepala_desa.dashboard');
        }
    }
}
