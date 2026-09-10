<?php

namespace App\Http\Controllers;

use App\Models\Satpen;
use App\Models\NPYP;
use App\Models\NPYPSatpen;
use App\Models\PTK;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class NpypController extends Controller
{
    protected function specificFilter() {
        if (!request()->specificFilter) {
            return [
                "id_pw" => null,
                "id_pc" => null,
            ];
        }
        $specificFilter = request()->specificFilter;
        $specificFilter["id_pw"] = @$specificFilter["id_prov"];
        unset($specificFilter["id_prov"]);
        return $specificFilter;
    }

    protected function npyp() {
        return NPYP::where($this->specificFilter())->first();
    }

    public function indexNpyp() {
        $provinsi = Provinsi::orderBy('nm_prov')->get();
        $npyp = $this->npyp();
        return view('admin.npyp.npyp', compact('npyp', 'provinsi'));
    }

    public function getSatpenList()
    {
        try {
            // Get parameters from request
            $page = request()->get('page', 1);
            $search = request()->get('search', '');
            $perPage = 15;

            // Build query with relationships
            $query = Satpen::with(['jenjang', 'provinsi', 'kabupaten'])
                ->select('id_satpen', 'no_registrasi', 'nm_satpen', 'id_jenjang', 'id_prov', 'id_kab', 'status', 'npsn')
                ->where(request()->specificFilter)
                ->whereIn('status', ['setujui', 'expired', 'perpanjangan'])
                ->whereNotIn('id_satpen', NPYPSatpen::pluck('id_satpen'));

            // Add search functionality
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('nm_satpen', 'LIKE', '%' . $search . '%')
                      ->orWhere('no_registrasi', 'LIKE', '%' . $search . '%')
                      ->orWhere('npsn', 'LIKE', '%' . $search . '%')
                      ->orWhereHas('provinsi', function($provinsiQuery) use ($search) {
                          $provinsiQuery->where('nm_prov', 'LIKE', '%' . $search . '%');
                      })
                      ->orWhereHas('kabupaten', function($kabupatenQuery) use ($search) {
                          $kabupatenQuery->where('nama_kab', 'LIKE', '%' . $search . '%');
                      })
                      ->orWhereHas('jenjang', function($jenjangQuery) use ($search) {
                          $jenjangQuery->where('nm_jenjang', 'LIKE', '%' . $search . '%');
                      });
                });
            }

            // Paginate the results
            $satpenPaginated = $query->paginate($perPage, ['*'], 'page', $page);

            $satpenList = $satpenPaginated->map(function($satpen) {
                return [
                    'id' => $satpen->id_satpen,
                    'no_registrasi' => $satpen->no_registrasi, 
                    'nama_satpen' => $satpen->nm_satpen, 
                    'provinsi' => $satpen->provinsi ? $satpen->provinsi->nm_prov : 'Tidak Diketahui',
                    'kabupaten' => $satpen->kabupaten ? $satpen->kabupaten->nama_kab : 'Tidak Diketahui',
                    'jenjang' => $satpen->jenjang ? $satpen->jenjang->nm_jenjang : 'Tidak Diketahui',
                    'npsn' => $satpen->npsn,
                    'status' => ucfirst($satpen->status)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $satpenList,
                'pagination' => [
                    'current_page' => $satpenPaginated->currentPage(),
                    'last_page' => $satpenPaginated->lastPage(),
                    'per_page' => $satpenPaginated->perPage(),
                    'total' => $satpenPaginated->total(),
                    'from' => $satpenPaginated->firstItem(),
                    'to' => $satpenPaginated->lastItem()
                ],
                'message' => 'Data satpen berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'pagination' => null,
                'message' => 'Gagal mengambil data satpen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'nomor_npyp' => 'required|string|max:50',
                'nama_npyp' => 'required|string|max:255',
                'nama_operator' => 'required|string|max:255',
                'nomor_operator' => 'required|string|max:20',
            ], [
                'nomor_npyp.required' => 'Nomor NPYP harus diisi',
                'nama_npyp.required' => 'Nama NPYP harus diisi',
                'nama_operator.required' => 'Nama Operator harus diisi',
                'nomor_operator.required' => 'Nomor Operator harus diisi',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Check if NPYP data already exists for super admin
            $existingNpyp = $this->npyp();

            if ($existingNpyp) {
                // Update existing data
                $existingNpyp->update([
                    'nomor_npyp' => $request->nomor_npyp,
                    'nama_npyp' => $request->nama_npyp,
                    'nama_operator' => $request->nama_operator,
                    'nomor_operator' => $request->nomor_operator,
                ]);

                return redirect()->back()
                    ->with('success', 'Data NPYP berhasil diperbarui');
            } else {
                // Create new data
                NPYP::create([
                    ...$this->specificFilter(),
                    'nomor_npyp' => $request->nomor_npyp,
                    'nama_npyp' => $request->nama_npyp,
                    'nama_operator' => $request->nama_operator,
                    'nomor_operator' => $request->nomor_operator,
                ]);

                return redirect()->back()
                    ->with('success', 'Data NPYP berhasil disimpan');
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'nomor_npyp' => 'required|string|max:50',
                'nama_npyp' => 'required|string|max:255',
                'nama_operator' => 'required|string|max:255',
                'nomor_operator' => 'required|string|max:20',
            ], [
                'nomor_npyp.required' => 'Nomor NPYP harus diisi',
                'nama_npyp.required' => 'Nama NPYP harus diisi',
                'nama_operator.required' => 'Nama Operator harus diisi',
                'nomor_operator.required' => 'Nomor Operator harus diisi',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Find and update NPYP data
            $npyp = NPYP::findOrFail($id);
            $npyp->update([
                'nomor_npyp' => $request->nomor_npyp,
                'nama_npyp' => $request->nama_npyp,
                'nama_operator' => $request->nama_operator,
                'nomor_operator' => $request->nomor_operator,
            ]);

            return redirect()->back()
                ->with('success', 'Data NPYP berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getSekolahNaunganData(Request $request)
    {
        try {
            // Get NPYP data for super admin
            $npyp = $this->npyp();
            
            if (!$npyp) {
                return response()->json([
                    'draw' => intval($request->draw),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'message' => 'Data NPYP belum ada'
                ]);
            }

            // Base query
            $query = NPYPSatpen::with(['satpen.provinsi', 'satpen.kabupaten', 'satpen.jenjang'])
                ->where('id_npyp', $npyp->id_npyp);

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->whereHas('satpen', function($q) use ($search) {
                    $q->where('nm_satpen', 'LIKE', "%{$search}%")
                      ->orWhere('npsn', 'LIKE', "%{$search}%")
                      ->orWhere('no_registrasi', 'LIKE', "%{$search}%")
                      ->orWhere('kelurahan', 'LIKE', "%{$search}%")
                      ->orWhere('kecamatan', 'LIKE', "%{$search}%");
                });
            }

            // Filter by Provinsi
            if ($request->has('provinsi') && !empty($request->provinsi)) {
                $query->whereHas('satpen', function($q) use ($request) {
                    $q->where('id_prov', $request->provinsi);
                });
            }

            // Filter by Kabupaten
            if ($request->has('kabupaten') && !empty($request->kabupaten)) {
                $query->whereHas('satpen', function($q) use ($request) {
                    $q->where('id_kab', $request->kabupaten);
                });
            }

            // Get total records before pagination
            $totalRecords = NPYPSatpen::where('id_npyp', $npyp->id_npyp)->count();
            $filteredRecords = $query->count();

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $query->offset($start)->limit($length);

            // Order
            if ($request->has('order')) {
                $columns = ['id', 'npsn', 'no_registrasi', 'nm_satpen', 'kelurahan', 'kecamatan', 'kabupaten', 'provinsi'];
                $orderColumn = $columns[$request->order[0]['column']] ?? 'id';
                $orderDir = $request->order[0]['dir'] ?? 'asc';
                
                if (in_array($orderColumn, ['npsn', 'no_registrasi', 'nm_satpen', 'kelurahan', 'kecamatan'])) {
                    $query->join('satpen', 'npyp_satpen.id_satpen', '=', 'satpen.id_satpen')
                          ->orderBy('satpen.' . $orderColumn, $orderDir)
                          ->select('npyp_satpen.*');
                } else {
                    $query->orderBy('npyp_satpen.id', $orderDir);
                }
            }

            $data = $query->get();

            $result = [];
            foreach ($data as $index => $item) {
                $satpen = $item->satpen;

                $result[] = [
                    'no' => '<span class="badge bg-light text-dark">' . ($start + $index + 1) . '</span>',
                    'npsn' => $satpen->npsn ? 
                        '<span class="fw-bold">' . $satpen->npsn . '</span>' : 
                        '<span class="text-muted">Belum ada</span>',
                    'no_registrasi' => $satpen->no_registrasi ?
                        '<a href="' . route('a.rekapsatpen.detail', $satpen->id_satpen) . '" class="text-decoration-none"><i class="ti ti-link me-1"></i><small>' . $satpen->no_registrasi . '</small></a>' :
                        '<span class="text-muted">-</span>',
                    'nama_satpen' => '<div><h6 class="mb-1">' . ($satpen->nm_satpen ?? 'Nama tidak tersedia') . '</h6>' .
                        '<small class="text-muted"><i class="ti ti-calendar me-1"></i>Terdaftar: ' . 
                        ($item->assign_date ? date('d M Y', strtotime($item->assign_date)) : 'Tidak diketahui') . '</small></div>',
                    'kelurahan' => '<small>' . ($satpen->kelurahan ?? 'Tidak ada') . '</small>',
                    'kecamatan' => '<small>' . ($satpen->kecamatan ?? 'Tidak ada') . '</small>',
                    'kabupaten' => '<small>' . ($satpen->kabupaten->nama_kab ?? 'Tidak ada') . '</small>',
                    'provinsi' => '<small>' . ($satpen->provinsi->nm_prov ?? 'Tidak ada') . '</small>',
                    'aksi' => '<button class="btn btn-outline-danger btn-sm delete-btn" data-id="' . $item->id . '" title="Hapus dari naungan">
                        <i class="ti ti-trash"></i>
                    </button>'
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteSekolahNaungan($id)
    {
        try {
            $npypSatpen = NPYPSatpen::findOrFail($id);
            $npypSatpen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sekolah naungan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus sekolah naungan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addSekolahNaungan(Request $request)
    {
        try {
            // Get NPYP data for super admin
            $npyp = $this->npyp();
            
            if (!$npyp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data NPYP belum ada. Silakan lengkapi data NPYP terlebih dahulu.'
                ], 400);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'selected_satpen' => 'required|array|min:1',
                'selected_satpen.*' => 'required|integer|exists:satpen,id_satpen'
            ], [
                'selected_satpen.required' => 'Silakan pilih minimal satu sekolah',
                'selected_satpen.array' => 'Data sekolah tidak valid',
                'selected_satpen.min' => 'Silakan pilih minimal satu sekolah',
                'selected_satpen.*.exists' => 'Sekolah yang dipilih tidak valid'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $selectedSatpen = $request->selected_satpen;
            $addedCount = 0;
            $skippedCount = 0;
            $skippedSchools = [];

            foreach ($selectedSatpen as $idSatpen) {
                // Check if this satpen is already added to this NPYP
                $exists = NPYPSatpen::where('id_npyp', $npyp->id_npyp)
                                   ->where('id_satpen', $idSatpen)
                                   ->exists();

                if (!$exists) {
                    NPYPSatpen::create([
                        'id_npyp' => $npyp->id_npyp,
                        'id_satpen' => $idSatpen,
                        'assign_date' => now()->format('Y-m-d')
                    ]);
                    $addedCount++;
                } else {
                    // Get school name for skipped message
                    $satpen = Satpen::find($idSatpen);
                    if ($satpen) {
                        $skippedSchools[] = $satpen->nm_satpen;
                    }
                    $skippedCount++;
                }
            }

            $message = "{$addedCount} sekolah berhasil ditambahkan";
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} sekolah sudah ada sebelumnya";
                if (count($skippedSchools) > 0) {
                    $message .= " (" . implode(', ', array_slice($skippedSchools, 0, 3));
                    if (count($skippedSchools) > 3) {
                        $message .= " dan " . (count($skippedSchools) - 3) . " lainnya";
                    }
                    $message .= ")";
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'added' => $addedCount,
                'skipped' => $skippedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexNpypWilayah() 
    {
        if (in_array(auth()->user()->role, ["admin wilayah"])) {
            return $this->indexNpyp();
        }

        return view('admin.npyp.wilayah');
    }

    public function getNpypWilayahData(Request $request)
    {
        try {
            // Base query for NPYP with id_pw not null (wilayah data)
            $query = NPYP::with('pengurusWilayah')
                ->whereNotNull('id_pw');

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('nomor_npyp', 'LIKE', "%{$search}%")
                      ->orWhere('nama_npyp', 'LIKE', "%{$search}%")
                      ->orWhere('nama_operator', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_operator', 'LIKE', "%{$search}%")
                      ->orWhereHas('pengurusWilayah', function($pw) use ($search) {
                          $pw->where('nm_prov', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Get total records before pagination
            $totalRecords = NPYP::whereNotNull('id_pw')->count();
            $filteredRecords = $query->count();

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            // Handle export all data
            if ($length == -1) {
                // Don't limit for export
            } else {
                $query->offset($start)->limit($length);
            }

            // Order
            if ($request->has('order')) {
                $columns = ['id_npyp', 'nomor_npyp', 'nama_npyp', 'nama_operator', 'nomor_operator', 'wilayah'];
                $orderColumn = $columns[$request->order[0]['column']] ?? 'nomor_npyp';
                $orderDir = $request->order[0]['dir'] ?? 'asc';

                if ($orderColumn === 'wilayah') {
                    $query->join('provinsi', 'npyp.id_pw', '=', 'provinsi.id_prov')
                          ->orderBy('provinsi.nm_prov', $orderDir)
                          ->select('npyp.*');
                } else {
                    $query->orderBy($orderColumn, $orderDir);
                }
            } else {
                $query->orderBy('nomor_npyp', 'asc');
            }

            $data = $query->get();

            $result = [];
            foreach ($data as $index => $item) {
                $wilayahName = $item->pengurusWilayah->nm_prov ?? 'Provinsi tidak ditemukan';
                $wilayahLink = $item->pengurusWilayah
                    ? '<div><a href="' . route('a.wilayah.detail', $item->pengurusWilayah->id_prov) . '"><i class="ti ti-link me-1"></i>' . $wilayahName . '</a></div>'
                    : '<div>' . $wilayahName . '</div>';

                $result[] = [
                    'no' => '<div class="text-center fw-bold">' . ($length == -1 ? ($index + 1) : ($start + $index + 1)) . '</div>',
                    'id_npyp' => $item->id_npyp,
                    'nomor_npyp' => '<div class="fw-bold">' . ($item->nomor_npyp ?? '<span class="text-muted">-</span>') . '</div>',
                    'nama_npyp' => $item->nama_npyp ?? '-',
                    'nama_operator' => '<div>' . ($item->nama_operator ?? '<span class="text-muted">-</span>') . '</div>',
                    'nomor_operator' => '<div>' . ($item->nomor_operator ?? '<span class="text-muted">-</span>') . '</div>',
                    'wilayah' => $wilayahLink
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getSatpenByNpyp($npypId)
    {
        try {
            $npyp = NPYP::find($npypId);

            if (!$npyp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data NPYP tidak ditemukan'
                ], 404);
            }

            // Get all satpen under this NPYP
            $satpenList = NPYPSatpen::with(['satpen.jenjang', 'satpen.kabupaten', 'satpen.provinsi'])
                ->where('id_npyp', $npypId)
                ->get();

            $data = [];
            $stats = [
                'total' => 0,
                'ra_tk' => 0,
                'mi_sd' => 0,
                'mts_smp_ma_sma' => 0
            ];

            foreach ($satpenList as $index => $item) {
                if ($item->satpen) {
                    $satpen = $item->satpen;
                    $jenjang = $satpen->jenjang ? $satpen->jenjang->nm_jenjang : '';

                    // Count by jenjang
                    $stats['total']++;
                    if (in_array($jenjang, ['RA', 'TK'])) {
                        $stats['ra_tk']++;
                    } elseif (in_array($jenjang, ['MI', 'SD'])) {
                        $stats['mi_sd']++;
                    } elseif (in_array($jenjang, ['MTs', 'SMP', 'MA', 'SMA', 'SMK'])) {
                        $stats['mts_smp_ma_sma']++;
                    }

                    $data[] = [
                        'no' => $index + 1,
                        'id_satpen' => $satpen->id_satpen,
                        'npsn' => $satpen->npsn ?? '-',
                        'no_registrasi' => $satpen->no_registrasi ?? '-',
                        'nama_satpen' => $satpen->nm_satpen ?? '-',
                        'jenjang' => $jenjang ?: '-',
                        'kabupaten' => $satpen->kabupaten ? $satpen->kabupaten->nama_kab : '-',
                        'alamat' => ($satpen->kelurahan ? $satpen->kelurahan . ', ' : '') .
                                   ($satpen->kecamatan ? $satpen->kecamatan : '-')
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'statistics' => $stats,
                'npyp_name' => $npyp->nama_npyp
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexNpypCabang()
    {
        if (in_array(auth()->user()->role, ["admin cabang"])) {
            return $this->indexNpyp();
        }

        return view('admin.npyp.cabang');
    }

    public function getNpypCabangData(Request $request)
    {
        try {
            // Base query for NPYP with id_pc not null (cabang data)
            $query = NPYP::with(['pengurusCabang.prov'])
                ->whereHas("pengurusCabang.prov", function ($query) {
                    $query->where(request()->specificFilter);
                });

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('nomor_npyp', 'LIKE', "%{$search}%")
                      ->orWhere('nama_npyp', 'LIKE', "%{$search}%")
                      ->orWhere('nama_operator', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_operator', 'LIKE', "%{$search}%")
                      ->orWhereHas('pengurusCabang', function($pc) use ($search) {
                          $pc->where('nama_pc', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('pengurusCabang.prov', function($prov) use ($search) {
                          $prov->where('nm_prov', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Get total records before pagination
            $totalRecords = NPYP::whereHas("pengurusCabang.prov", function ($query) {
                    $query->where(request()->specificFilter);
                })->count();
            $filteredRecords = $query->count();

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            
            // Handle export all data
            if ($length == -1) {
                // Don't limit for export
            } else {
                $query->offset($start)->limit($length);
            }

            // Order
            if ($request->has('order')) {
                $columns = ['id_npyp', 'nomor_npyp', 'nama_npyp', 'nama_operator', 'nomor_operator', 'cabang', 'wilayah'];
                $orderColumn = $columns[$request->order[0]['column']] ?? 'nomor_npyp';
                $orderDir = $request->order[0]['dir'] ?? 'asc';
                
                if ($orderColumn === 'cabang') {
                    $query->join('pengurus_cabang', 'npyp.id_pc', '=', 'pengurus_cabang.id_pc')
                          ->orderBy('pengurus_cabang.nama_pc', $orderDir)
                          ->select('npyp.*');
                } elseif ($orderColumn === 'wilayah') {
                    $query->join('pengurus_cabang', 'npyp.id_pc', '=', 'pengurus_cabang.id_pc')
                          ->join('provinsi', 'pengurus_cabang.id_prov', '=', 'provinsi.id_prov')
                          ->orderBy('provinsi.nm_prov', $orderDir)
                          ->select('npyp.*');
                } else {
                    $query->orderBy($orderColumn, $orderDir);
                }
            } else {
                $query->orderBy('nomor_npyp', 'asc');
            }

            $data = $query->get();

            $result = [];
            foreach ($data as $index => $item) {
                $cabangName = $item->pengurusCabang ? $item->pengurusCabang->nama_pc : 'Cabang tidak ditemukan';
                $wilayahName = ($item->pengurusCabang && $item->pengurusCabang->prov)
                    ? $item->pengurusCabang->prov->nm_prov
                    : 'Provinsi tidak ditemukan';

                $cabangLink = $item->pengurusCabang
                    ? '<div><a href="' . route('a.cabang.detail', $item->id_pc) . '"><i class="ti ti-link me-1"></i>' . $cabangName . '</a></div>'
                    : '<div>' . $cabangName . '</div>';

                $wilayahLink = ($item->pengurusCabang && $item->pengurusCabang->prov)
                    ? '<div><a href="' . route('a.wilayah.detail', $item->pengurusCabang->id_prov) . '"><i class="ti ti-link me-1"></i>' . $wilayahName . '</a></div>'
                    : '<div>' . $wilayahName . '</div>';

                $result[] = [
                    'no' => '<div class="text-center fw-bold">' . ($length == -1 ? ($index + 1) : ($start + $index + 1)) . '</div>',
                    'id_npyp' => $item->id_npyp,
                    'nomor_npyp' => '<div class="fw-bold">' . ($item->nomor_npyp ?? '<span class="text-muted">-</span>') . '</div>',
                    'nama_npyp' => $item->nama_npyp ?? '-',
                    'nama_operator' => '<div>' . ($item->nama_operator ?? '<span class="text-muted">-</span>') . '</div>',
                    'nomor_operator' => '<div>' . ($item->nomor_operator ?? '<span class="text-muted">-</span>') . '</div>',
                    'cabang' => $cabangLink,
                    'wilayah' => $wilayahLink
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display PTK National Summary
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function rekapPtkNasional(Request $request)
    {
        try {
            // Fixed query with correct relationships
            $query = PTK::with([
                'satpen',
                'npyp'
            ])
            ->whereNotNull('id_satpen'); // Make sure we have satpen data

            // Apply filters
            $this->applyPtkFilters($query, $request);

            // Handle Excel export
            if ($request->has('export') && $request->export === 'excel') {
                return $this->exportPtkExcel($query->get());
            }

            // Get statistics
            $statistics = $this->getPtkStatistics();

            // Determine items per page - use smaller default to force pagination for testing
            $perPage = (int) $request->input('per_page', 10); // Changed to 5 for testing pagination
            $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

            // Paginate results
            $ptkData = $query->paginate($perPage)->appends($request->query());

            // Get filter data
            $provinsi = Provinsi::orderBy('nm_prov')->get();

            // Debug: uncomment to check if we have data
            // dd($ptkData->total(), $ptkData->count(), $ptkData->hasPages(), $ptkData->items());

            return view('admin.npyp.rekapPtkNasional', compact(
                'ptkData',
                'provinsi'
            ) + $statistics);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat data PTK: ' . $e->getMessage());
        }
    }

    /**
     * Apply filters to PTK query
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @return void
     */
    private function applyPtkFilters($query, Request $request)
    {
        // Filter by Province
        if ($request->filled('provinsi_id')) {
            $query->whereHas('satpen.provinsi', function($q) use ($request) {
                $q->where('id_prov', $request->provinsi_id);
            });
        }

        // Filter by District
        if ($request->filled('cabang_id')) {
            $query->whereHas('satpen.cabang', function($q) use ($request) {
                $q->where('id_pc', $request->cabang_id);
            });
        }

        // Filter by Submission Status
        if ($request->filled('status_pengajuan')) {
            $statusMapping = [
                'disetujui' => ['approve', 'dikeluarkan'],
                'menunggu' => ['verifikasi', 'proses'],
                'ditolak' => ['revisi']
            ];

            if (isset($statusMapping[$request->status_pengajuan])) {
                $query->whereIn('status_ajuan', $statusMapping[$request->status_pengajuan]);
            }
        }

        // Filter by PTK Type
        if ($request->filled('jenis_ptk')) {
            $query->where('jenis_ptk', $request->jenis_ptk);
        }

        // Add search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_ptk', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%")
                  ->orWhereHas('satpen', function($sq) use ($search) {
                      $sq->where('nm_satpen', 'LIKE', "%{$search}%")
                        ->orWhere('no_registrasi', 'LIKE', "%{$search}%");
                  });
            });
        }
    }

    /**
     * Get PTK statistics for dashboard
     * 
     * @return array
     */
    private function getPtkStatistics()
    {
        $totalPtk = PTK::count();
        
        $ptkDisetujui = PTK::where('status_ajuan', 'approve')->count();
        
        $ptkMenunggu = PTK::whereIn('status_ajuan', ['verifikasi', 'proses'])->count();
        
        $ptkDitolak = PTK::where('status_ajuan', 'revisi')->count();
        
        $ptkDikeluarkan = PTK::where('status_ajuan', 'dikeluarkan')->count();

        return [
            'totalPtk' => $totalPtk,
            'ptkDisetujui' => $ptkDisetujui,
            'ptkMenunggu' => $ptkMenunggu,
            'ptkDitolak' => $ptkDitolak,
            'ptkDikeluarkan' => $ptkDikeluarkan
        ];
    }

    /**
     * Export PTK data to Excel
     * 
     * @param \Illuminate\Database\Eloquent\Collection $ptkData
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function exportPtkExcel($ptkData)
    {
        $exportData = $ptkData->map(function ($ptk, $index) {
            return [
                'No' => $index + 1,
                'No Registrasi' => $ptk->satpen->no_registrasi ?? '-',
                'No NPYP' => $ptk->npyp->nomor_npyp ?? '-',
                'Nama Satpen' => $ptk->satpen->nm_satpen ?? '-',
                'Kabupaten/Kota' => $ptk->satpen->kabupaten->nama_kab ?? '-',
                'Provinsi' => $ptk->satpen->provinsi->nm_prov ?? '-',
                'Nama PTK' => $ptk->nama_ptk ?? '-',
                'NIK' => $ptk->nik ?? '-',
                'Jenis PTK' => $ptk->jenis_ptk ?? '-',
                'Status Kepegawaian' => $ptk->status_kepegawaian ?? '-',
                'Status Pengajuan' => $this->getStatusPengajuanLabel($ptk->status_ajuan),
                'Petugas Approval' => $ptk->npyp->nama_operator ?? '-',
                'Catatan' => $ptk->keterangan_revisi ?? '-',
                'Tanggal Dibuat' => $ptk->created_at ? $ptk->created_at->format('d/m/Y H:i') : '-'
            ];
        });

        $filename = 'rekap_ptk_nasional_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new class($exportData) implements \Maatwebsite\Excel\Concerns\FromCollection {
            private $data;
            
            public function __construct($data) {
                $this->data = $data;
            }
            
            public function collection() {
                return collect($this->data);
            }
        }, $filename);
    }

    /**
     * Get readable status label
     *
     * @param string $status
     * @return string
     */
    private function getStatusPengajuanLabel($status)
    {
        $labels = [
            'verifikasi' => 'Menunggu Verifikasi',
            'revisi' => 'Perlu Revisi',
            'proses' => 'Dalam Proses',
            'approve' => 'Disetujui',
            'dikeluarkan' => 'SK Dikeluarkan'
        ];

        return $labels[$status] ?? 'Status Tidak Diketahui';
    }

    /**
     * Get PTK detail for modal
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPtkDetail($id)
    {
        try {
            $ptk = PTK::with([
                'satpen.jenjang',
                'satpen.provinsi',
                'satpen.kabupaten',
                'npyp'
            ])->find($id);

            if (!$ptk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data PTK tidak ditemukan'
                ], 404);
            }

            $data = [
                'id' => $ptk->id,
                // Identitas PTK
                'nik' => $ptk->nik,
                'nama_ptk' => $ptk->nama_ptk,
                'tempat_lahir' => $ptk->tempat_lahir,
                'tanggal_lahir' => $ptk->tanggal_lahir ? $ptk->tanggal_lahir->format('Y-m-d') : null,
                'jenis_kelamin' => $ptk->jenis_kelamin,
                'nama_ibu' => $ptk->nama_ibu,
                'agama' => $ptk->agama,
                'kebutuhan_khusus' => $ptk->kebutuhan_khusus,
                'status_perkawinan' => $ptk->status_perkawinan,
                'email' => $ptk->email,
                'kabupaten_kota' => $ptk->kabupaten_kota,
                'kecamatan' => $ptk->kecamatan,
                'desa_kelurahan' => $ptk->desa_kelurahan,
                'alamat' => $ptk->alamat,
                'kode_pos' => $ptk->kode_pos,

                // Informasi Kepegawaian
                'jenis_ptk' => $ptk->jenis_ptk,
                'status_kepegawaian' => $ptk->status_kepegawaian,
                'nip' => $ptk->nip,
                'lembaga_pengangkat' => $ptk->lembaga_pengangkat,
                'no_sk_pengangkatan' => $ptk->no_sk_pengangkatan,
                'tmt_pengangkatan' => $ptk->tmt_pengangkatan ? $ptk->tmt_pengangkatan->format('Y-m-d') : null,
                'sumber_gaji' => $ptk->sumber_gaji,
                'lisensi_kepala_sekolah' => $ptk->lisensi_kepala_sekolah,

                // Penugasan
                'nomor_surat_tugas' => $ptk->nomor_surat_tugas,
                'tanggal_surat_tugas' => $ptk->tanggal_surat_tugas ? $ptk->tanggal_surat_tugas->format('Y-m-d') : null,
                'tmt_tugas' => $ptk->tmt_tugas ? $ptk->tmt_tugas->format('Y-m-d') : null,
                'upload_sk' => $ptk->upload_sk,

                // Status Pengajuan & Timeline
                'status_ajuan' => $ptk->status_ajuan,
                'tanggal_verifikasi' => $ptk->tanggal_verifikasi ? $ptk->tanggal_verifikasi->format('Y-m-d H:i:s') : null,
                'tanggal_approve' => $ptk->tanggal_approve ? $ptk->tanggal_approve->format('Y-m-d H:i:s') : null,
                'tanggal_dikeluarkan' => $ptk->tanggal_dikeluarkan ? $ptk->tanggal_dikeluarkan->format('Y-m-d H:i:s') : null,
                'verifikator_id' => $ptk->verifikator_id,
                'approver_id' => $ptk->approver_id,
                'keterangan_revisi' => $ptk->keterangan_revisi,
                'nomor_sk_keluar' => $ptk->nomor_sk_keluar,
                'created_at' => $ptk->created_at ? $ptk->created_at->format('Y-m-d H:i:s') : null,

                // Informasi Satpen
                'satpen' => [
                    'nama' => $ptk->satpen->nm_satpen ?? null,
                    'no_registrasi' => $ptk->satpen->no_registrasi ?? null,
                    'npsn' => $ptk->satpen->npsn ?? null,
                    'jenjang' => $ptk->satpen->jenjang->nm_jenjang ?? null,
                    'alamat' => $ptk->satpen->alamat ?? null,
                    'kecamatan' => $ptk->satpen->kecamatan ?? null,
                    'kabupaten' => [
                        'nama' => $ptk->satpen->kabupaten->nama_kab ?? null,
                        'provinsi' => [
                            'nama' => $ptk->satpen->provinsi->nm_prov ?? null
                        ]
                    ]
                ],

                // Informasi NPYP
                'npyp' => [
                    'nomor_npyp' => $ptk->npyp->nomor_npyp ?? null,
                    'nama_npyp' => $ptk->npyp->nama_npyp ?? null,
                    'nama_operator' => $ptk->npyp->nama_operator ?? null,
                    'nomor_operator' => $ptk->npyp->nomor_operator ?? null
                ],

                'documents' => []
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail PTK: ' . $e->getMessage()
            ], 500);
        }
    }

}
