<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
        Route::get('/approval', [ApprovalController::class, 'index']);
        Route::post('/approve/{surat}', [ApprovalController::class, 'approve']);
        Route::post('/reject/{surat}', [ApprovalController::class, 'reject']);
    });

    // KEPALA DESA
    Route::middleware('role:kepala_desa')->prefix('kepala-desa')->group(function () {
        Route::get('/sign', [SignatureController::class, 'index']);
        Route::post('/sign/{surat}', [SignatureController::class, 'sign']);
    });
});

require __DIR__.'/auth.php';
