<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    BHPNUController,
    BrangkasController,
    CoretaxController,
    ForgotPasswordController,
    GeneralController,
    HandoverController,
    KeuanganController,
    OSSController,
    SatpenController,
    FileViewerController,
    NpypController,
    NPYPUserController,
    PTKController,
    Settings,
    SettingUserController,
    ProfileORGController
};
use App\Http\Controllers\Admin\{
    SATPENController as SATPENControllerAdmin,
    BHPNUController as BHPNUControllerAdmin,
    CoretaxAdminController,
    OSSController as OSSControllerAdmin,
    VirtualNPSNController,
    UsersController,
    ProfileController,
    ExportExcelController,
    AdminPTKController,
    ShortUrlController
};
use App\Http\Controllers\Master\{
    InformasiController,
    JenjangPendidikanController,
    KabupatenController,
    PengurusCabangController,
    PropinsiController,
    DapoController,
    TahunPelajaranController
};
use App\Http\Controllers\Api\{ApiController, DashboardApiController};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');
Route::get('/verify/{qrcode?}', [GeneralController::class, 'verifyDokumenPage'])->name('verify');
Route::get('/informasi/{slug?}', [GeneralController::class, 'readInformasiPage'])->name('informasi');
Route::get('/informasi/download/{filename?}', [GeneralController::class, 'downloadFileInformasi'])->name('informasi.download');
Route::get('/kontak', [GeneralController::class, 'contactPage'])->name('kontak');
Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'loginProses'])->name('login.proses');
Route::get('/register', [AuthController::class, 'registerPage'])->name('register');
Route::post('/register', [SatpenController::class, 'registerProses'])->name('register.proses');
Route::get('/register/success', [AuthController::class, 'registerSuccess'])->name('register.success');
Route::get('/ceknpsn', [AuthController::class, 'cekNpsnPage'])->name('ceknpsn');
Route::post('/ceknpsn', [AuthController::class, 'checkNpsn'])->name('ceknpsn.proses');
Route::get('/npsnvirtual', [AuthController::class, 'npsnVirtualPage'])->name('npsnvirtual');
Route::post('/npsnvirtual', [AuthController::class, 'requestVirtualNPSN'])->name('npsnvirtual.request');
Route::get('/json/provcount', [ApiController::class, 'getProvAndCount'])->name('provcount');
Route::get('/s/{code}', [ShortUrlController::class, 'redirectShort'])->name('shorturl.redirect');

