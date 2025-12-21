<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Warga\SuratController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSuratTypeController;
use App\Http\Controllers\Admin\AdminSuratController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminRoleController; // Note: Ensure this file exists or removed
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
    Route::middleware('role:warga')->prefix('warga')->name('warga.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::resource('surat', SuratController::class);
    });

    // ADMIN & SEKRETARIS
    Route::middleware('role:admin|sekretaris')->prefix('admin')->name('admin.')->group(function () {
        // DASHBOARD
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // SURAT MENYURAT
        Route::prefix('surat')->name('surat.')->group(function () {
            Route::get('/masuk', [AdminSuratController::class, 'masuk'])->name('masuk');
            Route::get('/approved', [AdminSuratController::class, 'approved'])->name('approved');
            Route::get('/rejected', [AdminSuratController::class, 'rejected'])->name('rejected');
            Route::get('/proses-ttd', [AdminSuratController::class, 'prosesTtd'])->name('proses-ttd');
            Route::get('/selesai', [AdminSuratController::class, 'selesai'])->name('selesai');

            // Processing Actions
            Route::get('/{id}', [AdminSuratController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [AdminSuratController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [AdminSuratController::class, 'reject'])->name('reject');
            Route::post('/{id}/upload-signed', [AdminSuratController::class, 'uploadSigned'])->name('upload-signed');
        });

        // MASTER (Admin Only)
        Route::middleware('role:admin')->prefix('master')->name('master.')->group(function () {
            Route::resource('/users', AdminUserController::class);
            Route::resource('/roles', AdminRoleController::class);
            Route::resource('/jenis-surat', AdminSuratTypeController::class);
            Route::get('/pengaturan', [AdminSettingController::class, 'index'])
                ->name('pengaturan');
            Route::post('/pengaturan', [AdminSettingController::class, 'update'])
                ->name('pengaturan.update');
        });
    });

    // SEKRETARIS
    Route::middleware('role:sekretaris')->prefix('sekretaris')->name('sekretaris.')->group(function () {
        Route::get('/approval', [ApprovalController::class, 'index'])
            ->name('approval.index');

        // Secretary Surat Management
        Route::prefix('surat')->name('surat.')->group(function () {
            Route::get('/masuk', [App\Http\Controllers\Admin\AdminSuratController::class, 'masuk'])
                ->name('masuk');
            Route::get('/approved', [App\Http\Controllers\Admin\AdminSuratController::class, 'approved'])
                ->name('approved');
            Route::get('/rejected', [App\Http\Controllers\Admin\AdminSuratController::class, 'rejected'])
                ->name('rejected');
            Route::get('/proses-ttd', [App\Http\Controllers\Admin\AdminSuratController::class, 'prosesTtd'])
                ->name('proses-ttd');
            Route::get('/selesai', [App\Http\Controllers\Admin\AdminSuratController::class, 'selesai'])
                ->name('selesai');

            // Processing Actions
            Route::get('/{id}', [App\Http\Controllers\Admin\AdminSuratController::class, 'show'])
                ->name('show');
            Route::post('/{id}/approve', [App\Http\Controllers\Admin\AdminSuratController::class, 'approve'])
                ->name('approve');
            Route::post('/{id}/reject', [App\Http\Controllers\Admin\AdminSuratController::class, 'reject'])
                ->name('reject');
            Route::post('/{id}/upload-signed', [App\Http\Controllers\Admin\AdminSuratController::class, 'uploadSigned'])
                ->name('upload-signed');
        });

        Route::get('/approval/{surat}', [ApprovalController::class, 'show'])
            ->name('approval.show');

        Route::post('/approval/{surat}/approve', [ApprovalController::class, 'approve'])
            ->name('approval.approve');

        Route::post('/approval/{surat}/reject', [ApprovalController::class, 'reject'])
            ->name('approval.reject');
    });

    // KEPALA DESA
    Route::middleware('role:kepala_desa')->prefix('kepala-desa')->name('kepala.')->group(function () {
        Route::get('/signature', [SignatureController::class, 'editSignature'])->name('signature.edit');
        Route::post('/signature', [SignatureController::class, 'updateSignature'])->name('signature.update');

        Route::prefix('surat')->name('surat.')->group(function () {
            Route::get('/', [SignatureController::class, 'index'])->name('index');
            Route::get('/{surat}', [SignatureController::class, 'show'])->name('show');
            Route::post('/{surat}/sign', [SignatureController::class, 'sign'])->name('sign');
            Route::post('/{surat}/reject', [SignatureController::class, 'reject'])->name('reject');
        });
    });
});

require __DIR__.'/auth.php';
