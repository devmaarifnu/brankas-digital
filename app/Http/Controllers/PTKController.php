<?php

namespace App\Http\Controllers;

use App\Models\PTK;
use App\Models\PTKStatusHistory;
use App\Models\NPYPSatpen;
use App\Models\Satpen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PTKController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get NPYP for this satpen
        $npypSatpen = NPYPSatpen::with(['npyp', 'satpen'])
            ->whereHas('satpen', function ($query) use ($user) {
                $query->where('id_user', $user->id_user);
            })
            ->first();

        return view('npyp.formPtk', compact('npypSatpen'));
    }

    public function getPTKData(Request $request)
    {
        try {
            $user = Auth::user();

            $status = $request->get('status', 'verifikasi');
            $search = $request->get('search', '');
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Base query - handle pengisian as verifikasi for data display
            $actualStatus = $status === 'pengisian' ? 'verifikasi' : $status;
            $query = PTK::with(['npyp', 'satpen'])
               ->whereHas('satpen', function ($query) use ($user) {
                $query->where('id_user', $user->id_user);
                })
                ->where('status_ajuan', $actualStatus);

            // Search functionality
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_ptk', 'LIKE', "%{$search}%")
                      ->orWhere('nik', 'LIKE', "%{$search}%")
                      ->orWhere('jenis_ptk', 'LIKE', "%{$search}%")
                      ->orWhere('nip', 'LIKE', "%{$search}%");
                });
            }

            // Get paginated results
            $paginatedData = $query->orderBy('created_at', 'desc')
                                  ->paginate($perPage, ['*'], 'page', $page);

            $result = [];
            foreach ($paginatedData->items() as $index => $ptk) {
                $globalIndex = ($page - 1) * $perPage + $index + 1;
                $result[] = [
                    'no' => '<span class="badge bg-light text-dark">' . $globalIndex . '</span>',
                    'nik' => '<span class="fw-bold">' . $ptk->nik . '</span>',
                    'nama_ptk' => '<div><h6 class="mb-1">' . $ptk->nama_ptk . '</h6>' .
                        '<small class="text-muted">' . $ptk->jenis_ptk . '</small></div>',
                    'jenis_kelamin' => $ptk->jenis_kelamin,
                    'status_kepegawaian' => '<small>' . $ptk->status_kepegawaian . '</small>',
                    'nip' => $ptk->nip ?? '<span class="text-muted">-</span>',
                    'status' => '<span class="badge ' . $ptk->status_badge . '">' . $ptk->status_label . '</span>',
                    'tanggal_dibuat' => $ptk->created_at->format('d M Y'),
                    'aksi' => $this->generateActionButtons($ptk, $actualStatus)
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'current_page' => $paginatedData->currentPage(),
                'last_page' => $paginatedData->lastPage(),
                'per_page' => $paginatedData->perPage(),
                'total' => $paginatedData->total(),
                'from' => $paginatedData->firstItem(),
                'to' => $paginatedData->lastItem()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    private function generateActionButtons($ptk, $status)
    {
        $buttons = '';
        
        // View button - always available
        $buttons .= '<button class="btn btn-outline-info btn-sm me-1" onclick="viewPTK(' . $ptk->id . ')" title="Lihat Detail">
            <i class="ti ti-eye"></i>
        </button>';

        // Edit button - only for revisi status
        if ($status === 'revisi') {
            $buttons .= '<button class="btn btn-outline-primary btn-sm me-1" onclick="editPTK(' . $ptk->id . ')" title="Edit">
                <i class="ti ti-edit"></i>
            </button>';
            
            // Submit revisi button - only for revisi status
            $buttons .= '<button class="btn btn-outline-success btn-sm me-1" onclick="submitRevisi(' . $ptk->id . ')" title="Ajukan Ulang">
                <i class="ti ti-send"></i>
            </button>';
        }

        // Delete button - only for verifikasi status
        if ($status === 'verifikasi') {
            $buttons .= '<button class="btn btn-outline-danger btn-sm" onclick="deletePTK(' . $ptk->id . ')" title="Hapus">
                <i class="ti ti-trash"></i>
            </button>';
        }

        // Download SK button - only for dikeluarkan status
        if ($status === 'dikeluarkan' && $ptk->nomor_sk_keluar) {
            $buttons .= '<button class="btn btn-outline-success btn-sm ms-1" onclick="downloadSK(' . $ptk->id . ')" title="Download SK">
                <i class="ti ti-download"></i>
            </button>';
        }

        return $buttons;
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $satpen = Satpen::where('id_user', $user->id_user)->first();

            // Get NPYP for this satpen
            $npypSatpen = NPYPSatpen::whereHas('satpen', function ($query) use ($user) {
                $query->where('id_user', $user->id_user);
            })->first();


            // Validation
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|size:16|unique:ptk,nik',
                'nama_ptk' => 'required|string|max:255',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
                'nama_ibu' => 'required|string|max:255',
                'agama' => 'required|in:' . implode(',', PTK::AGAMA),
                'kebutuhan_khusus' => 'nullable|in:' . implode(',', PTK::KEBUTUHAN_KHUSUS),
                'status_perkawinan' => 'required|in:' . implode(',', PTK::STATUS_PERKAWINAN),
                'email' => 'required|email|unique:ptk,email',
                'kabupaten_kota' => 'required|string|max:255',
                'kecamatan' => 'required|string|max:255',
                'desa_kelurahan' => 'required|string|max:255',
                'alamat' => 'required|string',
                'kode_pos' => 'required|string|size:5',
                'jenis_ptk' => 'required|in:' . implode(',', PTK::JENIS_PTK),
                'status_kepegawaian' => 'required|in:' . implode(',', PTK::STATUS_KEPEGAWAIAN),
                'nip' => 'nullable|string|max:50',
                'lembaga_pengangkat' => 'required|in:' . implode(',', PTK::LEMBAGA_PENGANGKAT),
                'no_sk_pengangkatan' => 'required|string|max:255',
                'tmt_pengangkatan' => 'required|date',
                'sumber_gaji' => 'required|in:' . implode(',', PTK::SUMBER_GAJI),
                'lisensi_kepala_sekolah' => 'nullable|in:Sudah,Belum',
                'nomor_surat_tugas' => 'required|string|max:255',
                'tanggal_surat_tugas' => 'required|date',
                'tmt_tugas' => 'required|date',
                'upload_sk' => 'required|file|mimes:pdf|max:1024'
            ], [
                'nik.required' => 'NIK harus diisi',
                'nik.size' => 'NIK harus terdiri dari 16 digit',
                'nik.unique' => 'NIK sudah terdaftar',
                'nama_ptk.required' => 'Nama PTK harus diisi',
                'email.unique' => 'Email sudah terdaftar',
                'upload_sk.required' => 'File SK harus diupload',
                'upload_sk.mimes' => 'Format file harus PDF',
                'upload_sk.max' => 'Ukuran file maksimal 1MB'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Upload file
            $uploadPath = null;
            if ($request->hasFile('upload_sk') && $request->file('upload_sk')->isValid()) {
                $file = $request->file('upload_sk');
                $filename = time() . '_' . $file->getClientOriginalName();
                $uploadPath = Storage::disk('ptk-doc')->putFileAs("sk", $file, $filename);
            }

            // Create PTK record
            $ptk = PTK::create([
                'id_npyp' => $npypSatpen->id_npyp,
                'id_satpen' => $satpen->id_satpen,
                'nik' => $request->nik,
                'nama_ptk' => strtoupper($request->nama_ptk),
                'tempat_lahir' => strtoupper($request->tempat_lahir),
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nama_ibu' => strtoupper($request->nama_ibu),
                'agama' => $request->agama,
                'kebutuhan_khusus' => $request->kebutuhan_khusus ?? 'Tidak ada',
                'status_perkawinan' => $request->status_perkawinan,
                'email' => strtolower($request->email),
                'kabupaten_kota' => $request->kabupaten_kota,
                'kecamatan' => $request->kecamatan,
                'desa_kelurahan' => $request->desa_kelurahan,
                'alamat' => $request->alamat,
                'kode_pos' => $request->kode_pos,
                'jenis_ptk' => $request->jenis_ptk,
                'status_kepegawaian' => $request->status_kepegawaian,
                'nip' => $request->nip,
                'lembaga_pengangkat' => $request->lembaga_pengangkat,
                'no_sk_pengangkatan' => $request->no_sk_pengangkatan,
                'tmt_pengangkatan' => $request->tmt_pengangkatan,
                'sumber_gaji' => $request->sumber_gaji,
                'lisensi_kepala_sekolah' => $request->lisensi_kepala_sekolah ?? 'Belum',
                'nomor_surat_tugas' => $request->nomor_surat_tugas,
                'tanggal_surat_tugas' => $request->tanggal_surat_tugas,
                'tmt_tugas' => $request->tmt_tugas,
                'upload_sk' => $uploadPath,
                'status_ajuan' => 'verifikasi'
            ]);

            // Create status history
            PTKStatusHistory::createHistory(
                $ptk->id,
                null,
                'verifikasi',
                'PTK baru diajukan oleh ' . $satpen->nm_satpen
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data PTK berhasil disimpan dan menunggu verifikasi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();
            
            $ptk = PTK::with(['npyp', 'satpen.provinsi'])
                ->whereHas('satpen', function ($query) use ($user) {
                    $query->where('id_user', $user->id_user);
                })
                ->where('id', $id)
                ->firstOrFail();

            // Get status history
            $statusHistory = PTKStatusHistory::where('ptk_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $ptk,
                'history' => $statusHistory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data PTK tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $satpen = Satpen::where('id_user', $user->id_user)->first();
            
            $ptk = PTK::where('id', $id)
                ->where('id_satpen', $satpen->id_satpen)
                ->where('status_ajuan', 'revisi')
                ->firstOrFail();

            // Validation (similar to store but with unique exceptions)
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|size:16|unique:ptk,nik,' . $id,
                'nama_ptk' => 'required|string|max:255',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
                'nama_ibu' => 'required|string|max:255',
                'agama' => 'required|in:' . implode(',', PTK::AGAMA),
                'kebutuhan_khusus' => 'nullable|in:' . implode(',', PTK::KEBUTUHAN_KHUSUS),
                'status_perkawinan' => 'required|in:' . implode(',', PTK::STATUS_PERKAWINAN),
                'email' => 'required|email|unique:ptk,email,' . $id,
                'kabupaten_kota' => 'required|string|max:255',
                'kecamatan' => 'required|string|max:255',
                'desa_kelurahan' => 'required|string|max:255',
                'alamat' => 'required|string',
                'kode_pos' => 'required|string|size:5',
                'jenis_ptk' => 'required|in:' . implode(',', PTK::JENIS_PTK),
                'status_kepegawaian' => 'required|in:' . implode(',', PTK::STATUS_KEPEGAWAIAN),
                'nip' => 'nullable|string|max:50',
                'lembaga_pengangkat' => 'required|in:' . implode(',', PTK::LEMBAGA_PENGANGKAT),
                'no_sk_pengangkatan' => 'required|string|max:255',
                'tmt_pengangkatan' => 'required|date',
                'sumber_gaji' => 'required|in:' . implode(',', PTK::SUMBER_GAJI),
                'lisensi_kepala_sekolah' => 'nullable|in:Sudah,Belum',
                'nomor_surat_tugas' => 'required|string|max:255',
                'tanggal_surat_tugas' => 'required|date',
                'tmt_tugas' => 'required|date',
                'upload_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
            ], [
                'nik.required' => 'NIK harus diisi',
                'nik.size' => 'NIK harus terdiri dari 16 digit',
                'nik.unique' => 'NIK sudah terdaftar',
                'nama_ptk.required' => 'Nama PTK harus diisi',
                'email.unique' => 'Email sudah terdaftar',
                'upload_sk.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG',
                'upload_sk.max' => 'Ukuran file maksimal 5MB'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Handle file upload if new file provided
            $uploadPath = $ptk->upload_sk;
            if ($request->hasFile('upload_sk')) {
                // Delete old file
                if ($uploadPath) {
                    Storage::disk("ptk-doc")->delete($uploadPath);
                }
                
                $file = $request->file('upload_sk');
                $filename = time() . '_' . $file->getClientOriginalName();
                $uploadPath = Storage::disk('ptk-doc')->putFileAs("sk", $file, $filename);
            }

            // Update PTK record
            $ptk->update([
                'nik' => $request->nik,
                'nama_ptk' => strtoupper($request->nama_ptk),
                'tempat_lahir' => strtoupper($request->tempat_lahir),
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nama_ibu' => strtoupper($request->nama_ibu),
                'agama' => $request->agama,
                'kebutuhan_khusus' => $request->kebutuhan_khusus ?? 'Tidak ada',
                'status_perkawinan' => $request->status_perkawinan,
                'email' => strtolower($request->email),
                'kabupaten_kota' => $request->kabupaten_kota,
                'kecamatan' => $request->kecamatan,
                'desa_kelurahan' => $request->desa_kelurahan,
                'alamat' => $request->alamat,
                'kode_pos' => $request->kode_pos,
                'jenis_ptk' => $request->jenis_ptk,
                'status_kepegawaian' => $request->status_kepegawaian,
                'nip' => $request->nip,
                'lembaga_pengangkat' => $request->lembaga_pengangkat,
                'no_sk_pengangkatan' => $request->no_sk_pengangkatan,
                'tmt_pengangkatan' => $request->tmt_pengangkatan,
                'sumber_gaji' => $request->sumber_gaji,
                'lisensi_kepala_sekolah' => $request->lisensi_kepala_sekolah ?? 'Belum',
                'nomor_surat_tugas' => $request->nomor_surat_tugas,
                'tanggal_surat_tugas' => $request->tanggal_surat_tugas,
                'tmt_tugas' => $request->tmt_tugas,
                'upload_sk' => $uploadPath,
                'status_ajuan' => 'verifikasi', // Reset to verifikasi after update
                'keterangan_revisi' => null, // Clear revision notes
                'tanggal_revisi' => null // Clear revision date
            ]);

            // Create status history if status changed
            PTKStatusHistory::createHistory(
                $ptk->id,
                $ptk->getOriginal('status_ajuan'),
                'verifikasi',
                'Data PTK diperbarui oleh ' . $satpen->nm_satpen
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data PTK berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();

            $ptk = PTK::where('id', $id)
               ->whereHas('satpen', function ($query) use ($user) {
                $query->where('id_user', $user->id_user);
                })
                ->where('status_ajuan', 'verifikasi')
                ->firstOrFail();

            DB::beginTransaction();

            // Delete file
            if ($ptk->upload_sk) {
                Storage::disk("ptk-doc")->delete($ptk->upload_sk);
            }

            // Delete status history
            PTKStatusHistory::where('ptk_id', $id)->delete();

            // Delete PTK record
            $ptk->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data PTK berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStatusCounts()
    {
        try {
            $user = Auth::user();
            $satpen = Satpen::where('id_user', $user->id_user)->first();

            $counts = [
                'pengisian' => PTK::where('id_satpen', $satpen->id_satpen)->where('status_ajuan', 'verifikasi')->count(),
                'verifikasi' => PTK::where('id_satpen', $satpen->id_satpen)->where('status_ajuan', 'verifikasi')->count(),
                'revisi' => PTK::where('id_satpen', $satpen->id_satpen)->where('status_ajuan', 'revisi')->count(),
                'proses' => PTK::where('id_satpen', $satpen->id_satpen)->where('status_ajuan', 'proses')->count(),
                'approve' => PTK::where('id_satpen', $satpen->id_satpen)->where('status_ajuan', 'approve')->count(),
                'dikeluarkan' => PTK::where('id_satpen', $satpen->id_satpen)->where('status_ajuan', 'dikeluarkan')->count(),
                'total' => PTK::where('id_satpen', $satpen->id_satpen)->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $counts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitRevisi($id)
    {
        try {
            $user = Auth::user();
            $satpen = Satpen::where('id_user', $user->id_user)->first();

            $ptk = PTK::where('id', $id)
                ->where('id_satpen', $satpen->id_satpen)
                ->where('status_ajuan', 'revisi')
                ->firstOrFail();

            DB::beginTransaction();

            // Update status to verifikasi
            $ptk->update([
                'status_ajuan' => 'verifikasi',
                'keterangan_revisi' => null, // Clear revision notes
                'tanggal_revisi' => null
            ]);

            // Create status history
            PTKStatusHistory::createHistory(
                $ptk->id,
                'revisi',
                'verifikasi',
                'PTK diajukan ulang setelah revisi oleh ' . $satpen->nm_satpen
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PTK berhasil diajukan ulang untuk verifikasi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}