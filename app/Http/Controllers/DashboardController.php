<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match (true) {
            $user->hasRole('admin') => view('admin.dashboard'),
            $user->hasRole('sekretaris') => view('sekretaris.dashboard'),
            $user->hasRole('kepala_desa') => view('kepala_desa.dashboard'),
            default => view('warga.dashboard'),
        };
    }
}
