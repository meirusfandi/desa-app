<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Warga\SuratController;
use App\Http\Controllers\Admin\SuratTypeController;
use App\Http\Controllers\Sekretaris\ApprovalController;
use App\Http\Controllers\KepalaDesa\SignatureController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // WARGA
    Route::middleware('role:warga')->prefix('warga')->group(function () {
        Route::get('/surat', [SuratController::class, 'index']);
        Route::post('/surat', [SuratController::class, 'store']);
    });

    // ADMIN
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::resource('/surat-types', SuratTypeController::class);
    });

    // SEKRETARIS
    Route::middleware('role:sekretaris')->prefix('sekretaris')->group(function () {
        Route::get('/approval', [ApprovalController::class, 'index'])
            ->name('approval.index');

        Route::get('/approval/{surat}', [ApprovalController::class, 'show'])
            ->name('approval.show');

        Route::post('/approval/{surat}/approve', [ApprovalController::class, 'approve'])
            ->name('approval.approve');

        Route::post('/approval/{surat}/reject', [ApprovalController::class, 'reject'])
            ->name('approval.reject');
    });

    // KEPALA DESA
    Route::middleware('role:kepala_desa')->prefix('kepala-desa')->group(function () {
        Route::get('/sign', [SignatureController::class, 'index']);
        Route::post('/sign/{surat}', [SignatureController::class, 'sign']);
    });
});

require __DIR__.'/auth.php';
