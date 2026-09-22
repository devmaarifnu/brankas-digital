<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    BrangkasController,
    ForgotPasswordController,
    HandoverController,
    KeuanganController,
    SettingUserController
};

/*
|--------------------------------------------------------------------------
| Web Routes - BRANGKAS DIGITAL (LP Ma'arif NU PBNU)
|--------------------------------------------------------------------------
|
| Rute aplikasi Brangkas Digital LP Ma'arif NU PBNU.
|
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'loginProses'])->name('login.proses');

Route::middleware('mustlogin')->group(function () {

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/gantipassword', [AuthController::class, 'changePassword'])->name('changepass');

    // ================================================================
    // BRANGKAS DIGITAL - Routes
    // ================================================================

    // Record of Handover
    Route::get('/handover', [HandoverController::class, 'index'])->name('handover.index');
    Route::get('/handover/export', [HandoverController::class, 'export'])->name('handover.export');
    Route::post('/handover', [HandoverController::class, 'store'])->name('handover.store');
    Route::get('/handover/get-items', [HandoverController::class, 'getItemsByKategori'])->name('handover.get-items');
    Route::get('/handover/items', [HandoverController::class, 'getItemsByKategori'])->name('handover.items');

    // Surat Surat Berharga
    Route::get('/brangkas/surat-tanah', [BrangkasController::class, 'suratTanah'])->name('brangkas.surat-tanah');
    Route::get('/brangkas/surat-tanah/export', [BrangkasController::class, 'exportSuratTanah'])->name('brangkas.surat-tanah.export');
    Route::get('/brangkas/surat-tanah/tambah', [BrangkasController::class, 'createSuratTanah'])->name('brangkas.surat-tanah.create');
    Route::post('/brangkas/surat-tanah', [BrangkasController::class, 'storeSuratTanah'])->name('brangkas.surat-tanah.store');
    Route::get('/brangkas/surat-tanah/{id}/edit', [BrangkasController::class, 'editSuratTanah'])->name('brangkas.surat-tanah.edit');
    Route::post('/brangkas/surat-tanah/{id}/update', [BrangkasController::class, 'updateSuratTanah'])->name('brangkas.surat-tanah.update');
    Route::post('/brangkas/surat-tanah/{id}/delete', [BrangkasController::class, 'destroySuratTanah'])->name('brangkas.surat-tanah.destroy');

    Route::get('/brangkas/akta-notaris', [BrangkasController::class, 'aktaNotaris'])->name('brangkas.akta-notaris');
    Route::get('/brangkas/akta-notaris/export', [BrangkasController::class, 'exportAktaNotaris'])->name('brangkas.akta-notaris.export');
    Route::get('/brangkas/akta-notaris/tambah', [BrangkasController::class, 'createAktaNotaris'])->name('brangkas.akta-notaris.create');
    Route::post('/brangkas/akta-notaris', [BrangkasController::class, 'storeAktaNotaris'])->name('brangkas.akta-notaris.store');
    Route::get('/brangkas/akta-notaris/{id}/edit', [BrangkasController::class, 'editAktaNotaris'])->name('brangkas.akta-notaris.edit');
    Route::post('/brangkas/akta-notaris/{id}/update', [BrangkasController::class, 'updateAktaNotaris'])->name('brangkas.akta-notaris.update');
    Route::post('/brangkas/akta-notaris/{id}/delete', [BrangkasController::class, 'destroyAktaNotaris'])->name('brangkas.akta-notaris.destroy');

    Route::get('/brangkas/surat-kendaraan', [BrangkasController::class, 'suratKendaraan'])->name('brangkas.surat-kendaraan');
    Route::get('/brangkas/surat-kendaraan/export', [BrangkasController::class, 'exportSuratKendaraan'])->name('brangkas.surat-kendaraan.export');
    Route::get('/brangkas/surat-kendaraan/kendaraan-list', [BrangkasController::class, 'getKendaraanList'])->name('brangkas.surat-kendaraan.kendaraan-list');
    Route::post('/brangkas/surat-kendaraan', [BrangkasController::class, 'storeSuratKendaraan'])->name('brangkas.surat-kendaraan.store');
    Route::get('/brangkas/surat-kendaraan/{id}/edit', [BrangkasController::class, 'editSuratKendaraan'])->name('brangkas.surat-kendaraan.edit');
    Route::post('/brangkas/surat-kendaraan/{id}/update', [BrangkasController::class, 'updateSuratKendaraan'])->name('brangkas.surat-kendaraan.update');
    Route::post('/brangkas/surat-kendaraan/{id}/delete', [BrangkasController::class, 'destroySuratKendaraan'])->name('brangkas.surat-kendaraan.destroy');

    // Data Aset Lembaga
    Route::get('/brangkas/data-aset', [BrangkasController::class, 'dataAset'])->name('brangkas.data-aset');
    Route::get('/brangkas/data-aset/export', [BrangkasController::class, 'exportDataAset'])->name('brangkas.data-aset.export');
    Route::get('/brangkas/data-aset/tambah', [BrangkasController::class, 'createDataAset'])->name('brangkas.data-aset.create');
    Route::post('/brangkas/data-aset', [BrangkasController::class, 'storeDataAset'])->name('brangkas.data-aset.store');
    Route::get('/brangkas/data-aset/{id}/edit', [BrangkasController::class, 'editDataAset'])->name('brangkas.data-aset.edit');
    Route::post('/brangkas/data-aset/{id}/update', [BrangkasController::class, 'updateDataAset'])->name('brangkas.data-aset.update');
    Route::post('/brangkas/data-aset/{id}/delete', [BrangkasController::class, 'destroyDataAset'])->name('brangkas.data-aset.destroy');

    // Keuangan
    Route::get('/keuangan/pengajuan', [KeuanganController::class, 'pengajuan'])->name('keuangan.pengajuan');
    Route::post('/keuangan/pengajuan', [KeuanganController::class, 'storePengajuan'])->name('keuangan.pengajuan.store');
    Route::post('/keuangan/pengajuan/{id}/approve', [KeuanganController::class, 'approvePengajuan'])->name('keuangan.pengajuan.approve');
    Route::post('/keuangan/pengajuan/{id}/reject', [KeuanganController::class, 'rejectPengajuan'])->name('keuangan.pengajuan.reject');
    Route::get('/keuangan/rekening-koran', [KeuanganController::class, 'rekeningKoran'])->name('keuangan.rekening-koran');
    Route::get('/keuangan/buku-bank', [KeuanganController::class, 'bukuBank'])->name('keuangan.buku-bank');
    Route::get('/keuangan/buku-kas-tunai', [KeuanganController::class, 'bukuKasTunai'])->name('keuangan.buku-kas-tunai');
    Route::get('/keuangan/buku-kas-umum', [KeuanganController::class, 'bukuKasUmum'])->name('keuangan.buku-kas-umum');
    Route::get('/keuangan/rekap-bulanan', [KeuanganController::class, 'rekapBulanan'])->name('keuangan.rekap-bulanan');
    Route::get('/keuangan/rekap-tahunan', [KeuanganController::class, 'rekapTahunan'])->name('keuangan.rekap-tahunan');
    Route::post('/keuangan/dokumen', [KeuanganController::class, 'storeDokumen'])->name('keuangan.dokumen.store');

    // Setting - Users (Super Admin only)
    Route::get('/setting/users', [SettingUserController::class, 'index'])->name('setting.users');
    Route::post('/setting/users', [SettingUserController::class, 'store'])->name('setting.users.store');
    Route::post('/setting/users/{id}/update', [SettingUserController::class, 'update'])->name('setting.users.update');
    Route::post('/setting/users/{id}/delete', [SettingUserController::class, 'destroy'])->name('setting.users.destroy');
    Route::post('/setting/users/{id}/toggle', [SettingUserController::class, 'toggle'])->name('setting.users.toggle');
    Route::post('/setting/users/{id}/update-role', [SettingUserController::class, 'updateRole'])->name('setting.users.updateRole');

    // Export Excel
    Route::get('/brangkas/surat-tanah/export', [BrangkasController::class, 'exportSuratTanah'])->name('brangkas.surat-tanah.export');
    Route::get('/brangkas/akta-notaris/export', [BrangkasController::class, 'exportAktaNotaris'])->name('brangkas.akta-notaris.export');
    Route::get('/brangkas/data-aset/export', [BrangkasController::class, 'exportDataAset'])->name('brangkas.data-aset.export');

    // ================================================================
    // END BRANGKAS DIGITAL
    // ================================================================


    // Dashboard alias
    Route::get('/dashboard', function () {
        return redirect()->route('handover.index');
    })->name('dashboard');

    Route::prefix('a')->group(function () {
        Route::get('/', function () {
            return redirect()->route('handover.index');
        })->name('a.dash');
        Route::get('/dashboard', function () {
            return redirect()->route('handover.index');
        });
    });

    /**
     * Secure Document Serving (Authenticated Users Only + Anti-Indexing)
     */
    Route::get('/uploads/{path}', function ($path) {
        if (str_contains($path, '..')) {
            abort(403, 'Akses ditolak.');
        }

        $file = null;
        if (file_exists(storage_path('app/uploads/' . $path))) {
            $file = storage_path('app/uploads/' . $path);
        } elseif (file_exists(storage_path('app/' . $path))) {
            $file = storage_path('app/' . $path);
        } elseif (file_exists(public_path('uploads/' . $path))) {
            $file = public_path('uploads/' . $path);
        }

        if (!$file) {
            abort(404);
        }

        $res = response()->file($file);
        $res->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        $res->headers->set('Cache-Control', 'no-cache, private, no-store, must-revalidate');
        return $res;
    })->where('path', '.*')->name('secure.uploads');
});

/**
 * Forgot Password
 */
Route::prefix("auth")->group(function () {
    Route::get('forgot', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forgot');
    Route::post('forgot', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.send');
    Route::get('reset/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset');
    Route::post('reset', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.send');
});
