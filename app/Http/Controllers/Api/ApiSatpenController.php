<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\MailService;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\SATPENController as SatpenControllerAdmin;
use App\Http\Requests\StatusSatpenRequest;
use App\Models\FileRegister;
use App\Models\Kategori;
use App\Models\PengurusCabang;
use App\Models\Provinsi;
use App\Models\Satpen;
use App\Models\Timeline;
use App\Models\VirtualNPSN;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ApiSatpenController extends Controller
{
    /**
     * Determine kategori satpen
     */
    public static function makekategori(string $yayasan, string $statusTanah): ?Kategori
    {
        $kategori = null;
        if ($yayasan == 'bhpnu' && $statusTanah == 'jamiyah') {
            $kategori = 'A';
        } elseif ($yayasan == 'bhpnu' && $statusTanah <> 'jamiyah') {
            $kategori = 'B';
        } elseif ($yayasan <> 'bhpnu' && $statusTanah == 'jamiyah') {
            $kategori = 'C';
        } elseif ($yayasan <> 'bhpnu' && $statusTanah <> 'jamiyah') {
            $kategori = 'D';
        }
        return Kategori::where('nm_kategori', '=', $kategori)->first();
    }

    /**
     * List all Satpen with filtering & pagination
     */
    public function index(Request $request)
    {
        $query = Satpen::with([
            'kategori:id_kategori,nm_kategori',
            'provinsi:id_prov,nm_prov',
            'kabupaten:id_kab,nama_kab',
            'cabang:id_pc,nama_pc',
            'jenjang:id_jenjang,nm_jenjang',
        ]);

        // Role-based scoping
        $user = auth()->user();
        if ($user) {
            if ($user->role === 'admin wilayah') {
                $query->where('id_prov', $user->provId);
            } elseif ($user->role === 'admin cabang') {
                $query->where('id_pc', $user->cabangId);
            }
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default active statuses if not specified
            if (!$request->filled('all_status')) {
                $query->whereIn('status', ['setujui', 'expired', 'perpanjangan']);
            }
        }

        if ($request->filled('jenjang')) {
            $query->where('id_jenjang', $request->jenjang);
        }
        if ($request->filled('provinsi')) {
            $query->where('id_prov', $request->provinsi);
        }
        if ($request->filled('kabupaten')) {
            $query->where('id_kab', $request->kabupaten);
        }
        if ($request->filled('cabang')) {
            $query->where('id_pc', $request->cabang);
        }
        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        // Search keyword
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nm_satpen', 'LIKE', "%{$keyword}%")
                  ->orWhere('npsn', 'LIKE', "%{$keyword}%")
                  ->orWhere('no_registrasi', 'LIKE', "%{$keyword}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$keyword}%")
                  ->orWhere('kelurahan', 'LIKE', "%{$keyword}%");
            });
        }

        $perPage = (int) $request->get('per_page', 15);
        $satpenList = $query->orderBy('id_satpen', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $satpenList
        ], HttpResponse::HTTP_OK);
    }

    /**
     * Show Satpen details
     */
    public function show($id)
    {
        $satpen = Satpen::with([
            'kategori',
            'provinsi',
            'kabupaten',
            'cabang',
            'jenjang',
            'filereg',
            'timeline',
            'pdptk',
            'other',
            'file'
        ])->find($id);

        if (!$satpen) {
            return response()->json([
                'success' => false,
                'message' => 'Satpen tidak ditemukan.'
            ], HttpResponse::HTTP_NOT_FOUND);
        }

        // Role check
        $user = auth()->user();
        if ($user) {
            if ($user->role === 'operator' && $satpen->id_user !== $user->id_user) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk data ini.'], HttpResponse::HTTP_FORBIDDEN);
            }
            if ($user->role === 'admin wilayah' && $satpen->id_prov !== $user->provId) {
                return response()->json(['success' => false, 'message' => 'Data berada di luar wilayah kewenangan Anda.'], HttpResponse::HTTP_FORBIDDEN);
            }
            if ($user->role === 'admin cabang' && $satpen->id_pc !== $user->cabangId) {
                return response()->json(['success' => false, 'message' => 'Data berada di luar cabang kewenangan Anda.'], HttpResponse::HTTP_FORBIDDEN);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $satpen
        ], HttpResponse::HTTP_OK);
    }

    /**
     * Get Current Operator's Satpen profile
     */
    public function mySatpen()
    {
        $user = auth()->user();
        if ($user->role !== 'operator') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya operator sekolah yang memiliki data profil Satpen.'
            ], HttpResponse::HTTP_FORBIDDEN);
        }

        $satpen = Satpen::with([
            'kategori',
            'provinsi',
            'kabupaten',
            'cabang',
            'jenjang',
            'filereg',
            'timeline',
            'pdptk',
            'other',
            'file'
        ])->where('id_user', $user->id_user)->first();

        if (!$satpen) {
            return response()->json([
                'success' => false,
                'message' => 'Data Satpen untuk operator ini belum terdaftar.'
            ], HttpResponse::HTTP_NOT_FOUND);
        }

        $usingVNPSN = VirtualNPSN::where('nomor_virtual', $satpen->npsn)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'satpen' => $satpen,
                'is_using_vnpsn' => $usingVNPSN > 0
            ]
        ], HttpResponse::HTTP_OK);
    }

    /**
     * Register Satpen (API version)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'propinsi' => 'required|integer',
            'kabupaten' => 'required|integer',
            'cabang' => 'required|integer',
            'jenjang' => 'required|integer',
            'nm_satpen' => 'required|string',
            'npsn' => 'required|string',
            'yayasan' => 'required|string',
            'kepsek' => 'required|string',
            'telp' => 'required|string',
            'email' => 'required|email',
            'thn_berdiri' => 'required|string',
            'alamat' => 'required|string',
            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'aset_tanah' => 'required|string',
            'nm_pemilik' => 'required|string',
            'password' => 'required|string|min:6',
            'no_srt_permohonan' => 'required|string',
            'tgl_srt_permohonan' => 'required|string',
            'file_permohonan' => 'required|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'nm_rekom_pc' => 'required|string',
            'cabang_rekom_pc' => 'required|string',
            'no_srt_rekom_pc' => 'required|string',
            'tgl_srt_rekom_pc' => 'required|string',
            'file_rekom_pc' => 'required|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'nm_rekom_pw' => 'required|string',
            'wilayah_rekom_pw' => 'required|string',
            'no_srt_rekom_pw' => 'required|string',
            'tgl_srt_rekom_pw' => 'required|string',
            'file_rekom_pw' => 'required|file|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi pendaftaran gagal.',
                'errors' => $validator->errors()
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $provinsi = Provinsi::find($request->propinsi);
            $cabang = PengurusCabang::find($request->cabang);

            if (!$provinsi || !$cabang) {
                return response()->json(['success' => false, 'message' => 'Provinsi atau Cabang tidak valid.'], HttpResponse::HTTP_BAD_REQUEST);
            }

            $lastOfSatpen = Satpen::orderBy('id_satpen', 'desc')->first();
            $orderedNumber = $lastOfSatpen ? (int) $lastOfSatpen->no_urut : 0;
            $orderedNumber = str_pad(++$orderedNumber, 4, '0', STR_PAD_LEFT);

            $makeCategorySatpen = self::makekategori(strtolower($request->yayasan), strtolower($request->aset_tanah));
            if (!$makeCategorySatpen) {
                return response()->json(['success' => false, 'message' => 'Gagal menentukan kategori Satpen.'], HttpResponse::HTTP_BAD_REQUEST);
            }

            $registerNumber = $provinsi->kode_prov . $cabang->kode_kab . $orderedNumber;

            return DB::transaction(function () use ($request, $registerNumber, $provinsi, $cabang, $makeCategorySatpen, $orderedNumber) {
                // Store files
                $pathFilePermohonan = Storage::disk('uploads')->putFile(null, $request->file('file_permohonan'));
                $pathFileRekomPC = Storage::disk('uploads')->putFile(null, $request->file('file_rekom_pc'));
                $pathFileRekomPW = Storage::disk('uploads')->putFile(null, $request->file('file_rekom_pw'));

                // Create user account
                $user = AuthController::register($registerNumber, $request->password);

                // Create satpen
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

                FileRegister::insert([
                    [
                        'id_satpen' => $satpen->id_satpen,
                        'mapfile' => 'surat_permohonan',
                        'nm_lembaga' => $satpen->nm_satpen,
                        'daerah' => '',
                        'nomor_surat' => $request->no_srt_permohonan,
                        'tgl_surat' => $request->tgl_srt_permohonan,
                        'filesurat' =>  $pathFilePermohonan,
                    ],
                    [
                        'id_satpen' => $satpen->id_satpen,
                        'mapfile' => 'rekom_pc',
                        'daerah' => $request->cabang_rekom_pc,
                        'nm_lembaga' => $request->nm_rekom_pc,
                        'nomor_surat' => $request->no_srt_rekom_pc,
                        'tgl_surat' => $request->tgl_srt_rekom_pc,
                        'filesurat' =>  $pathFileRekomPC,
                    ],
                    [
                        'id_satpen' => $satpen->id_satpen,
                        'mapfile' => 'rekom_pw',
                        'daerah' => $request->wilayah_rekom_pw,
                        'nm_lembaga' => $request->nm_rekom_pw,
                        'nomor_surat' => $request->no_srt_rekom_pw,
                        'tgl_surat' => $request->tgl_srt_rekom_pw,
                        'filesurat' =>  $pathFileRekomPW,
                    ]
                ]);

                // Check VNPSN
                VirtualNPSN::where('nomor_virtual', '=', $request->npsn)->update([
                    'actived_date' => Carbon::now(),
                ]);

                // Initial status: permohonan
                (new SatpenControllerAdmin())->updateSatpenStatus(
                    (new StatusSatpenRequest())->merge([
                        "status_verifikasi" => "permohonan",
                        "keterangan" => "Pendaftaran Satpen baru",
                    ]),
                    $satpen
                );

                // Send email
                MailService::send([
                    "to" => $satpen->email,
                    "subject" => "Registrasi Berhasil",
                    "recipient" => $satpen->nm_satpen,
                    "content" => "<h2>Registrasi Berhasil</h2> <p>Berikut nomor registrasi satpen anda.</p> <div class='button'> <a href='#' class='cta-button'>{$registerNumber}</a> </div> <p>Gunakan nomor registrasi di atas untuk login ke portal SIPINTER.</p>"
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi Satpen berhasil.',
                    'data' => [
                        'no_registrasi' => $registerNumber,
                        'satpen' => $satpen,
                        'user_id' => $user->id_user,
                    ]
                ], HttpResponse::HTTP_CREATED);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pendaftaran Satpen: ' . $e->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Admin updates Satpen Status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status_verifikasi' => 'required|in:setujui,revisi,tolak,permohonan,proses dokumen,expired,perpanjangan',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $satpen = Satpen::find($id);
        if (!$satpen) {
            return response()->json(['success' => false, 'message' => 'Satpen tidak ditemukan.'], HttpResponse::HTTP_NOT_FOUND);
        }

        try {
            $adminController = new SatpenControllerAdmin();
            $statusRequest = (new StatusSatpenRequest())->merge([
                'status_verifikasi' => $request->status_verifikasi,
                'keterangan' => $request->keterangan ?? '',
            ]);

            $adminController->updateSatpenStatus($statusRequest, $satpen);

            return response()->json([
                'success' => true,
                'message' => 'Status Satpen berhasil diperbarui menjadi: ' . $request->status_verifikasi,
                'data' => $satpen->fresh(['timeline'])
            ], HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status Satpen: ' . $e->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
