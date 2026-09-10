<?php

namespace App\Http\Controllers;

use App\Exceptions\CatchErrorException;
use App\Helpers\MailService;
use App\Helpers\ReferensiKemdikbud;
use App\Http\Controllers\Admin\SATPENController as SatpenControllerAdmin;
use App\Http\Requests\CekNpsnRequest;
use App\Http\Requests\OtherRequest;
use App\Http\Requests\PDPTKRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RegisterUpdateRequest;
use App\Http\Requests\StatusSatpenRequest;
use App\Mail\RegisterMail;
use App\Models\FileRegister;
use App\Models\Jenjang;
use App\Models\Kabupaten;
use App\Models\Kategori;
use App\Models\Others;
use App\Models\PDPTK;
use App\Models\PengurusCabang;
use App\Models\Provinsi;
use App\Models\Satpen;
use App\Models\VirtualNPSN;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class SatpenController extends Controller
{
    public function dashboardPage()
    {
        try {
            $mySatpen = Satpen::with(['kategori', 'timeline', 'file'])
                ->where('id_user', '=', auth()->user()->id_user)
                ->first();
            $usingVNPSN = VirtualNPSN::where('nomor_virtual', '=', $mySatpen->npsn)->count('nomor_virtual');
            $pdptk = PDPTK::whereHas('satpen', function ($query) {
                            $query->where('id_user', '=', auth()->user()->id_user);
                        })->first();

            return view('home.dashboard', compact('mySatpen', 'usingVNPSN', 'pdptk'));
        } catch (\Exception $e) {
            throw new CatchErrorException("[DASHBOARD PAGE] has error " . $e);
        }
    }

    public function registerProses(RegisterRequest $request)
    {
        $registerNumber = "";
        try {
            $provinsi = Provinsi::find($request->propinsi);
            $cabang = PengurusCabang::find($request->cabang);
            $lastOfSatpen = Satpen::orderBy('id_satpen', 'desc')->first();

            if (!$provinsi) return redirect()->back()->with('error', 'Provinsi code not found');
            else if (!$cabang) return redirect()->back()->with('error', 'Cabang code not found');
            /**
             * Generate registration number
             */
            $orderedNumber = 0;
            if ($lastOfSatpen !== null) {
                $orderedNumber = (int) $lastOfSatpen->no_urut;
            }
            $orderedNumber = str_pad(++$orderedNumber, 4, '0', STR_PAD_LEFT);

            /**
             * Determined kategori of yayasan based on type yayasan and aset tanah
             */
            $makeCategorySatpen = SatpenController::makekategori(strtolower($request->yayasan), strtolower($request->aset_tanah));
            if ($makeCategorySatpen) {

                try {
                    /**
                     * When yayasan is not bhp nu append prefix A in generated number
                     * Generated number is combined of Kode Provinsi + Kode Kabupaten + 4 digit ordered number
                     */
                    $registerNumber .= $provinsi->kode_prov . $cabang->kode_kab . $orderedNumber;

                    return DB::transaction(function () use ($request, $registerNumber, $provinsi, $cabang, $makeCategorySatpen, $orderedNumber) {
                        /**
                         * Store files
                         */
                        if (
                            $request->file('file_permohonan')->isValid()
                            && $request->file('file_rekom_pc')->isValid()
                            && $request->file('file_rekom_pw')->isValid()
                        ) {
                            $pathFilePermohonan = Storage::disk('uploads')->putFile(null, $request->file('file_permohonan'));
                            $pathFileRekomPC = Storage::disk('uploads')->putFile(null, $request->file('file_rekom_pc'));
                            $pathFileRekomPW = Storage::disk('uploads')->putFile(null, $request->file('file_rekom_pw'));
                        }

                        /**
                         * Make account on db.users
                         */
                        $user = AuthController::register($registerNumber, $request->password);
                        /**
                         * Store satpen on db.satpen
                         */
                        try {
                            $satpen = Satpen::create([
                                'id_user' => $user->id_user,
                                'id_prov' => $provinsi->id_prov,
                                'id_kab' => $request->kabupaten,
                                'id_pc' => $cabang->id_pc,
                                'id_kategori' => $makeCategorySatpen->id_kategori,
                                'id_jenjang' => $request->jenjang,
                                'npsn' => $request->npsn,
                                'no_registrasi' => $registerNumber,
                                'no_urut' => $orderedNumber,
                                'nm_satpen' => $request->nm_satpen,
                                'yayasan' => strtolower($request->yayasan) <> "bhpnu" ? $request->nm_yayasan : $request->yayasan,
                                'kepsek' => $request->kepsek,
                                'telpon' => $request->telp,
                                'email' => $request->email,
                                'fax' => $request->fax,
                                'thn_berdiri' => $request->thn_berdiri,
                                'alamat' => $request->alamat,
                                'kelurahan' => $request->kelurahan,
                                'kecamatan' => $request->kecamatan,
                                'aset_tanah' => $request->aset_tanah,
                                'nm_pemilik' => $request->nm_pemilik,
                                'tgl_registrasi' => Date::now(),
                            ]);

                            FileRegister::insert([[
                                'id_satpen' => $satpen->id_satpen,
                                'mapfile' => 'surat_permohonan',
                                'nm_lembaga' => $satpen->nm_satpen,
                                'daerah' => '',
                                'nomor_surat' => $request->no_srt_permohonan,
                                'tgl_surat' => $request->tgl_srt_permohonan,
                                'filesurat' =>  $pathFilePermohonan,
                            ], [
                                'id_satpen' => $satpen->id_satpen,
                                'mapfile' => 'rekom_pc',
                                'daerah' => $request->cabang_rekom_pc,
                                'nm_lembaga' => $request->nm_rekom_pc,
                                'nomor_surat' => $request->no_srt_rekom_pc,
                                'tgl_surat' => $request->tgl_srt_rekom_pc,
                                'filesurat' =>  $pathFileRekomPC,
                            ], [
                                'id_satpen' => $satpen->id_satpen,
                                'mapfile' => 'rekom_pw',
                                'daerah' => $request->wilayah_rekom_pw,
                                'nm_lembaga' => $request->nm_rekom_pw,
                                'nomor_surat' => $request->no_srt_rekom_pw,
                                'tgl_surat' => $request->tgl_srt_rekom_pw,
                                'filesurat' =>  $pathFileRekomPW,
                            ]]);

                            /*
                            check the is a virtual npsn
                            */
                            VirtualNPSN::where('nomor_virtual', '=', $request->npsn)->update([
                                'actived_date' => Carbon::now(),
                            ]);

                            (new SatpenControllerAdmin())->updateSatpenStatus((new StatusSatpenRequest())
                                    ->merge([
                                        "status_verifikasi" => "permohonan",
                                        "keterangan" => "",
                                    ]),
                                $satpen
                            );
                        } catch (\Exception $e) {
                            throw new CatchErrorException("[REGISTER PROCESS INSERT] has error " . $e);
                        }

                        MailService::send([
                            "to" => $satpen->email,
                            "subject" => "Registrasi Berhasil",
                            "recipient" => $satpen->nm_satpen,
                            "content" => "<h2>Registrasi Berhasil</h2> <p>Berikut nomor registrasi satpen anda.</p>  <div class='button'> <a href='#' class='cta-button'>{$registerNumber}</a> </div> <p>Untuk dapat masuk pada portal, anda harus login menggunakan nomor registrasi tersebut. Simpan nomor registrasi diatas.</p>"
                        ]);

                        return redirect()->route('register.success')->with('regNumber', $registerNumber);
                    });
                } catch (\Exception $e) {
                    throw new CatchErrorException("[REGISTER PROCESS] has error " . $e);
                }
            }

            return redirect()->back()->with('error', 'cannot create satpen kategori');
        } catch (\Exception $e) {
            throw new CatchErrorException("[REGISTER PROCESS] has error " . $e);
        }
    }

    public function revisionProses(RegisterUpdateRequest $request)
    {
        $registerNumber = "";
        try {
            /**
             * Get satpen by satpen.userid
             */
            $provinsi = Provinsi::find($request->propinsi);
            $cabang = PengurusCabang::find($request->cabang);
            $satpen = Satpen::where('id_user', '=', auth()->user()->id_user)->first();
            /**
             * Validation when status must revisi
             */
            if (!$satpen) return redirect()->back()
                ->with('error', 'Forbidden to update satpen profile');
            /**
             * Validation when satpen id not releate with current user id
             */
            elseif ($satpen->status !== 'revisi' && $satpen->status !== 'expired') return redirect()->back()
                ->with('error', 'Satpen status is not revisi or expired');

            elseif (
                $satpen->filereg->isEmpty() &&
                (!$request->file('file_permohonan')
                    || !$request->file('file_rekom_pc')
                    || !$request->file('file_rekom_pw'))
            ) return redirect()->back()
                ->with('error', 'lengkapi semua formg unggahan dokumen');
            /**
             * Update registration number
             */
            $orderedNumber = $satpen->no_urut;
            if ($satpen->status == 'revisi') {
                $registerNumber .= $provinsi->kode_prov . $cabang->kode_kab . $orderedNumber;
            } else {
                $registerNumber .= $satpen->no_registrasi;
            }
            /**
             * Determine kategori
             */
            $makeCategorySatpen = SatpenController::makekategori(strtolower($request->yayasan), strtolower($request->aset_tanah));
            if ($makeCategorySatpen) {
                /**
                 * Replace and store files
                 */
                if (!$satpen->filereg->isEmpty()) {
                    if (
                        $request->file('file_permohonan')
                        && $request->file('file_permohonan')->isValid()
                    ) {
                        $pathFilePermohonan = Storage::disk('uploads')->putFile(null, $request->file('file_permohonan'));
                        Storage::disk("uploads")->delete($satpen->filereg[0]->filesurat);
                    } else {
                        $pathFilePermohonan = $satpen->filereg[0]->filesurat;
                    }
                    if (
                        $request->file('file_rekom_pc')
                        && $request->file('file_rekom_pc')->isValid()
                    ) {
                        $pathFileRekomPC = Storage::disk('uploads')->putFile(null, $request->file('file_rekom_pc'));
                        Storage::disk("uploads")->delete($satpen->filereg[1]->filesurat);
                    } else {
                        $pathFileRekomPC = $satpen->filereg[1]->filesurat;
                    }
                    if (
                        $request->file('file_rekom_pw')
                        && $request->file('file_rekom_pw')->isValid()
                    ) {
                        $pathFileRekomPW = Storage::disk('uploads')->putFile(null, $request->file('file_rekom_pw'));
                        Storage::disk("uploads")->delete($satpen->filereg[2]->filesurat);
                    } else {
                        $pathFileRekomPW = $satpen->filereg[2]->filesurat;
                    }
                }
                /**
                 * Update account db.users.username
                 */
                AuthController::updateUsername($registerNumber);
                /**
                 * Update satpen on db.satpen
                 */
                $address = [];
                if ($satpen->status == 'revisi') {
                    $address = [
                        'id_prov' => $provinsi->id_prov,
                        'id_kab' => $request->kabupaten,
                        'id_pc' => $cabang->id_pc,
                    ];
                }
                $satpen->update(array_merge($address, [
                    'id_kategori' => $makeCategorySatpen->id_kategori,
                    'id_jenjang' => $request->jenjang,
                    //                    'npsn' => $request->npsn,
                    'no_registrasi' => $registerNumber,
                    'nm_satpen' => $request->nm_satpen,
                    'yayasan' => strtolower($request->yayasan) <> "bhpnu" ? $request->nm_yayasan : $request->yayasan,
                    'kepsek' => $request->kepsek,
                    'telpon' => $request->telp,
                    'email' => $request->email,
                    'fax' => $request->fax,
                    'thn_berdiri' => $request->thn_berdiri,
                    'alamat' => $request->alamat,
                    'kelurahan' => $request->kelurahan,
                    'kecamatan' => $request->kecamatan,
                    'aset_tanah' => $request->aset_tanah,
                    'nm_pemilik' => $request->nm_pemilik,
                ]));
                /**
                 * Update file register
                 */
                if (!$satpen->filereg->isEmpty()) {
                    FileRegister::find($satpen->filereg[0]->id_file)->update([
                        'nm_lembaga' => $satpen->nm_satpen,
                        'nomor_surat' => $request->no_srt_permohonan,
                        'tgl_surat' => $request->tgl_srt_permohonan,
                        'filesurat' =>  $pathFilePermohonan,
                    ]);
                    FileRegister::find($satpen->filereg[1]->id_file)->update([
                        'daerah' => $request->cabang_rekom_pc,
                        'nm_lembaga' => $request->nm_rekom_pc,
                        'nomor_surat' => $request->no_srt_rekom_pc,
                        'tgl_surat' => $request->tgl_srt_rekom_pc,
                        'filesurat' =>  $pathFileRekomPC,
                    ]);
                    FileRegister::find($satpen->filereg[2]->id_file)->update([
                        'daerah' => $request->wilayah_rekom_pw,
                        'nm_lembaga' => $request->nm_rekom_pw,
                        'nomor_surat' => $request->no_srt_rekom_pw,
                        'tgl_surat' => $request->tgl_srt_rekom_pw,
                        'filesurat' =>  $pathFileRekomPW,
                    ]);
                } else {
                    /**
                     * Store files
                     */
                    if (
                        $request->file('file_permohonan')->isValid()
                        && $request->file('file_rekom_pc')->isValid()
                        && $request->file('file_rekom_pw')->isValid()
                    ) {
                        $pathFilePermohonan = Storage::disk('uploads')->putFile(null, $request->file('file_permohonan'));
                        $pathFileRekomPC = Storage::disk('uploads')->putFile(null, $request->file('file_rekom_pc'));
                        $pathFileRekomPW = Storage::disk('uploads')->putFile(null, $request->file('file_rekom_pw'));
                    }
                    /**
                     * Save files register
                     */
                    FileRegister::insert([[
                        'id_satpen' => $satpen->id_satpen,
                        'mapfile' => 'surat_permohonan',
                        'nm_lembaga' => $satpen->nm_satpen,
                        'daerah' => '',
                        'nomor_surat' => $request->no_srt_permohonan,
                        'tgl_surat' => $request->tgl_srt_permohonan,
                        'filesurat' =>  $pathFilePermohonan,
                    ], [
                        'id_satpen' => $satpen->id_satpen,
                        'mapfile' => 'rekom_pc',
                        'daerah' => $request->cabang_rekom_pc,
                        'nm_lembaga' => $request->nm_rekom_pc,
                        'nomor_surat' => $request->no_srt_rekom_pc,
                        'tgl_surat' => $request->tgl_srt_rekom_pc,
                        'filesurat' =>  $pathFileRekomPC,
                    ], [
                        'id_satpen' => $satpen->id_satpen,
                        'mapfile' => 'rekom_pw',
                        'daerah' => $request->wilayah_rekom_pw,
                        'nm_lembaga' => $request->nm_rekom_pw,
                        'nomor_surat' => $request->no_srt_rekom_pw,
                        'tgl_surat' => $request->tgl_srt_rekom_pw,
                        'filesurat' =>  $pathFileRekomPW,
                    ]]);
                }

                if ($satpen->status == "revisi") {
                    (new SatpenControllerAdmin())->updateSatpenStatus((new StatusSatpenRequest())
                            ->merge([
                                "status_verifikasi" => "permohonan",
                                "keterangan" => "permohonan setelah revisi",
                            ]),
                        $satpen
                    );
                } elseif ($satpen->status == "expired") {
                    (new SatpenControllerAdmin())->updateSatpenStatus((new StatusSatpenRequest())
                            ->merge([
                                "status_verifikasi" => "perpanjangan",
                                "keterangan" => "permohonan perpanjangan dokumen",
                            ]),
                        $satpen
                    );
                }

                return redirect()->route('mysatpen')->with('success', 'satpen berhasil di update');
            }

            throw new \Exception("cannot create satpen kategori");
        } catch (\Exception $e) {
            throw new CatchErrorException("[REVISION PROCESS] has error " . $e);
        }
    }

    public function downloadDocument($document)
    {
        try {
            $satpenData = Satpen::select('id_satpen')->with('file')
                ->where('id_user', auth()->user()->id_user)
                ->first();
            if ($satpenData->file) {
                if (
                    $document == 'piagam'
                    && $satpenData->file[0]->nm_file
                    && Storage::exists("generated/piagam/" . $satpenData->file[0]->nm_file)
                ) {
                    return response()->download(
                        storage_path("app/generated/piagam/" . $satpenData->file[0]->nm_file)
                    );
                } elseif (
                    $document == 'sk'
                    && $satpenData->file[1]->nm_file
                    && Storage::exists("generated/sk/" . $satpenData->file[1]->nm_file)
                ) {
                    return response()->download(
                        storage_path("app/generated/sk/" . $satpenData->file[1]->nm_file)
                    );
                } else return redirect()->back()->with('error', 'dokumen tidak ditemukan');
            }
            return redirect()->back()->with('error', 'Document belum selesai');
        } catch (\Exception $e) {
            throw new CatchErrorException("[DOWNLOAD DOCUMENT] has error " . $e);
        }
    }

    public function mySatpenPage()
    {
        try {
            $satpenProfile = Satpen::with(['kategori', 'provinsi', 'kabupaten', 'jenjang', 'timeline'])
                ->where('id_user', '=', auth()->user()->id_user)
                ->first();
            $usingVNPSN = VirtualNPSN::where('nomor_virtual', '=', $satpenProfile->npsn)->count('nomor_virtual');

            return view('satpen.satpen', compact('satpenProfile', 'usingVNPSN'));
        } catch (\Exception $e) {
            throw new CatchErrorException("[MYSATPEN PAGE] has error " . $e);
        }
    }
    public function editSatpenPage()
    {
        try {
            $satpenProfile = Satpen::where('id_user', '=', auth()->user()->id_user)->first();

            if ($satpenProfile->status != 'revisi') return redirect()->back()
                ->with('error', 'Status satpen bukan dalam masa revisi');

            $kabupaten = Kabupaten::where('id_prov', '=', $satpenProfile->id_prov)
                ->orderBy('id_kab')->get();
            $cabang = PengurusCabang::where('id_prov', '=', $satpenProfile->id_prov)
                ->orderBy('id_pc')->get();
            $propinsi = Provinsi::orderBy('id_prov')->get();
            $jenjang = Jenjang::orderBy('id_jenjang')->get();

            return view('satpen.revisi', compact('satpenProfile', 'jenjang', 'propinsi', 'kabupaten', 'cabang'));
        } catch (\Exception $e) {
            throw new CatchErrorException("[EDIT SATPEN PAGE] has error " . $e);
        }
    }
    public function perpanjangSatpenPage()
    {
        try {
            $satpenProfile = Satpen::where('id_user', '=', auth()->user()->id_user)->first();

            if ($satpenProfile->status != 'expired') return redirect()->back()
                ->with('error', 'Status dokumen satpen belum expired');

            $kabupaten = Kabupaten::where('id_prov', '=', $satpenProfile->id_prov)
                ->orderBy('id_kab')->get();
            $cabang = PengurusCabang::where('id_prov', '=', $satpenProfile->id_prov)
                ->orderBy('id_pc')->get();
            $propinsi = Provinsi::orderBy('id_prov')->get();
            $jenjang = Jenjang::orderBy('id_jenjang')->get();

            return view('satpen.perpanjang', compact('satpenProfile', 'jenjang', 'propinsi', 'kabupaten', 'cabang'));
        } catch (\Exception $e) {
            throw new CatchErrorException("[PERPANJANG SATPEN PAGE] has error " . $e);
        }
    }

    public function changeNPSN(CekNpsnRequest $request, Satpen $satpen)
    {
        try {
            if ($request->npsn == $satpen->npsn) {
                return redirect()->back()->with('error', 'Anda memasukkan npsn sama dengan yang saat ini');
            }
            /**
             * Cek npsn on referensi.data.kemdikbud.go.id/
             */
            $cloneSekolah = new ReferensiKemdikbud();
            $cloneSekolah->clone($request->npsn);

            if ($cloneSekolah->getStatus() && $cloneSekolah->getResult() !== null) {
                $jsonResultSekolah = $cloneSekolah->getResult();
                /**
                 * Cek npsn on system based on npsn number
                 */
                if (Satpen::where(['npsn' => $jsonResultSekolah["npsn"]])->first()) {
                    return redirect()->back()->with('error', 'NPSN sudah pernah terdaftar dalam sistem');
                }

                VirtualNPSN::where("nomor_virtual", "=", $satpen->npsn)->delete();
                $satpen->update([
                    'npsn' => $jsonResultSekolah["npsn"],
                    'nm_satpen' => $jsonResultSekolah["nama"],
                ]);
                if ($satpen->status == "expired") {
                    (new SatpenControllerAdmin())->updateSatpenStatus((new StatusSatpenRequest())
                            ->merge([
                                "status_verifikasi" => "perpanjangan",
                                "keterangan" => "memperbaharui npsn",
                            ]),
                        $satpen
                    );
                } elseif ($satpen->status == "revisi") {
                    (new SatpenControllerAdmin())->updateSatpenStatus((new StatusSatpenRequest())
                            ->merge([
                                "status_verifikasi" => "permohonan",
                                "keterangan" => "memperbaharui npsn",
                            ]),
                        $satpen
                    );
                }
                return redirect()->back()->with('success', 'Berhasil memperbaharui npsn satuan pendidikan. Data sedang ditinjau oleh Admin');
            }

            return redirect()->back()->with('error', $cloneSekolah->getResult());
        } catch (\Exception $e) {
            throw new CatchErrorException("[CHANGE NPSN] has error " . $e);
        }
    }


    private static function makekategori(string $yayasan, string $statusTanah): ?Kategori
    {
        $kategori = null;
        /**
         * When status yayasan is BHPNU and tanah milik nu (jam'iyah)
         */
        if ($yayasan == 'bhpnu' && $statusTanah == 'jamiyah') {
            $kategori = 'A';
        }
        /**
         * When status yayasan is BHPNU and tanah milik masyarakat nu
         */
        elseif ($yayasan == 'bhpnu' && $statusTanah <> 'jamiyah') {
            $kategori = 'B';
        }
        /**
         * When status yayasan is non BHPNU and tanah milik nu (jam'iyah)
         */
        elseif ($yayasan <> 'bhpnu' && $statusTanah == 'jamiyah') {
            $kategori = 'C';
        }
        /**
         * When status yayasan is non BHPNU and tanah milik masyarakat nu
         */
        elseif ($yayasan <> 'bhpnu' && $statusTanah <> 'jamiyah') {
            $kategori = 'D';
        }
        return Kategori::where('nm_kategori', '=', $kategori)->first();
    }

    public function indexPDPTK()
    {
        $tapel = Settings::get('current_tapel');
        $pdptk = PDPTK::with([
            'satpen:id_satpen,id_jenjang,id_prov,id_kab,no_registrasi,nm_satpen',
            'satpen.jenjang:id_jenjang,nm_jenjang',
            'satpen.provinsi:id_prov,nm_prov',
            'satpen.kabupaten:id_kab,nama_kab',
        ])
            ->where('tapel', '=', $tapel)
            ->whereHas('satpen', function ($query) {
                $query->where('id_user', '=', auth()->user()->id_user);
            })
            ->first();

        $satpen = Satpen::with([
            'jenjang:id_jenjang,nm_jenjang',
            'provinsi:id_prov,nm_prov',
            'kabupaten:id_kab,nama_kab',
        ])
            ->where('id_user', '=', auth()->user()->id_user)->first();

        return view('satpen.pdptk', compact('pdptk', 'satpen', 'tapel'));
    }

    public function modifPDPTK(PDPTKRequest $request)
    {
        try {
            PDPTK::updateOrInsert(
                ['id' => $request->pdptkId],
                [
                    'id_satpen' => $request->satpenId,
                    'tapel' => $request->tapel,
                    'pd_lk' => $request->pd_lk,
                    'pd_pr' => $request->pd_pr,
                    'jml_pd' => $request->jml_pd,
                    'guru_lk' => $request->guru_lk,
                    'guru_pr' => $request->guru_pr,
                    'jml_guru' => $request->jml_guru,
                    'tendik_lk' => $request->tendik_lk,
                    'tendik_pr' => $request->tendik_pr,
                    'jml_tendik' => $request->jml_tendik,
                    'last_sinkron' => Carbon::now(),
                    'status_sinkron' => 1,
                ]
            );
            return redirect()->route('pdptk')->with('success', 'Berhasil melengkapi data PDPTK');
        } catch (\Exception $e) {
            throw new CatchErrorException("[MODIF PDPTK] has error " . $e);
        }
    }

    public function hitDapo(string $npsn)
    {
        $tapel = Settings::get('current_tapel');
        $url = config('app.referensi_data_crawler') . "dapo-data/$tapel/$npsn";
        $response = Http::get($url);

        if ($response->failed()) {
            return response()->json($response->json(), HttpResponse::HTTP_BAD_REQUEST);
        }
        return $response->json();
    }

    public function indexOther()
    {
        $other = Others::with([
            'satpen:id_satpen,id_jenjang,id_prov,id_kab,no_registrasi,nm_satpen',
            'satpen.jenjang:id_jenjang,nm_jenjang',
            'satpen.provinsi:id_prov,nm_prov',
            'satpen.kabupaten:id_kab,nama_kab',
        ])
            ->whereHas('satpen', function ($query) {
                $query->where('id_user', '=', auth()->user()->id_user);
            })
            ->first();

        $satpen = Satpen::with([
            'jenjang:id_jenjang,nm_jenjang',
            'provinsi:id_prov,nm_prov',
            'kabupaten:id_kab,nama_kab',
        ])
            ->where('id_user', '=', auth()->user()->id_user)->first();

        return view('satpen.other', compact('other', 'satpen'));
    }

    public function modifOther(OtherRequest $request)
    {
        try {
            Others::updateOrInsert(
                ['id' => $request->otherId],
                [
                    'id_satpen'            => $request->satpenId,
                    'npyp'                 => $request->npyp,
                    'naungan'              => $request->naungan,
                    'no_sk_pendirian'      => $request->no_sk_pendirian,
                    'tgl_sk_pendirian'     => $request->tgl_sk_pendirian,
                    'no_sk_operasional'    => $request->no_sk_operasional,
                    'tgl_sk_operasional'   => $request->tgl_sk_operasional,
                    'link_sk_operational'  => $request->link_sk_operational,
                    'akreditasi'           => $request->akreditasi,
                    'website'              => $request->website,
                    'lingkungan_satpen'    => $request->lingkungan_satpen,
                    'last_sinkron' => Carbon::now(),
                    'status_sinkron' => 1,
                ]
            );
            return redirect()->route('other')->with('success', 'Berhasil melengkapi data Lainnya');
        } catch (\Exception $e) {
            throw new CatchErrorException("[MODIF OTHER] has error " . $e);
        }
    }

    public function hitReferensi(string $npsn)
    {
        $url = config('app.referensi_data_crawler') . "referensi-data/$npsn";
        $response = Http::get($url);

        if ($response->failed()) {
            return response()->json($response->json(), HttpResponse::HTTP_BAD_REQUEST);
        }
        return $response->json();
    }

    public function underConstruction()
    {
        return view('template.construction');
    }
}
