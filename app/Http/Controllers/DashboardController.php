<?php

namespace App\Http\Controllers;

use App\Models\{User, SuratRequest, SuratType, Setting};
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
            $totalPending = SuratRequest::where('status', 'submitted')->count();
            $totalProcess = SuratRequest::where('status', 'approved_secretary')->count();
            $totalCompleted = SuratRequest::where('status', 'signed')->count();
            $recentSurats = SuratRequest::with(['user', 'suratType'])
                ->latest()
                ->limit(5)
                ->get();

            return view('sekretaris.dashboard', compact(
                'totalPending',
                'totalProcess',
                'totalCompleted',
                'recentSurats'
            ));
        } else if ($user->hasRole('kepala_desa')) {
            $totalQueue = SuratRequest::where('status', 'approved_secretary')->count();
            $totalSigned = SuratRequest::where('status', 'signed')->count();
            $totalReturned = SuratRequest::where('status', 'rejected')->count();
            $recentSurats = SuratRequest::with(['user', 'suratType'])
                ->whereIn('status', ['approved_secretary', 'signed'])
                ->latest()
                ->limit(5)
                ->get();
            $signatureReady = Setting::where('key', 'kepala_signature')->exists();

            return view('kepala_desa.dashboard', compact(
                'totalQueue',
                'totalSigned',
                'totalReturned',
                'recentSurats',
                'signatureReady'
            ));
        }
    }
}
