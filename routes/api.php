<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiMasterController;
use App\Http\Controllers\Api\ApiSatpenController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Admin\AdminPTKController;
use App\Http\Controllers\Admin\ExportExcelController;
use App\Http\Controllers\Admin\SATPENController as SATPENControllerAdmin;
use App\Http\Controllers\Admin\VirtualNPSNController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\NpypController;
use App\Http\Controllers\PTKController;
use App\Http\Controllers\SatpenController;
use App\Http\Controllers\SyncController;

/*
|--------------------------------------------------------------------------
| API Routes - SIPINTER Backend REST API
|--------------------------------------------------------------------------
|
| All routes in this file are assigned the "api" middleware group.
| Responses are returned in JSON format.
|
*/

// =========================================================================
// 1. PUBLIC AUTH & ACCOUNT ENDPOINTS
// =========================================================================
Route::prefix('auth')->group(function () {
    Route::post('/login', [ApiAuthController::class, 'login'])->name('api.auth.login');
    Route::post('/check-npsn', [ApiAuthController::class, 'checkNpsn'])->name('api.auth.check-npsn');
    Route::post('/virtual-npsn', [ApiAuthController::class, 'requestVirtualNPSN'])->name('api.auth.request-vnpsn');
    Route::post('/register-satpen', [ApiSatpenController::class, 'register'])->name('api.auth.register-satpen');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('api.auth.forgot');
    Route::post('/reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('api.auth.reset');
});

// =========================================================================
// 2. PUBLIC MASTER DATA & INFORMASI
// =========================================================================
Route::prefix('master')->group(function () {
    Route::get('/provinsi', [ApiMasterController::class, 'listProvinsi'])->name('api.master.provinsi');
    Route::get('/provinsi/{id}', [ApiMasterController::class, 'showProvinsi'])->name('api.master.provinsi.show');
    Route::get('/kabupaten', [ApiMasterController::class, 'listKabupaten'])->name('api.master.kabupaten');
    Route::get('/kabupaten/{id}', [ApiMasterController::class, 'showKabupaten'])->name('api.master.kabupaten.show');
    Route::get('/cabang', [ApiMasterController::class, 'listCabang'])->name('api.master.cabang');
    Route::get('/cabang/{id}', [ApiMasterController::class, 'showCabang'])->name('api.master.cabang.show');
    Route::get('/jenjang', [ApiMasterController::class, 'listJenjang'])->name('api.master.jenjang');
    Route::get('/kategori', [ApiMasterController::class, 'listKategori'])->name('api.master.kategori');
    Route::get('/tapel', [ApiMasterController::class, 'listTapel'])->name('api.master.tapel');
});

Route::prefix('informasi')->group(function () {
    Route::get('/', [ApiMasterController::class, 'listInformasi'])->name('api.informasi.list');
    Route::get('/{slugOrId}', [ApiMasterController::class, 'showInformasi'])->name('api.informasi.show');
});

// Public Satpen search & reference check
Route::prefix('satpen')->group(function () {
    Route::get('/search', [ApiController::class, 'searchSatpen'])->name('api.satpen.search');
    Route::get('/public/{satpenId}', [ApiController::class, 'getSatpenById'])->name('api.satpen.public');
    Route::get('/check-dapo/{npsn}', [ApiController::class, 'checkNPSNtoReferensiData'])->name('api.satpen.check-dapo');
});

// =========================================================================
// 3. PROTECTED AUTHENTICATED ENDPOINTS (Requires Sanctum Bearer Token)
// =========================================================================
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth & Profile
    Route::get('/auth/me', [ApiAuthController::class, 'me'])->name('api.auth.me');
    Route::post('/auth/logout', [ApiAuthController::class, 'logout'])->name('api.auth.logout');
    Route::post('/auth/change-password', [ApiAuthController::class, 'changePassword'])->name('api.auth.change-password');

    // ---------------------------------------------------------------------
    // OPERATOR ROUTES (School level)
    // ---------------------------------------------------------------------
    Route::middleware('onlyoperator')->prefix('operator')->group(function () {
        Route::get('/satpen', [ApiSatpenController::class, 'mySatpen'])->name('api.operator.satpen');
        
        // PDPTK & Other data sync
        Route::get('/pdptk', [SatpenController::class, 'indexPDPTK'])->name('api.operator.pdptk');
        Route::put('/pdptk', [SatpenController::class, 'modifPDPTK'])->name('api.operator.pdptk.save');
        Route::get('/pdptk/dapo/{npsn}', [SatpenController::class, 'hitDapo'])->name('api.operator.pdptk.dapo');
        Route::get('/other', [SatpenController::class, 'indexOther'])->name('api.operator.other');
        Route::put('/other', [SatpenController::class, 'modifOther'])->name('api.operator.other.save');
        Route::get('/other/dapo/{npsn}', [SatpenController::class, 'hitReferensi'])->name('api.operator.other.dapo');

        // PTK (Teachers & Educational Staff)
        Route::prefix('ptk')->group(function () {
            Route::get('/data', [PTKController::class, 'getPTKData'])->name('api.operator.ptk.data');
            Route::get('/status-counts', [PTKController::class, 'getStatusCounts'])->name('api.operator.ptk.counts');
            Route::post('/', [PTKController::class, 'store'])->name('api.operator.ptk.store');
            Route::get('/{id}', [PTKController::class, 'show'])->name('api.operator.ptk.show');
            Route::put('/{id}', [PTKController::class, 'update'])->name('api.operator.ptk.update');
            Route::post('/{id}/revisi', [PTKController::class, 'submitRevisi'])->name('api.operator.ptk.revisi');
            Route::delete('/{id}', [PTKController::class, 'destroy'])->name('api.operator.ptk.destroy');
        });
    });

    // ---------------------------------------------------------------------
    // ADMIN ROUTES (Admin Pusat, Admin Wilayah, Admin Cabang, Super Admin)
    // ---------------------------------------------------------------------
    Route::middleware('onlyadmin')->prefix('admin')->group(function () {

        // Satpen Management
        Route::get('/satpen', [ApiSatpenController::class, 'index'])->name('api.admin.satpen.list');
        Route::get('/satpen/{id}', [ApiSatpenController::class, 'show'])->name('api.admin.satpen.show');
        Route::put('/satpen/{id}/status', [ApiSatpenController::class, 'updateStatus'])->name('api.admin.satpen.status');
        Route::delete('/satpen/{satpen}', [SATPENControllerAdmin::class, 'destroySatpen'])->middleware('superadmin')->name('api.admin.satpen.destroy');

        // Dashboard Statistics & Counts
        Route::prefix('dashboard')->group(function () {
            Route::get('/provcount', [ApiController::class, 'getProvAndCount'])->name('api.admin.dash.provcount');
            Route::get('/kabcount/{provId?}', [ApiController::class, 'getKabAndCount'])->name('api.admin.dash.kabcount');
            Route::get('/pccount', [ApiController::class, 'getPCAndCount'])->name('api.admin.dash.pccount');
            Route::get('/jenjangcount/{provId?}', [ApiController::class, 'getJenjangAndCount'])->name('api.admin.dash.jenjangcount');
            Route::get('/jenjangcount/{provId}/{cabangId}', [ApiController::class, 'getJenjangAndCountByCabang'])->name('api.admin.dash.jenjangcountbycabang');
            Route::get('/ptkcount', [DashboardApiController::class, 'getPTKCount'])->name('api.admin.dash.ptkcount');
            Route::get('/pdcount', [DashboardApiController::class, 'getPDCount'])->name('api.admin.dash.pdcount');
        });

        // PTK Admin Verification
        Route::prefix('ptk')->group(function () {
            Route::get('/data', [AdminPTKController::class, 'getData'])->name('api.admin.ptk.data');
            Route::get('/statistics', [AdminPTKController::class, 'statistics'])->name('api.admin.ptk.statistics');
            Route::get('/{id}/detail', [AdminPTKController::class, 'detail'])->name('api.admin.ptk.detail');
            Route::post('/action', [AdminPTKController::class, 'action'])->name('api.admin.ptk.action');
        });

        // NPYP & Sekolah Naungan Data
        Route::prefix('npyp')->group(function () {
            Route::get('/satpen-list', [NpypController::class, 'getSatpenList'])->name('api.admin.npyp.satpen-list');
            Route::get('/sekolah-naungan', [NpypController::class, 'getSekolahNaunganData'])->name('api.admin.npyp.sekolah-naungan');
            Route::post('/sekolah-naungan', [NpypController::class, 'addSekolahNaungan'])->name('api.admin.npyp.add-naungan');
            Route::delete('/sekolah-naungan/{id}', [NpypController::class, 'deleteSekolahNaungan'])->name('api.admin.npyp.delete-naungan');
            Route::get('/cabang-data', [NpypController::class, 'getNpypCabangData'])->name('api.admin.npyp.cabang-data');
            Route::get('/wilayah-data', [NpypController::class, 'getNpypWilayahData'])->name('api.admin.npyp.wilayah-data');
            Route::get('/rekap-ptk', [NpypController::class, 'rekapPtkNasional'])->name('api.admin.npyp.rekap-ptk');
            Route::get('/ptk-detail/{id}', [NpypController::class, 'getPtkDetail'])->name('api.admin.npyp.ptk-detail');
        });

        // Virtual NPSN Administration
        Route::prefix('vnpsn')->group(function () {
            Route::get('/', [VirtualNPSNController::class, 'listPermohonanVNPSN'])->name('api.admin.vnpsn.list');
            Route::put('/{virtualNPSN}/accept', [VirtualNPSNController::class, 'generateVirtualNumber'])->name('api.admin.vnpsn.accept');
            Route::delete('/{virtualNPSN}/reject', [VirtualNPSNController::class, 'rejectPermohonanVNPSN'])->name('api.admin.vnpsn.reject');
            Route::delete('/{virtualNPSN}', [VirtualNPSNController::class, 'destroyVNPSN'])->middleware('superadmin')->name('api.admin.vnpsn.destroy');
        });

        // Excel Export Endpoints
        Route::prefix('export')->group(function () {
            Route::get('/satpen', [ExportExcelController::class, 'exportSatpentoExcel'])->name('api.export.satpen');
            Route::get('/pdptk', [ExportExcelController::class, 'exportPDPTKtoExcel'])->name('api.export.pdptk');
            Route::get('/other', [ExportExcelController::class, 'exportOthersDatatoExcel'])->name('api.export.other');
            Route::get('/wilayah', [ExportExcelController::class, 'exportWilayahtoExcel'])->name('api.export.wilayah');
            Route::get('/cabang', [ExportExcelController::class, 'exportCabangtoExcel'])->name('api.export.cabang');
        });

        // Master Data Management (Super Admin only)
        Route::middleware('superadmin')->prefix('master')->group(function () {
            Route::post('/provinsi', [ApiMasterController::class, 'storeProvinsi'])->name('api.admin.master.provinsi.store');
            Route::put('/provinsi/{id}', [ApiMasterController::class, 'updateProvinsi'])->name('api.admin.master.provinsi.update');
            Route::delete('/provinsi/{id}', [ApiMasterController::class, 'destroyProvinsi'])->name('api.admin.master.provinsi.destroy');
            Route::post('/tapel', [ApiMasterController::class, 'storeTapel'])->name('api.admin.master.tapel.store');
            Route::put('/tapel/{id}', [ApiMasterController::class, 'updateTapel'])->name('api.admin.master.tapel.update');
            Route::delete('/tapel/{id}', [ApiMasterController::class, 'destroyTapel'])->name('api.admin.master.tapel.destroy');
        });
    });
});

// =========================================================================
// 4. SYSTEM & SYNC ENDPOINTS (Token-verified)
// =========================================================================
Route::middleware('authverifytoken')->group(function () {
    Route::post('/sync', [SyncController::class, 'bypassExistingData'])->name('api.sync.data');
    Route::get('/clean-vnpsn', [VirtualNPSNController::class, 'checkAndRemoveUnusedVNPSN'])->name('api.vnpsn.clean');
});
