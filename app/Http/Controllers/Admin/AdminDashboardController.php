<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, SuratRequest, SuratType};
use Spatie\Permission\Models\Role;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalSuratMasuk' => SuratRequest::count(),
            'totalSuratSelesai' => SuratRequest::where('status','signed')->count(),
            'totalJenisSurat' => SuratType::count(),
            'totalUser' => User::count(),
            'totalRole' => Role::count(),
        ]);
    }
}
