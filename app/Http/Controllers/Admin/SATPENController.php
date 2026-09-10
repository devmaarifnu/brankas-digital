<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CatchErrorException;
use App\Export\ExportDocument;
use App\Helpers\GenerateQr;
use App\Helpers\MailService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings;
use App\Http\Requests\StatusSatpenRequest;
use App\Models\BHPNU;
use App\Models\Coretax;
use App\Models\FileRegister;
use App\Models\FileUpload;
use App\Models\Jenjang;
use App\Models\Kabupaten;
use App\Models\Kategori;
use App\Models\OSS;
use App\Models\PDPTK;
use App\Models\Others;
use App\Models\PengurusCabang;
use App\Models\Provinsi;
use App\Models\Satpen;
use App\Models\TahunPelajaran;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SATPENController extends Controller
{
    public function dashboardPage()
    {

        try {
            $provFilter = auth()->user()->provId ? ["id_prov" => auth()->user()->provId] : null;
            $listProvinsi = Provinsi::where($provFilter)->get();
            $countOfRecordSatpen = Satpen::whereIn('status', ['setujui', 'expired', 'perpanjangan'])->where(request()->specificFilter)->count("id_satpen");

            $countOfPropinsi = $recordPerPropinsi = $countPerStatus = null;

            if (!in_array(auth()->user()->role, ["admin wilayah", "admin cabang"])) {
                $countOfKabupaten = Kabupaten::count("id_kab");
                $countOfPropinsi = Provinsi::count("id_prov");
                $recordPerPropinsi = DB::select("SELECT id_prov, nm_prov,
                                                        (SELECT COUNT(id_prov) FROM satpen WHERE id_prov=provinsi.id_prov) AS record_count
                                                         FROM provinsi");

                $countPerStatus = DB::select("SELECT (SELECT COUNT(id_satpen) FROM satpen WHERE status='permohonan') AS permohonan,
                                                                (SELECT COUNT(id_satpen) FROM satpen WHERE status='revisi') AS revisi,
                                                                (SELECT COUNT(id_satpen) FROM satpen WHERE status='proses dokumen') AS proses_dokumen,
                                                                (SELECT COUNT(id_satpen) FROM satpen WHERE status='expired') AS expired,
                                                                (SELECT COUNT(id_satpen) FROM satpen WHERE status='perpanjangan') AS perpanjangan ");
            } else {
                $countOfKabupaten = PengurusCabang::where(request()->specificFilter)->count("id_pc");
            }

            return view('admin.home.dashboard', compact(
                "listProvinsi",
                "countOfKabupaten",
                "countOfPropinsi",
                "countOfRecordSatpen",
                "recordPerPropinsi",
                "countPerStatus"
            ));
        } catch (\Exception $e) {
            throw new CatchErrorException("[DASHBOARD PAGE] has error " . $e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|void
     */

    public function getAllSatpenOrFilter(Request $request)
    {
        $paginatePerPage = Settings::get("count_perpage");
        $selectedColumns = ['id_satpen', 'id_kategori', 'id_kab', 'id_prov', 'id_jenjang', 'npsn', 'no_registrasi', 'nm_satpen', 'yayasan', 'thn_berdiri', 'status', 'tgl_registrasi', 'actived_date'];
        try {
            /**
             * If request without satpenid show all satpen where status 'setujui'
             */
            $statuses = ['setujui', 'expired', 'perpanjangan'];
            if (
                $request->jenjang
                || $request->kabupaten
                || $request->cabang
                || $request->provinsi
                || $request->kategori || $request->keyword || $request->status
            ) {

                $filter = [];
                $keywordFilter = [];
                if ($request->jenjang) $filter["id_jenjang"] = $request->jenjang;
                if ($request->kabupaten) $filter["id_kab"] = $request->kabupaten;
                if ($request->cabang) $filter["id_pc"] = $request->cabang;
                if ($request->provinsi) $filter["id_prov"] = $request->provinsi;
                if ($request->kategori) $filter["id_kategori"] = $request->kategori;
                if ($request->status) $statuses = [$request->status];
                if ($request->keyword) {
                    array_push($keywordFilter, ["nm_satpen", "like", "%" . $request->keyword . "%"]);
                    array_push($keywordFilter, ["npsn", "like", "%" . $request->keyword . "%"]);
                    array_push($keywordFilter, ["no_registrasi", "like", "%" . $request->keyword . "%"]);
                    array_push($keywordFilter, ["yayasan", "like", "%" . $request->keyword . "%"]);
                }

                if ($filter || $statuses) {
                    $satpenProfileQuery = Satpen::with([
                        'kategori:id_kategori,nm_kategori',
                        'provinsi:id_prov,nm_prov',
                        'kabupaten:id_kab,nama_kab',
                        'jenjang:id_jenjang,nm_jenjang',
                    ])
                        ->select($selectedColumns)
                        ->whereIn('status', $statuses)
                        ->where(request()->specificFilter)
                        ->where($filter)
                        ->where(function ($query) use ($keywordFilter) {
                            foreach ($keywordFilter as $condition) {
                                $query->orWhere(...$condition);
                            }
                        });
                    //                        ->get();

                    $satpenProfileCount = $satpenProfileQuery->count();
                    $satpenProfile = $satpenProfileQuery
                        ->paginate($paginatePerPage)->appends(request()->query());
                }
            } else {
                $satpenProfileQuery = Satpen::with([
                    'kategori:id_kategori,nm_kategori',
                    'provinsi:id_prov,nm_prov',
                    'kabupaten:id_kab,nama_kab',
                    'jenjang:id_jenjang,nm_jenjang',
                ])
                    ->select($selectedColumns)
                    ->whereIn('status', $statuses)
                    ->where(request()->specificFilter);
                //                    ->get();

                $satpenProfileCount = $satpenProfileQuery->count();
                $satpenProfile = $satpenProfileQuery
                    ->paginate($paginatePerPage)->appends(request()->query());
            }

            /**
             * If satpen profile null is user access satpen id not releate with user id
             */
            if (!$satpenProfile) return redirect()->back()->with('error', 'Forbidden to access satpen profile');

            $propinsi = Provinsi::all();
            $jenjang = Jenjang::all();
            $kategori = Kategori::all();

            return view('admin.satpen.rekapsatpen', compact(
                'satpenProfile',
                'satpenProfileCount',
                'propinsi',
                'jenjang',
                'kategori'
            ));
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET ALL SATPEN OR FILTER] has error " . $e);
        }
    }

    public function getSatpenById(string $satpenId = null)
    {
        try {
            if ($satpenId) {
                $satpenProfile = Satpen::with(['kategori', 'provinsi', 'kabupaten', 'jenjang', 'timeline', 'filereg'])
                    ->where('id_satpen', '=', $satpenId)
                    ->where(request()->specificFilter)
                    ->first();
                if (!$satpenProfile) return redirect()->back()->with('error', 'Forbidden to access satpen profile');

                return view('admin.satpen.detailSatpen', compact('satpenProfile'));
            }
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET SATPEN BY ID] has error " . $e);
        }
    }

    public function permohonanRegisterSatpen()
    {
        try {
            $selectedColumns = ['id_satpen', 'id_kategori', 'id_kab', 'id_prov', 'id_jenjang', 'npsn', 'no_registrasi', 'nm_satpen', 'yayasan', 'status'];
            $relationTable = [
                'kategori:id_kategori,nm_kategori',
                'provinsi:id_prov,nm_prov',
                'kabupaten:id_kab,nama_kab',
                'jenjang:id_jenjang,nm_jenjang',
            ];

            $permohonanSatpens = Satpen::with($relationTable)->where('status', '=', 'permohonan')->get($selectedColumns);
            $revisiSatpens = Satpen::with($relationTable)->where('status', '=', 'revisi')->get(array_merge($selectedColumns, ['kecamatan']));
            $prosesDocuments = Satpen::with($relationTable)->where('status', '=', 'proses dokumen')->get(array_merge($selectedColumns, ['kecamatan']));
            $perpanjanganDocuments = Satpen::with($relationTable)->where('status', '=', 'perpanjangan')->get(array_merge($selectedColumns, ['kecamatan']));

            return view('admin.satpen.registersatpen', compact('permohonanSatpens', 'revisiSatpens', 'prosesDocuments', 'perpanjanganDocuments'));
        } catch (\Exception $e) {
            throw new CatchErrorException("[PERMOHONAN REGISTER SATPEN] has error " . $e);
        }
    }

    public function updateSatpenStatus(StatusSatpenRequest $request, Satpen $satpen)
    {
        try {
            if ($satpen->status == $request->status_verifikasi) return redirect()->back()->with('error', 'status satpen sudah sudah sesuai');

            $satpen->update([
                'status' => $request->status_verifikasi
            ]);

            Timeline::create([
                'id_satpen' => $satpen->id_satpen,
                'status_verifikasi' => $request->status_verifikasi,
                'tgl_status' => Date::now(),
                'keterangan' => $request->keterangan,
            ]);

            $status_verifikasi = strtoupper($request->status_verifikasi);
            MailService::send([
                "to" => $satpen->email,
                "subject" => "Status Satpen",
                "recipient" => $satpen->nm_satpen,
                "content" => "<p>Saat ini, status satuan pendidikan anda telah menjadi.</p> <h3><strong>{$status_verifikasi}</strong></h3>"
            ]);

            return redirect()->back()->with('success', 'Status satpen telah diupdate menjadi ' . $request->status_verifikasi);
        } catch (\Exception $e) {
            throw new CatchErrorException("[UPDATE SATPEN STATUS] has error " . $e);
        }
    }

    public function generatePiagamAndSK(Request $request)
    {
        try {
            $piagamFilename = Settings::get("prefix_piagam_name");
            $skFilename = Settings::get("prefix_sk_name");
            /**
             * get selected satpen
             */
            $satpen = Satpen::find($request->satpenid);

            if ($satpen) {
                /**
                 * create file data in db.file_upload
                 */
                $replacedSatpenName = str_replace("/", "-", $satpen->nm_satpen);
                $piagamFilename .= $satpen->no_registrasi . "-" . $replacedSatpenName . ".pdf";
                //                $piagamFilename .= $satpen->nm_satpen.".docx";
                $skFilename .= $satpen->no_registrasi . "-" . $replacedSatpenName . ".pdf";
                //                $skFilename .= $satpen->nm_satpen.".docx";
                //create piagam
                FileUpload::create([
                    'id_satpen' => $satpen->id_satpen,
                    'typefile' => "piagam",
                    'qrcode' => GenerateQr::encodeQr(),
                    'nm_file' => $piagamFilename,
                    'tgl_file' => $request->tgl_doc,
                ]);
                FileUpload::create([
                    'id_satpen' => $satpen->id_satpen,
                    'typefile' => "sk",
                    'qrcode' => GenerateQr::encodeQr(),
                    'nm_file' => $skFilename,
                    'tgl_file' => $request->tgl_doc,
                ]);

                /**
                 * get relation data
                 */
                $satpenProfile = Satpen::with(['kategori', 'provinsi', 'kabupaten', 'jenjang', 'filereg', 'file'])
                    ->where('id_satpen', '=', $satpen->id_satpen)
                    ->first();

                /**
                 * Create pdf document and save in server
                 */
                ExportDocument::makePiagamDokumen($satpenProfile);
                ExportDocument::makeSKDokumen($satpenProfile);

                /**
                 * update status satpen menjadi disetujui
                 */
                $satpen->update([
                    'actived_date' => Date::now(),
                ]);

                $this->updateSatpenStatus((new StatusSatpenRequest())
                        ->merge(["status_verifikasi" => "setujui"]),
                    $satpen
                );

                return redirect()->back()->with('success', 'Dokumen Surat Keputusan dan Piagam telah generate');
            }

            return redirect()->back()->with('error', 'satpen tidak ditemukan!');
        } catch (\Exception $e) {
            throw new CatchErrorException("[GENERATE PIAGAM AND SK] has error " . $e);
        }
    }

    public function reGeneratePiagamAndSK(Request $request)
    {
        try {
            $piagamFilename = Settings::get("prefix_piagam_name");
            $skFilename = Settings::get("prefix_sk_name");
            /**
             * get selected satpen
             */
            $satpen = Satpen::find($request->satpenid);

            if ($satpen) {
                /**
                 * create file data in db.file_upload
                 */
                $replacedSatpenName = str_replace("/", "-", $satpen->nm_satpen);
                $piagamFilename .= $satpen->no_registrasi . "-" . $replacedSatpenName . ".pdf";
                //                $piagamFilename .= $satpen->nm_satpen.".docx";
                $skFilename .= $satpen->no_registrasi . "-" . $replacedSatpenName . ".pdf";
                //                $skFilename .= $satpen->nm_satpen.".docx";
                //create piagam
                FileUpload::where([
                    'id_satpen' => $satpen->id_satpen,
                    'typefile' => "piagam",
                ])->update([
                    //                    'qrcode' => GenerateQr::encodeQr(),
                    'nm_file' => $piagamFilename,
                    'tgl_file' => $request->tgl_doc,
                ]);
                FileUpload::where([
                    'id_satpen' => $satpen->id_satpen,
                    'typefile' => "sk",
                ])->update([
                    //                    'qrcode' => GenerateQr::encodeQr(),
                    'nm_file' => $skFilename,
                    'tgl_file' => $request->tgl_doc,
                ]);

                /**
                 * get relation data
                 */
                $satpenProfile = Satpen::with(['kategori', 'provinsi', 'kabupaten', 'jenjang', 'filereg', 'file'])
                    ->where('id_satpen', '=', $satpen->id_satpen)
                    ->first();

                /**
                 * Create pdf document and save in server
                 */
                ExportDocument::makePiagamDokumen($satpenProfile);
                ExportDocument::makeSKDokumen($satpenProfile);

                /**
                 * update status satpen menjadi disetujui
                 */
                $satpen->update([
                    'actived_date' => Date::now(),
                ]);

                $this->updateSatpenStatus((new StatusSatpenRequest())
                        ->merge(["status_verifikasi" => "setujui"]),
                    $satpen
                );

                return redirect()->back()->with('success', 'Dokumen Surat Keputusan dan Piagam telah regenerate');
            }

            return redirect()->back()->with('error', 'satpen tidak ditemukan!');
        } catch (\Exception $e) {
            throw new CatchErrorException("[REGENERATE PIAGAM AND SK] has error " . $e);
        }
    }

    public function destroySatpen(Satpen $satpen)
    {
        try {
            $fileRegister = FileRegister::where('id_satpen', '=', $satpen->id_satpen);
            foreach ($fileRegister->get() as $file) {
                Storage::disk('uploads')->delete($file->filesurat);
            }
            $fileRegister->delete();
            $fileUploads = FileUpload::where('id_satpen', '=', $satpen->id_satpen);
            foreach ($fileUploads->get() as $file) {
                Storage::delete("generated/" . strtolower($file->typefile) . "/" . $file->nm_file);
            }
            $fileUploads->delete();
            User::find($satpen->id_user)->delete();

            return redirect()->back()->with('success', 'Berhasil menghapus satpen');
        } catch (\Exception $e) {
            throw new CatchErrorException("[DESTROY SATPEN] has error " . $e);
        }
    }

    public function sendNotifEmail(Satpen $satpen)
    {
        if ($satpen) {
            MailService::send([
                "to" => $satpen->email,
                "subject" => "Announcement",
                "recipient" => $satpen->nm_satpen,
                "content" => "<p>Kami mengingatkan kembali bahwa status permohonan satuan pendidikan anda dalam revisi.
                                    Sehingga perlu perbaikan data sesuai dengan arahan verifikator.</p>"
            ]);
            return redirect()->back()->with('success', 'Email berhasil dikirimkan');
        }
        return redirect()->back()->with('error', 'Satpen tidak ditemukan');
    }

    public function getAllPDPTKOrFilter(Request $request)
    {
        $paginatePerPage = Settings::get("count_perpage");
        $specificFilter = request()->specificFilter;
        try {
            $tapel = @$request->tapel ? $request->tapel : Settings::get("current_tapel");
            if (
                $request->jenjang
                || $request->kabupaten
                || $request->cabang
                || $request->provinsi
                || $request->kategori || $request->keyword || $request->lembaga || $request->filled
            ) {

                $filter = [];
                $keywordFilter = [];
                $lembaga = ["SEKOLAH", "MADRASAH"];
                $filled = ["0", "1"];
                if ($request->jenjang) $filter["id_jenjang"] = $request->jenjang;
                if ($request->kabupaten) $filter["id_kab"] = $request->kabupaten;
                if ($request->cabang) $filter["id_pc"] = $request->cabang;
                if ($request->provinsi) $filter["id_prov"] = $request->provinsi;
                if ($request->kategori) $filter["id_kategori"] = $request->kategori;
                if ($request->status) $statuses = [$request->status];
                if ($request->lembaga) $lembaga = [strtoupper($request->lembaga)];
                if ($request->filled) $filled = [$request->filled == "true" ? "1" : "0"];
                if ($request->keyword) {
                    array_push($keywordFilter, ["nm_satpen", "like", "%" . $request->keyword . "%"]);
                    array_push($keywordFilter, ["npsn", "like", "%" . $request->keyword . "%"]);
                    array_push($keywordFilter, ["no_registrasi", "like", "%" . $request->keyword . "%"]);
                    array_push($keywordFilter, ["yayasan", "like", "%" . $request->keyword . "%"]);
                }

                if ($filter || $lembaga || $filled) {
                    $pdptkQuery = PDPTK::with([
                        'satpen:id_satpen,id_jenjang,id_prov,id_kab,no_registrasi,nm_satpen',
                        'satpen.jenjang:id_jenjang,nm_jenjang',
                        'satpen.provinsi:id_prov,nm_prov',
                        'satpen.kabupaten:id_kab,nama_kab',
                    ])
                        ->where(function ($query) use ($specificFilter) {
                            $query->whereHas('satpen', function ($q) use ($specificFilter) {
                                $q->where($specificFilter);
                            });
                        })
                        ->where('tapel', '=', $tapel)
                        ->whereIn('status_sinkron', $filled)
                        ->whereHas('satpen', function ($query) use ($filter) {
                            $query->where($filter);
                        })
                        ->whereHas('satpen.jenjang', function ($query) use ($lembaga) {
                            $query->whereIn('lembaga', $lembaga);
                        })
                        ->whereHas('satpen', function ($query) use ($keywordFilter) {
                            $query->where(function ($subQuery) use ($keywordFilter) {
                                foreach ($keywordFilter as $condition) {
                                    $subQuery->orWhere(...$condition);
                                }
                            });
                        });
                    //                        ->get();
                }
            } else {
                $pdptkQuery = PDPTK::with([
                    'satpen:id_satpen,id_jenjang,id_prov,id_kab,no_registrasi,nm_satpen',
                    'satpen.jenjang:id_jenjang,nm_jenjang',
                    'satpen.provinsi:id_prov,nm_prov',
                    'satpen.kabupaten:id_kab,nama_kab',
                ])
                    ->where('tapel', '=', $tapel)
                    ->where(function ($query) use ($specificFilter) {
                        $query->whereHas('satpen', function ($q) use ($specificFilter) {
                            $q->where($specificFilter);
                        });
                    });
                //                    ->get();
            }
            $sum = [
                'sumPdLk' => $pdptkQuery->sum('pd_lk'),
                'sumPdPr' => $pdptkQuery->sum('pd_pr'),
                'sumJmlPd' => $pdptkQuery->sum('jml_pd'),
                'sumGuruLk' => $pdptkQuery->sum('guru_lk'),
                'sumGuruPr' => $pdptkQuery->sum('guru_pr'),
                'sumJmlGuru' => $pdptkQuery->sum('jml_guru'),
                'sumTendikLk' => $pdptkQuery->sum('tendik_lk'),
                'sumTendikPr' => $pdptkQuery->sum('tendik_pr'),
                'sumJmlTendik' => $pdptkQuery->sum('jml_tendik'),
            ];

            $pdptkCount = $pdptkQuery->count();
            $pdptkData = $pdptkQuery
                ->paginate($paginatePerPage)->appends(request()->query());

            if (auth()->user()->cabangId) {
                $region = $pdptkQuery->first()?->satpen
                    ->kabupaten
                    ->nama_kab ?? 'Indonesia';
            } elseif (auth()->user()->provId) {
                $region = $pdptkQuery->first()?->satpen
                    ->provinsi
                    ->nm_prov ?? 'Indonesia';
            } else {
                $region = 'Indonesia';
            }

            /**
             * If satpen profile null is user access satpen id not releate with user id
             */
            if (!$pdptkData) return redirect()->back()->with('error', 'Forbidden to access pdptk data');

            $propinsi = Provinsi::all();
            $jenjang = Jenjang::all();
            $kategori = Kategori::all();
            $tapel = TahunPelajaran::orderBy('id', 'desc')->get();

            return view('admin.satpen.pdptk', compact(
                'pdptkData',
                'pdptkCount',
                'propinsi',
                'jenjang',
                'kategori',
                'tapel',
                'sum',
                'region',
            ));
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET ALL PDPTK OR FILTER] has error " . $e);
        }
    }

    public function processBulkSyncPDPTK(Request $request)
    {
        try {
            $url = config('app.referensi_data_crawler') . 'dapo-data/bulk';

            $school = [];
            $tapel = $request->tapel ?? Settings::get("current_tapel");
            Satpen::select('id_satpen', 'npsn')
                ->leftJoin('virtual_npsn', 'satpen.npsn', '=', 'virtual_npsn.nomor_virtual')
                ->whereIn('status', ['setujui', 'expired', 'perpanjangan'])
                ->whereNull('virtual_npsn.nomor_virtual')
                ->get()->map(function ($satpen) use (&$school, $tapel) {
                    array_push($school, [
                        "satpenid" => $satpen->id_satpen,
                        "npsn" => $satpen->npsn,
                        "tapel" => $tapel,
                    ]);
                });

            $response = Http::post($url, [
                "schools" => $school,
            ]);

            $responseJson = $response->json();

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Proses sinkronisasi PDPTK berjalan di latar belakang');
            }
            return redirect()->back()->with('error', 'Proses sinkronisasi gagal, ada masalah pada service ' . $responseJson['error']);
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET PROCESS SYNC BULK PDPTK] has error " . $e);
        }
    }

    public function processSyncPDPTK(Request $request, Satpen $satpen)
    {
        try {
            $url = config('app.referensi_data_crawler') . 'dapo-data/';

            $payload = [
                "satpenid" => $satpen->id_satpen,
                "npsn" => $satpen->npsn,
                "tapel" => $request->tapel ?? Settings::get("current_tapel"),
            ];

            $response = Http::post($url, $payload);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Proses sinkronisasi PDPTK berhasil');
            }
            $errorData = $response->json();
            return redirect()->back()->with('error', 'Proses sinkronisasi gagal. ' . $errorData['error']);
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET PROCESS SYNC PDPTK] has error " . $e);
        }
    }

    public function getAllOtherDataOrFilter(Request $request)
    {
        $paginatePerPage = Settings::get("count_perpage");
        $specificFilter = request()->specificFilter;
        try {
            if (
                $request->jenjang
                || $request->kabupaten
                || $request->cabang
                || $request->provinsi
                || $request->kategori || $request->keyword || $request->lembaga || $request->akreditasi
            ) {

                $filter = [];
                $keywordFilter = [];
                $lembaga = ["SEKOLAH", "MADRASAH"];
                $akreditasi = ["A", "B", "C", "-"];
                $lingkungan_satpen = ["Sekolah berbasis Pondok Pesantren", "Sekolah Boarding", "Sekolah biasa", ""];
                if ($request->jenjang) $filter["id_jenjang"] = $request->jenjang;
                if ($request->kabupaten) $filter["id_kab"] = $request->kabupaten;
                if ($request->cabang) $filter["id_pc"] = $request->cabang;
                if ($request->provinsi) $filter["id_prov"] = $request->provinsi;
                if ($request->kategori) $filter["id_kategori"] = $request->kategori;
                if ($request->lembaga) $lembaga = [strtoupper($request->lembaga)];
                if ($request->akreditasi) $akreditasi = [strtoupper($request->akreditasi)];
                if ($request->lingkungan_satpen) $lingkungan_satpen = [$request->lingkungan_satpen];
                if ($request->keyword) {
                    array_push($keywordFilter, ["nm_satpen", "like", "%" . $request->keyword . "%"]);
                    array_push($keywordFilter, ["npsn", "like", "%" . $request->keyword . "%"]);
                    array_push($keywordFilter, ["no_registrasi", "like", "%" . $request->keyword . "%"]);
                    array_push($keywordFilter, ["yayasan", "like", "%" . $request->keyword . "%"]);
                }

                if ($filter || $lembaga) {
                    $othersQuery = Others::with([
                        'satpen:id_satpen,id_jenjang,id_prov,id_kab,no_registrasi,nm_satpen',
                        'satpen.jenjang:id_jenjang,nm_jenjang,lembaga',
                        'satpen.provinsi:id_prov,nm_prov',
                        'satpen.kabupaten:id_kab,nama_kab',
                    ])
                        ->where(function ($query) use ($specificFilter) {
                            $query->whereHas('satpen', function ($q) use ($specificFilter) {
                                $q->where($specificFilter);
                            });
                        })
                        ->whereIn('lingkungan_satpen', $lingkungan_satpen)
                        ->whereIn('akreditasi', $akreditasi)
                        ->whereHas('satpen', function ($query) use ($filter) {
                            $query->where($filter);
                        })
                        ->whereHas('satpen.jenjang', function ($query) use ($lembaga) {
                            $query->whereIn('lembaga', $lembaga);
                        })
                        ->whereHas('satpen', function ($query) use ($keywordFilter) {
                            $query->where(function ($subQuery) use ($keywordFilter) {
                                foreach ($keywordFilter as $condition) {
                                    $subQuery->orWhere(...$condition);
                                }
                            });
                        });
                }
            } else {
                $othersQuery = Others::with([
                    'satpen:id_satpen,id_jenjang,id_prov,id_kab,no_registrasi,nm_satpen',
                    'satpen.jenjang:id_jenjang,nm_jenjang',
                    'satpen.provinsi:id_prov,nm_prov',
                    'satpen.kabupaten:id_kab,nama_kab',
                ])
                    ->where(function ($query) use ($specificFilter) {
                        $query->whereHas('satpen', function ($q) use ($specificFilter) {
                            $q->where($specificFilter);
                        });
                    });
            }

            $othersCount = $othersQuery->count();
            $othersData = $othersQuery
                ->paginate($paginatePerPage)->appends(request()->query());

            /**
             * If satpen profile null is user access satpen id not releate with user id
             */
            if (!$othersData) return redirect()->back()->with('error', 'Forbidden to access data lainnya');

            $propinsi = Provinsi::all();
            $jenjang = Jenjang::all();
            $kategori = Kategori::all();

            return view('admin.satpen.other', compact(
                'othersData',
                'othersCount',
                'propinsi',
                'jenjang',
                'kategori',
            ));
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET ALL OTHER DATA OR FILTER] has error " . $e);
        }
    }

    public function processBulkSyncOthers()
    {
        try {
            $url = config('app.referensi_data_crawler') . 'referensi-data/bulk';

            $school = [];
            Satpen::select('id_satpen', 'npsn')
                ->leftJoin('virtual_npsn', 'satpen.npsn', '=', 'virtual_npsn.nomor_virtual')
                ->whereIn('status', ['setujui', 'expired', 'perpanjangan'])
                ->whereNull('virtual_npsn.nomor_virtual')
                ->get()->map(function ($satpen) use (&$school) {
                    array_push($school, [
                        "satpenid" => $satpen->id_satpen,
                        "npsn" => $satpen->npsn
                    ]);
                });

            $response = Http::post($url, [
                "schools" => $school,
            ]);

            $responseJson = $response->json();

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Proses sinkronisasi Data Lainnya berjalan di latar belakang');
            }
            return redirect()->back()->with('error', 'Proses sinkronisasi gagal, ada masalah pada service ' . $responseJson['error']);
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET PROCESS BULK OTHERS] has error " . $e);
        }
    }

    public function processSyncOthers(Satpen $satpen)
    {
        try {

            $url = config('app.referensi_data_crawler') . 'referensi-data/';

            $payload = [
                "satpenid" => $satpen->id_satpen,
                "npsn" => $satpen->npsn
            ];

            $response = Http::post($url, $payload);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Proses sinkronisasi Data Lainnya berhasil');
            }
            $errorData = $response->json();
            return redirect()->back()->with('error', 'Proses sinkronisasi gagal. ' . $errorData['error']);
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET PROCESS SYNC OTHERS] has error " . $e);
        }
    }

    public function showHistoryLayanan($userId)
    {

        $prosesHistory = collect([
            ...BHPNU::where([
                'id_user' => $userId,
            ])
                ->whereIn('status', ['mengisi persyaratan', 'verifikasi', 'perbaikan', 'dokumen diproses'])
                ->select(
                    'id_bhpnu as id',
                    'bukti_bayar',
                    'tanggal',
                    'tgl_dikirim as acc',
                    'tgl_expired as expiry',
                    'status',
                    'created_at',
                    DB::raw("'BHPNU' as layanan")
                )
                ->get(),
            ...Coretax::where([
                'id_user' => $userId,
            ])
                ->whereIn('status', ['mengisi persyaratan', 'verifikasi', 'perbaikan', 'dokumen diproses'])
                ->select(
                    'id as id',
                    DB::raw("'' as bukti_bayar"),
                    'tgl_submit as tanggal',
                    'tgl_acc as acc',
                    'tgl_expiry as expiry',
                    'status',
                    'created_at',
                    DB::raw("'CORETAX' as layanan")
                )
                ->get(),
            ...OSS::where([
                'id_user' => $userId,
            ])
                ->whereIn('status', ['mengisi persyaratan','verifikasi','perbaikan','dokumen diproses'])
                ->select(
                    'id_oss as id',
                    'bukti_bayar',
                    'tanggal',
                    'tgl_izin as acc',
                    'tgl_expired as expiry',
                    'status',
                    'created_at',
                    DB::raw("'OSS' as layanan")
                )
                ->get(),

        ])->sortByDesc('created_at')->values();

        $finalHistory = collect([
            ...BHPNU::where([
                'id_user' => $userId,
                'status' => 'dokumen dikirim'
            ])
                ->select(
                    'id_bhpnu as id',
                    'bukti_bayar',
                    'tanggal',
                    'tgl_dikirim as acc',
                    'tgl_expired as expiry',
                    'status',
                    'created_at',
                    DB::raw("'BHPNU' as layanan")
                )
                ->get(),
            ...Coretax::where([
                'id_user' => $userId,
                'status' => 'final aprove'
            ])
                ->select(
                    'id as id',
                    DB::raw("'' as bukti_bayar"),
                    'tgl_submit as tanggal',
                    'tgl_acc as acc',
                    'tgl_expiry as expiry',
                    'status',
                    'created_at',
                    DB::raw("'CORETAX' as layanan")
                )
                ->get(),
            ...OSS::where([
                'id_user' => $userId,
                'status' => 'izin terbit'
            ])
                ->select(
                    'id_oss as id',
                    'bukti_bayar',
                    'tanggal',
                    'tgl_izin as acc',
                    'tgl_expired as expiry',
                    'status',
                    'created_at',
                    DB::raw("'OSS' as layanan")
                )
                ->get(),

        ])->sortByDesc('created_at')->values();

        return view('admin.satpen.history-layanan', compact('prosesHistory', 'finalHistory'));
    }


    public function underConstruction()
    {
        return view('template.constructionad');
    }
}