Route::middleware('mustlogin')->group(function () {

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/gantipassword', [AuthController::class, 'changePassword'])->name('changepass');
    Route::get('/upload/{fileName?}', [FileViewerController::class, 'pdfUploadViewer'])->name('viewerpdf');

    // ================================================================
    // BRANGKAS DIGITAL - Routes
    // ================================================================

    // Record of Handover
    Route::get('/handover', [HandoverController::class, 'index'])->name('handover.index');
    Route::post('/handover', [HandoverController::class, 'store'])->name('handover.store');
    Route::get('/handover/get-items', [HandoverController::class, 'getItemsByKategori'])->name('handover.get-items');
    Route::get('/handover/items', [HandoverController::class, 'getItemsByKategori'])->name('handover.items');

    // Surat Surat Berharga
    Route::get('/brangkas/surat-tanah', [BrangkasController::class, 'suratTanah'])->name('brangkas.surat-tanah');
    Route::get('/brangkas/surat-tanah/tambah', [BrangkasController::class, 'createSuratTanah'])->name('brangkas.surat-tanah.create');
    Route::post('/brangkas/surat-tanah', [BrangkasController::class, 'storeSuratTanah'])->name('brangkas.surat-tanah.store');
    Route::get('/brangkas/surat-tanah/{id}/edit', [BrangkasController::class, 'editSuratTanah'])->name('brangkas.surat-tanah.edit');
    Route::post('/brangkas/surat-tanah/{id}/update', [BrangkasController::class, 'updateSuratTanah'])->name('brangkas.surat-tanah.update');
    Route::post('/brangkas/surat-tanah/{id}/delete', [BrangkasController::class, 'destroySuratTanah'])->name('brangkas.surat-tanah.destroy');

    Route::get('/brangkas/akta-notaris', [BrangkasController::class, 'aktaNotaris'])->name('brangkas.akta-notaris');
    Route::get('/brangkas/akta-notaris/tambah', [BrangkasController::class, 'createAktaNotaris'])->name('brangkas.akta-notaris.create');
    Route::post('/brangkas/akta-notaris', [BrangkasController::class, 'storeAktaNotaris'])->name('brangkas.akta-notaris.store');
    Route::get('/brangkas/akta-notaris/{id}/edit', [BrangkasController::class, 'editAktaNotaris'])->name('brangkas.akta-notaris.edit');
    Route::post('/brangkas/akta-notaris/{id}/update', [BrangkasController::class, 'updateAktaNotaris'])->name('brangkas.akta-notaris.update');
    Route::post('/brangkas/akta-notaris/{id}/delete', [BrangkasController::class, 'destroyAktaNotaris'])->name('brangkas.akta-notaris.destroy');

    // Data Aset Lembaga
    Route::get('/brangkas/data-aset', [BrangkasController::class, 'dataAset'])->name('brangkas.data-aset');
    Route::get('/brangkas/data-aset/tambah', [BrangkasController::class, 'createDataAset'])->name('brangkas.data-aset.create');
    Route::post('/brangkas/data-aset', [BrangkasController::class, 'storeDataAset'])->name('brangkas.data-aset.store');
    Route::get('/brangkas/data-aset/{id}/edit', [BrangkasController::class, 'editDataAset'])->name('brangkas.data-aset.edit');
    Route::post('/brangkas/data-aset/{id}/update', [BrangkasController::class, 'updateDataAset'])->name('brangkas.data-aset.update');
    Route::post('/brangkas/data-aset/{id}/delete', [BrangkasController::class, 'destroyDataAset'])->name('brangkas.data-aset.destroy');

    // Keuangan
    Route::get('/keuangan/pengajuan', [KeuanganController::class, 'pengajuan'])->name('keuangan.pengajuan');
    Route::post('/keuangan/pengajuan', [KeuanganController::class, 'storePengajuan'])->name('keuangan.pengajuan.store');
    Route::get('/keuangan/rekening-koran', [KeuanganController::class, 'rekeningKoran'])->name('keuangan.rekening-koran');
    Route::get('/keuangan/buku-bank', [KeuanganController::class, 'bukuBank'])->name('keuangan.buku-bank');
    Route::get('/keuangan/buku-kas-tunai', [KeuanganController::class, 'bukuKasTunai'])->name('keuangan.buku-kas-tunai');
    Route::get('/keuangan/buku-kas-umum', [KeuanganController::class, 'bukuKasUmum'])->name('keuangan.buku-kas-umum');
    Route::get('/keuangan/rekap-bulanan', [KeuanganController::class, 'rekapBulanan'])->name('keuangan.rekap-bulanan');
    Route::get('/keuangan/rekap-tahunan', [KeuanganController::class, 'rekapTahunan'])->name('keuangan.rekap-tahunan');
    Route::post('/keuangan/dokumen', [KeuanganController::class, 'storeDokumen'])->name('keuangan.dokumen.store');

    // Setting - Users
    Route::get('/setting/users', [SettingUserController::class, 'index'])->name('setting.users');
    Route::post('/setting/users/{id}/toggle', [SettingUserController::class, 'toggle'])->name('setting.users.toggle');

    // ================================================================
    // END BRANGKAS DIGITAL
    // ================================================================


    Route::group(["prefix" => "coretax", "middleware" => "verifysatpenactive"], function () {
        Route::get('/forbidden', [CoretaxController::class, 'forbidden'])->name('coretax.403')->withoutMiddleware('verifysatpenactive');
        Route::get('/', [CoretaxController::class, 'index'])->name('coretax');
        Route::get('/new', [CoretaxController::class, 'new'])->name('coretax.new');
        Route::get('/req-expiry', [CoretaxController::class, 'openExpiry'])->name('coretax.req-exipry');
        Route::get('/history', [CoretaxController::class, 'history'])->name('coretax.history');
        Route::put('/{coretax}', [CoretaxController::class, 'stored'])->name('coretax.save');
        Route::get('/file/{fileName?}', [FileViewerController::class, 'viewNpwpLama'])->name('coretax.file');
    });

    Route::middleware('onlyoperator')->group(function () {

        Route::get('/dashboard', function () {
            return redirect()->route('handover.index');
        })->name('dashboard');
        /**
         * Satpen
         */
        Route::group(["prefix" => "satpen"], function () {
            Route::get('/', [SatpenController::class, 'mySatpenPage'])->name('mysatpen');
            Route::get('/edit', [SatpenController::class, 'editSatpenPage'])->name('mysatpen.revisi');
            Route::get('/perpanjang', [SatpenController::class, 'perpanjangSatpenPage'])->name('mysatpen.perpanjang');
            Route::put('/edit', [SatpenController::class, 'revisionProses'])->name('mysatpen.revisi');
            Route::put('/change/npsn/{satpen}', [SatpenController::class, 'changeNPSN'])->name('mysatpen.npsn');
            Route::put('/perpanjang', [SatpenController::class, 'revisionProses'])->name('mysatpen.perpanjang');
            Route::get('/download/{document}', [SatpenController::class, 'downloadDocument'])->name('download');

            Route::get('/pdptk', [SatpenController::class, 'indexPDPTK'])->name('pdptk');
            Route::get('/pdptk/edit', [SatpenController::class, 'indexPDPTK'])->name('pdptk.edit');
            Route::get('/pdptk/dapo/{npsn}', [SatpenController::class, 'hitDapo'])->name('pdptk.dapo');
            Route::put('/pdptk', [SatpenController::class, 'modifPDPTK'])->name('pdptk.save');
            Route::get('/pdptk/sync/{satpen}', [SATPENControllerAdmin::class, 'processSyncPDPTK'])->name('pdptk.sync');

            Route::get('/other', [SatpenController::class, 'indexOther'])->name('other');
            Route::get('/other/edit', [SatpenController::class, 'indexOther'])->name('other.edit');
            Route::get('/other/dapo/{npsn}', [SatpenController::class, 'hitReferensi'])->name('other.referensi');
            Route::put('/other', [SatpenController::class, 'modifOther'])->name('other.save');
            Route::get('/other/sync/{satpen}', [SATPENControllerAdmin::class, 'processSyncOthers'])->name('other.sync');
        });

        /**
         * NPYP User
         */
        Route::group(["prefix" => "npyp"], function () {
            Route::get('/', [NPYPUserController::class, 'index'])->name('npyp.index');

            // PTK Routes
            Route::get('/ptk', [PTKController::class, 'index'])->name('ptk.index');
            Route::get('/ptk/data', [PTKController::class, 'getPTKData'])->name('ptk.data');
            Route::get('/ptk/status-counts', [PTKController::class, 'getStatusCounts'])->name('ptk.status-counts');
            Route::post('/ptk', [PTKController::class, 'store'])->name('ptk.store');
            Route::get('/ptk/{id}', [PTKController::class, 'show'])->name('ptk.show');
            Route::put('/ptk/{id}', [PTKController::class, 'update'])->name('ptk.update');
            Route::post('/ptk/{id}/revisi', [PTKController::class, 'submitRevisi'])->name('ptk.submit-revisi');
            Route::delete('/ptk/{id}', [PTKController::class, 'destroy'])->name('ptk.destroy');
            Route::get('/file/{path?}/{fileName?}', [FileViewerController::class, 'viewSkPtk'])->name('ptk.file');
        });
        /**
         * OSS
         */
        Route::group(["prefix" => "oss", "middleware" => "verifysatpenactive"], function () {
            Route::get('/forbidden', [OSSController::class, 'forbiddenPage'])->name('oss.403')->withoutMiddleware('verifysatpenactive');
            Route::get('/', [OSSController::class, 'landOSSRequest'])->name('oss');
            Route::get('/new', [OSSController::class, 'newOSSRequest'])->name('oss.new');
            Route::get('/detail/{ossId}', [OSSController::class, 'detailOSSQuesioner'])->name('oss.detail');
            Route::put('/{oss}', [OSSController::class, 'storedOSSRequest'])->name('oss.save');
            Route::get('/history', [OSSController::class, 'historyOSSRequest'])->name('oss.history');
            Route::get('/file/{path?}/{fileName?}', [FileViewerController::class, 'viewOSSDoc'])->name('oss.file');
        });
        /**
         * BHPNU
         */
        Route::group(["prefix" => "bhpnu", "middleware" => "verifysatpenactive"], function () {
            Route::get('/forbidden', [BHPNUController::class, 'forbiddenPage'])->name('bhpnu.403')->withoutMiddleware('verifysatpenactive');
            Route::get('/', [BHPNUController::class, 'permohonanBHPNUPage'])->name('bhpnu');
            Route::get('/new', [BHPNUController::class, 'permohonanBaruBHPNU'])->name('bhpnu.new');
            Route::put('/{bhpnu}', [BHPNUController::class, 'storePermohonanBHPNU'])->name('bhpnu.save');
            Route::get('/history', [BHPNUController::class, 'historyPermohonan'])->name('bhpnu.history');
            Route::get('/file/{fileName?}', [FileViewerController::class, 'viewBuktiPembayaran'])->name('bhpnu.file');
        });
        Route::group(["prefix" => "bantuan"], function () {
            Route::get('/', [SatpenController::class, 'underConstruction'])->name('bantuan');
        });

        Route::group(["prefix" => "beasiswa"], function () {
            Route::get('/', [SatpenController::class, 'underConstruction'])->name('beasiswa');
        });

        Route::group(["prefix" => "katalog"], function () {
            Route::get('/', [SatpenController::class, 'underConstruction'])->name('katalog');
        });
        /**
         * API
         */
        Route::group(["prefix" => "api"], function () {
            Route::get('/checknpsn/{npsn}', [ApiController::class, 'checkNPSNtoReferensiData'])->name('api.checknpsn');
        });
    });

    Route::middleware('onlyadmin')->prefix('a')->group(function () {
        /**
         * Dashboard
         */
        Route::get('/', function () {
            return redirect()->route('handover.index');
        })->name('a.dash');
        Route::get('/dashboard', function () {
            return redirect()->route('handover.index');
        });
        Route::prefix('org')->group(function () {
            Route::get('/profile', [ProfileORGController::class, 'index'])->name('profile');
            Route::post('/profile', [ProfileORGController::class, 'storeOrUpdate'])->name('profile.save');
        });
        Route::get('/settings', [Settings::class, 'pageSetting'])->name('a.setting');
        Route::put('/settings', [Settings::class, 'saveSetting'])->name('a.setting.save');
        Route::get('/log-activity', [Settings::class, 'viewLogActivity'])->name('a.logactivity');
        Route::resource('/shorturl', ShortUrlController::class)->middleware('superadmin')->except(['create', 'edit', 'show']);

        Route::middleware('primaryadmin')->group(function () {
            /**
             * Master
             */
            Route::group(["prefix" => "/master", "middleware" => "superadmin"], function () {
                Route::resource('/informasi', InformasiController::class);
                Route::resource('/propinsi', PropinsiController::class);
                Route::resource('/kabupaten', KabupatenController::class);
                Route::resource('/cabang', PengurusCabangController::class);
                Route::resource('/jenjang', JenjangPendidikanController::class);
                Route::resource('/users', UsersController::class);
                Route::resource('/tapel', TahunPelajaranController::class);
                Route::group(["prefix" => "/dapo"], function () {
                    Route::get("/", [DapoController::class, 'index'])->name('dapo.index');
                    Route::delete("/{npsn}", [DapoController::class, 'destroy'])->name('dapo.delete');
                    Route::post("/", [DapoController::class, 'store'])->name('dapo.save');
                });
                Route::group(["prefix" => "operators"], function () {
                    Route::get("/", [UsersController::class, 'users'])->name('users.satpen');
                    Route::get("{user}/reset", [UsersController::class, 'reset'])->name('users.reset');
                    Route::get("{user}/block", [UsersController::class, 'block'])->name('users.block');
                    Route::get("{user}/unblock", [UsersController::class, 'unblock'])->name('users.unblock');
                });
            });

            /**
             * Satpen
             */
            Route::group(["prefix" => "satpen"], function () {
                Route::get('/', [SATPENControllerAdmin::class, 'permohonanRegisterSatpen'])->name('a.satpen');
                Route::put('/{satpen}/status', [SATPENControllerAdmin::class, 'updateSatpenStatus'])->name('a.satpen.changestatus');
                Route::get('/rekap', [SATPENControllerAdmin::class, 'getAllSatpenOrFilter'])->name('a.rekapsatpen')->withoutMiddleware('primaryadmin');
                Route::get('/{satpenId}/detail', [SATPENControllerAdmin::class, 'getSatpenById'])->name('a.rekapsatpen.detail')->withoutMiddleware('primaryadmin');
                Route::delete('/{satpen}', [SATPENControllerAdmin::class, 'destroySatpen'])->name('a.rekapsatpen.destroy')->middleware('superadmin');
                Route::post('/doc/generate', [SATPENControllerAdmin::class, 'generatePiagamAndSK'])->name('generate.document');
                Route::post('/doc/regenerate', [SATPENControllerAdmin::class, 'reGeneratePiagamAndSK'])->name('regenerate.document');
                Route::get('/email/{satpen}', [SATPENControllerAdmin::class, 'sendNotifEmail'])->name('email.notif');
                Route::get('/reader/{type?}/{fileName?}', [FileViewerController::class, 'pdfGeneratedViewer'])->name('pdf.generated')->withoutMiddleware('primaryadmin');
                Route::get('/export_excel', [ExportExcelController::class, 'exportSatpentoExcel'])->name('satpen.excel')->withoutMiddleware('primaryadmin');

                Route::get('/pdptk', [SATPENControllerAdmin::class, 'getAllPDPTKOrFilter'])->name('a.pdptk')->withoutMiddleware('primaryadmin');;
                Route::get('/pdptk/sync', [SATPENControllerAdmin::class, 'processBulkSyncPDPTK'])->name('a.pdptk.sync');
                Route::get('/pdptk/sync/{satpen}', [SATPENControllerAdmin::class, 'processSyncPDPTK'])->name('a.pdptk.syncid');
                Route::get('/pdptk/export_excel', [ExportExcelController::class, 'exportPDPTKtoExcel'])->name('pdptk.excel')->withoutMiddleware('primaryadmin');

                Route::get('/other', [SATPENControllerAdmin::class, 'getAllOtherDataOrFilter'])->name('a.other')->withoutMiddleware('primaryadmin');;
                Route::get('/other/sync', [SATPENControllerAdmin::class, 'processBulkSyncOthers'])->name('a.other.sync');
                Route::get('/other/sync/{satpen}', [SATPENControllerAdmin::class, 'processSyncOthers'])->name('a.other.syncid');
                Route::get('/other/export_excel', [ExportExcelController::class, 'exportOthersDatatoExcel'])->name('other.excel')->withoutMiddleware('primaryadmin');

                Route::get('/layanan/{userId}', [SATPENControllerAdmin::class, 'showHistoryLayanan'])->name('a.satpen.history')->withoutMiddleware('primaryadmin');
            });
            /**
             * Virtual NPSN
             */
            Route::group(["prefix" => "virtualnpsn"], function () {
                Route::get('/', [VirtualNPSNController::class, 'listPermohonanVNPSN'])->name('a.vnpsn');
                Route::delete('/{virtualNPSN}', [VirtualNPSNController::class, 'destroyVNPSN'])->name('a.vnpsn.destroy')->middleware('superadmin');
                Route::delete('/reject/{virtualNPSN}', [VirtualNPSNController::class, 'rejectPermohonanVNPSN'])->name('a.vnpsn.reject');
                Route::put('/{virtualNPSN}', [VirtualNPSNController::class, 'generateVirtualNumber'])->name('a.vnpsn.accept');
            });
            /**
             * OSS
             */
            Route::group(["prefix" => "oss"], function () {
                Route::get('/', [OSSControllerAdmin::class, 'listPermohonanOSS'])->name('a.oss')->withoutMiddleware('primaryadmin');
                Route::get('/detail/{ossId}', [OSSControllerAdmin::class, 'detailOSSQuesioner'])->name('a.oss.detail');
                Route::put('/acc/{oss}', [OSSControllerAdmin::class, 'setAcceptOSS'])->name('a.oss.acc');
                Route::put('/appear/{oss}', [OSSControllerAdmin::class, 'setIzinTerbitOSS'])->name('a.oss.appear');
                Route::put('/reject/{oss}', [OSSControllerAdmin::class, 'setRejectOSS'])->name('a.oss.reject');
                Route::delete('/destroy/{oss}', [OSSControllerAdmin::class, 'destroyOSS'])->name('a.oss.destroy')->middleware('superadmin');
                Route::get('/file/{path}/{fileName?}', [FileViewerController::class, 'viewOSSDoc'])->name('a.oss.file')->withoutMiddleware('primaryadmin');
            });

            /**
             * BHPNU
             */
            Route::group(["prefix" => "bhpnu"], function () {
                Route::get('/', [BHPNUControllerAdmin::class, 'listPermohonanBHPNU'])->name('a.bhpnu')->withoutMiddleware('primaryadmin');
                Route::get('/acc/{bhpnu}', [BHPNUControllerAdmin::class, 'setAcceptBHPNU'])->name('a.bhpnu.acc');
                Route::put('/appear/{bhpnu}', [BHPNUControllerAdmin::class, 'setIzinTerbitBHPNU'])->name('a.bhpnu.appear');
                Route::put('/reject/{bhpnu}', [BHPNUControllerAdmin::class, 'setRejectBHPNU'])->name('a.bhpnu.reject');
                Route::delete('/destroy/{bhpnu}', [BHPNUControllerAdmin::class, 'destroyBHPNU'])->name('a.bhpnu.destroy')->middleware('superadmin');
                Route::get('/file/{fileName?}', [FileViewerController::class, 'viewBuktiPembayaran'])->name('a.bhpnu.file')->withoutMiddleware('primaryadmin');
            });
            
            /**
             * CORETAX
            */
            Route::group(["prefix" => "coretax"], function () {
                Route::get('/', [CoretaxAdminController::class, 'index'])->name('a.coretax')->withoutMiddleware('primaryadmin');
                Route::get('/{coretaxId}', [CoretaxAdminController::class, 'getById'])->name('a.coretax.byid');
                Route::get('/acc/{coretax}', [CoretaxAdminController::class, 'accepted'])->name('a.coretax.acc');
                Route::get('/open-expiry/{coretax}', [CoretaxAdminController::class, 'openExpiry'])->name('a.coretax.open-expiry');
                Route::put('/appear/{coretax}', [CoretaxAdminController::class, 'appeared'])->name('a.coretax.appear');
                Route::put('/reject/{coretax}', [CoretaxAdminController::class, 'rejected'])->name('a.coretax.reject');
                Route::delete('/destroy/{coretax}', [CoretaxAdminController::class, 'destroy'])->name('a.coretax.destroy')->middleware('superadmin');
                Route::get('/file/{fileName?}', [FileViewerController::class, 'viewNpwpLama'])->name('a.coretax.file')->withoutMiddleware('primaryadmin');
            });

            /**
             * PROFILE
             */
            Route::group(["prefix" => "profile"], function () {
                //export
                Route::get('/wilayah/export_excel', [ExportExcelController::class, 'exportWilayahtoExcel'])->name('wilayah.excel')->withoutMiddleware('primaryadmin');
                Route::get('/cabang/export_excel', [ExportExcelController::class, 'exportCabangtoExcel'])->name('cabang.excel')->withoutMiddleware('primaryadmin');

                Route::get('/wilayah', [ProfileController::class, 'profileWilayah'])->name('a.wilayah');
                Route::get('/wilayah/{ID}', [ProfileController::class, 'profileDetail'])->name('a.wilayah.detail');
                Route::delete('/wilayah', [ProfileController::class, 'destroyWilayah'])->name('a.wilayah.destroy');
                Route::get('/cabang', [ProfileController::class, 'profileCabang'])->name('a.cabang')->withoutMiddleware('primaryadmin');
                Route::get('/cabang/{ID}', [ProfileController::class, 'profileDetail'])->name('a.cabang.detail')->withoutMiddleware('primaryadmin');
                Route::delete('/cabang', [ProfileController::class, 'destroyCabang'])->name('a.cabang.destroy');
            });

            Route::group(["prefix" => "npyp"], function () {
                Route::get('/', [NpypController::class, 'indexNpyp'])->name('a.npyp');
                Route::withoutMiddleware('primaryadmin')->group(function () {
                    Route::post('/', [NpypController::class, 'store'])->name('a.npyp.store');
                    Route::put('/{id}', [NpypController::class, 'update'])->name('a.npyp.update');
                    Route::get('/satpen-list', [NpypController::class, 'getSatpenList'])->name('a.npyp.satpen-list')->withoutMiddleware('primaryadmin');
                    Route::get('/sekolah-naungan-data', [NpypController::class, 'getSekolahNaunganData'])->name('a.npyp.sekolah-naungan-data');
                    Route::post('/add-sekolah-naungan', [NpypController::class, 'addSekolahNaungan'])->name('a.npyp.add-sekolah-naungan');
                    Route::delete('/sekolah-naungan/{id}', [NpypController::class, 'deleteSekolahNaungan'])->name('a.npyp.delete-sekolah-naungan');
                    Route::get('/wilayah', [NpypController::class, 'indexNpypWilayah'])->name('a.npyp.wilayah');
                    Route::get('/cabang', [NpypController::class, 'indexNpypCabang'])->name('a.npyp.cabang');
                    Route::get('/cabang/data', [NpypController::class, 'getNpypCabangData'])->name('a.npyp.cabang.data');
                    Route::get('/satpen/{npypId}', [NpypController::class, 'getSatpenByNpyp'])->name('a.npyp.satpen-detail');
                    
                    // Admin PTK Verification Routes
                    Route::prefix('ptk')->group(function () {
                        Route::get('/verifikasi', [AdminPTKController::class, 'index'])->name('admin.ptk.verifikasi');
                        Route::get('/data', [AdminPTKController::class, 'getData'])->name('admin.ptk.data');
                        Route::get('/statistics', [AdminPTKController::class, 'statistics'])->name('admin.ptk.statistics');
                        Route::get('/{id}/detail', [AdminPTKController::class, 'detail'])->name('admin.ptk.detail');
                        Route::post('/action', [AdminPTKController::class, 'action'])->name('admin.ptk.action');
                    });
                });
                Route::get('/wilayah/data', [NpypController::class, 'getNpypWilayahData'])->name('a.npyp.wilayah.data');
                Route::get('/rekap-ptk', [NpypController::class, 'rekapPtkNasional'])->name('a.npyp.rekap-ptk');
                Route::get('/rekap-ptk/{id}/detail', [NpypController::class, 'getPtkDetail'])->name('a.npyp.ptk-detail');
                Route::get('/file/{path?}/{fileName?}', [FileViewerController::class, 'viewSkPtk'])->name('ptk.file')->withoutMiddleware('primaryadmin');
            });

            Route::group(["prefix" => "bantuan"], function () {
                Route::get('/', [SATPENControllerAdmin::class, 'underConstruction'])->name('a.bantuan');
            });

            Route::group(["prefix" => "beasiswa"], function () {
                Route::get('/', [SATPENControllerAdmin::class, 'underConstruction'])->name('a.beasiswa');
            });

            Route::group(["prefix" => "katalog"], function () {
                Route::get('/', [SATPENControllerAdmin::class, 'underConstruction'])->name('a.katalog');
            });
        });
    });
    /**
     * API Json
     */
    Route::prefix('api')->middleware('onlyadmin')->group(function () {
        Route::get('/provcount', [ApiController::class, 'getProvAndCount'])->name('api.provcount');
        Route::get('/satpen/search', [ApiController::class, 'searchSatpen'])->name('api.searchsatpen')->withoutMiddleware(["onlyadmin", "mustlogin"]);
        Route::get('/satpen/{satpenId}', [ApiController::class, 'getSatpenById'])->name('api.satpenbyid');
        Route::get('/kabupaten/{provId}', [ApiController::class, 'getKabupatenByProv'])->name('api.kabupatenbyprov')->withoutMiddleware(["onlyadmin", "mustlogin"]);
        Route::get('/pc/{provId}', [ApiController::class, 'getPCByProv'])->name('api.pcbyprov')->withoutMiddleware(["onlyadmin", "mustlogin"]);
        Route::get('/kabcount/{provId?}', [ApiController::class, 'getKabAndCount'])->name('api.kabcount');
        Route::get('/pccount', [ApiController::class, 'getPCAndCount'])->name('api.pccount');
        Route::get('/jenjangcount/{provId?}', [ApiController::class, 'getJenjangAndCount'])->name('api.jenjangcount');
        Route::get('/jenjangcount/{provId}/{cabangId}', [ApiController::class, 'getJenjangAndCountByCabang'])->name('api.jenjangcountbycabang');
        Route::get('/kabupaten', [ApiController::class, 'getKabupatenByProvinsi'])->name('api.kabupaten');
        
        // Dashboard API untuk Admin Cabang  
        Route::get('/ptkcount', [DashboardApiController::class, 'getPTKCount'])->name('api.ptk.count');
        Route::get('/pdcount', [DashboardApiController::class, 'getPDCount'])->name('api.pd.count');
    });
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
