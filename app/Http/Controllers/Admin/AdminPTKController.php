<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PTK;
use App\Models\PTKStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPTKController extends Controller
{
     protected function specificFilter() {
        $specificFilter = request()->specificFilter;
        
        if (!$specificFilter) {
            return [];
        }
        
        $specificFilter["id_pw"] = @$specificFilter["id_prov"];
        unset($specificFilter["id_prov"]);
        empty($specificFilter["id_pc"]) ? $specificFilter["id_pc"] = @$specificFilter["id_pc"] : null;
        
        // Remove null values
        return array_filter($specificFilter, function($value) {
            return $value !== null;
        });
    }

    public function index()
    {
        return view('admin.npyp.ptk.verifikasi');
    }

    public function getData(Request $request)
    {
        try {
            $status = $request->get('status', 'verifikasi');

            // Base query
            $query = PTK::with(['satpen.kabupaten', 'satpen.provinsi', 'npyp'])
                ->whereHas('npyp', function($q) {
                    $specificFilter = $this->specificFilter();
                    if (!empty($specificFilter)) {
                        $q->where($specificFilter);
                    }
                })
                ->where('status_ajuan', $status);

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('nama_ptk', 'LIKE', "%{$search}%")
                      ->orWhere('nik', 'LIKE', "%{$search}%")
                      ->orWhere('jenis_ptk', 'LIKE', "%{$search}%")
                      ->orWhere('nip', 'LIKE', "%{$search}%")
                      ->orWhereHas('satpen', function($sq) use ($search) {
                          $sq->where('nm_satpen', 'LIKE', "%{$search}%")
                            ->orWhere('no_registrasi', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Get total records before pagination
            $totalRecords = PTK::whereHas('npyp', function($q) {
                    $specificFilter = $this->specificFilter();
                    if (!empty($specificFilter)) {
                        $q->where($specificFilter);
                    }
                })->where('status_ajuan', $status)->count();
            $filteredRecords = $query->count();

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $query->offset($start)->limit($length);

            // Order
            if ($request->has('order')) {
                $columns = ['id', 'no_registrasi', 'nm_satpen', 'kabupaten', 'provinsi', 'nama_ptk', 'nik', 'status_ajuan', 'petugas_approval', 'catatan'];
                $orderColumn = $columns[$request->order[0]['column']] ?? 'id';
                $orderDir = $request->order[0]['dir'] ?? 'desc';

                if (in_array($orderColumn, ['no_registrasi', 'nm_satpen'])) {
                    $query->join('satpen', 'ptk.id_satpen', '=', 'satpen.id_satpen')
                          ->orderBy('satpen.' . $orderColumn, $orderDir)
                          ->select('ptk.*');
                } elseif ($orderColumn === 'kabupaten') {
                    $query->join('satpen', 'ptk.id_satpen', '=', 'satpen.id_satpen')
                          ->join('kabupaten', 'satpen.id_kab', '=', 'kabupaten.id_kab')
                          ->orderBy('kabupaten.nm_kabupaten', $orderDir)
                          ->select('ptk.*');
                } elseif ($orderColumn === 'provinsi') {
                    $query->join('satpen', 'ptk.id_satpen', '=', 'satpen.id_satpen')
                          ->join('provinsi', 'satpen.id_prov', '=', 'provinsi.id_prov')
                          ->orderBy('provinsi.nm_provinsi', $orderDir)
                          ->select('ptk.*');
                } else {
                    $query->orderBy($orderColumn, $orderDir);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $data = $query->get();

            $result = [];
            foreach ($data as $index => $ptk) {
                $result[] = [
                    'no' => '<span class="badge bg-light text-dark">' . ($start + $index + 1) . '</span>',
                    'no_registrasi' => '<span class="fw-bold text-primary">' . ($ptk->satpen->no_registrasi ?? '-') . '</span>',
                    'nama_satpen' => '<div>' .
                        '<h6 class="mb-1">' . ($ptk->satpen->nm_satpen ?? '-') . '</h6>' .
                        '<small class="text-muted">' . ($ptk->satpen->jenjang->nm_jenjang ?? '-') . '</small>' .
                        '</div>',
                    'kabupaten' => $ptk->satpen->kabupaten->nama_kab ?? '-',
                    'provinsi' => $ptk->satpen->provinsi->nm_prov ?? '-',
                    'nama_ptk' => '<div>' .
                        '<h6 class="mb-1">' . $ptk->nama_ptk . '</h6>' .
                        '<small class="text-muted">' . $ptk->jenis_ptk . '</small>' .
                        '</div>',
                    'nik' => '<span class="fw-bold">' . $ptk->nik . '</span>',
                    'status_pengajuan' => $this->getStatusBadge($ptk->status_ajuan),
                    'petugas_approval' => $ptk->npyp->nama_operator ?? '<span class="text-muted">-</span>',
                    'catatan' => $this->formatCatatan($ptk),
                    'aksi' => $this->generateActionButtons($ptk)
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

    private function getStatusBadge($status)
    {
        $statusClass = '';
        $statusText = '';

        switch ($status) {
            case 'verifikasi':
                $statusClass = 'bg-warning text-dark';
                $statusText = 'Verifikasi';
                break;
            case 'revisi':
                $statusClass = 'bg-danger';
                $statusText = 'Revisi';
                break;
            case 'proses':
                $statusClass = 'bg-info';
                $statusText = 'Proses';
                break;
            case 'approve':
                $statusClass = 'bg-success';
                $statusText = 'Approved';
                break;
            case 'dikeluarkan':
                $statusClass = 'bg-primary';
                $statusText = 'Dikeluarkan';
                break;
            default:
                $statusClass = 'bg-secondary';
                $statusText = 'Unknown';
                break;
        }

        return '<span class="badge ' . $statusClass . '">' . $statusText . '</span>';
    }

    private function formatCatatan($ptk)
    {
        $catatan = $ptk->keterangan_revisi ?? $ptk->catatan_verifikator ?? '-';
        if (strlen($catatan) > 50) {
            return '<span title="' . htmlspecialchars($catatan) . '">' . substr($catatan, 0, 47) . '...</span>';
        }
        return $catatan;
    }

    private function generateActionButtons($ptk)
    {
        $buttons = '';

        // Detail button - always available
        $buttons .= '<button class="btn btn-outline-info btn-sm me-1 btn-detail" data-id="' . $ptk->id . '" title="Lihat Detail">
            <i class="ti ti-eye"></i>
        </button>';

        // Action buttons based on status
        switch ($ptk->status_ajuan) {
            case 'verifikasi':
                $buttons .= '<button class="btn btn-outline-success btn-sm me-1 btn-action" data-id="' . $ptk->id . '" data-action="terima" title="Terima">
                    <i class="ti ti-check"></i>
                </button>';
                $buttons .= '<button class="btn btn-outline-danger btn-sm btn-action" data-id="' . $ptk->id . '" data-action="tolak" title="Tolak">
                    <i class="ti ti-x"></i>
                </button>';
                break;

            case 'revisi':
                // No actions available in revisi status - waiting for user to resubmit
                break;

            case 'proses':
                $buttons .= '<button class="btn btn-outline-success btn-sm me-1 btn-action" data-id="' . $ptk->id . '" data-action="approve" title="Approve">
                    <i class="ti ti-check"></i>
                </button>';
                $buttons .= '<button class="btn btn-outline-danger btn-sm btn-action" data-id="' . $ptk->id . '" data-action="tolak" title="Tolak">
                    <i class="ti ti-x"></i>
                </button>';
                break;

            case 'approve':
                $buttons .= '<button class="btn btn-outline-primary btn-sm btn-action" data-id="' . $ptk->id . '" data-action="keluarkan" title="Keluarkan PTK">
                    <i class="ti ti-cut"></i>
                </button>';
                break;

            case 'dikeluarkan':
                if ($ptk->nomor_sk_keluar) {
                    $buttons .= '<button class="btn btn-outline-success btn-sm" onclick="downloadSK(' . $ptk->id . ')" title="Download SK">
                        <i class="ti ti-download"></i>
                    </button>';
                }
                break;
        }

        return $buttons;
    }

    public function detail($id)
    {
        try {
            $ptk = PTK::with(['satpen.kabupaten', 'satpen.provinsi', 'npyp'])
                ->whereHas('npyp', function($q) {
                    $specificFilter = $this->specificFilter();
                    if (!empty($specificFilter)) {
                        $q->where($specificFilter);
                    }
                })
                ->findOrFail($id);

            // Get status history
            $statusHistory = PTKStatusHistory::where('ptk_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            $html = view('admin.npyp.ptk._detail', compact('ptk', 'statusHistory'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'ptk_status' => $ptk->status_ajuan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data PTK tidak ditemukan'
            ], 404);
        }
    }

    public function action(Request $request)
    {
        try {
            $ptkId = $request->input('ptk_id');
            $action = $request->input('action_type');
            $notes = $request->input('notes');
            $nomorSK = $request->input('nomor_sk');
            $tanggalSK = $request->input('tanggal_sk');

            $ptk = PTK::whereHas('npyp', function($q) {
                    $specificFilter = $this->specificFilter();
                    if (!empty($specificFilter)) {
                        $q->where($specificFilter);
                    }
                })->findOrFail($ptkId);
            $user = Auth::user();

            DB::beginTransaction();

            $oldStatus = $ptk->status_ajuan;
            $newStatus = '';
            $message = '';
            $historyNotes = '';

            switch ($action) {
                case 'terima':
                    if ($ptk->status_ajuan == 'verifikasi') {
                        $ptk->tanggal_verifikasi = now();
                        $newStatus = 'proses';
                        $message = 'PTK berhasil diterima dan masuk tahap proses';
                        $historyNotes = 'PTK diterima untuk diproses oleh ' . $user->name;
                    } else {
                        throw new \Exception('Status PTK tidak valid untuk aksi ini');
                    }
                    break;

                case 'tolak':
                    $newStatus = 'revisi';
                    $message = 'PTK ditolak dan dikembalikan untuk revisi';
                    $historyNotes = 'PTK ditolak dan dikembalikan untuk revisi oleh ' . $user->name;

                    // Update revision notes
                    $ptk->keterangan_revisi = $notes;
                    $ptk->tanggal_revisi = now();
                    break;

                case 'proses':
                    if ($ptk->status_ajuan == 'verifikasi') {
                        $ptk->tanggal_proses = now();

                        $newStatus = 'proses';
                        $message = 'PTK berhasil diproses';
                        $historyNotes = 'PTK diproses oleh ' . $user->name;
                    } else {
                        throw new \Exception('Status PTK tidak valid untuk aksi ini');
                    }
                    break;

                case 'approve':
                    if ($ptk->status_ajuan == 'proses') {
                        $ptk->tanggal_proses = now();
                        $ptk->tanggal_approve = now();

                        $newStatus = 'approve';
                        $message = 'PTK berhasil disetujui';
                        $historyNotes = 'PTK disetujui oleh ' . $user->name;
                    } else {
                        throw new \Exception('Status PTK tidak valid untuk aksi ini');
                    }
                    break;

                case 'keluarkan':
                    if ($ptk->status_ajuan == 'approve') {
                        $ptk->tanggal_dikeluarkan = now();
                        $ptk->nomor_sk_keluar = $nomorSK;
                        
                        $newStatus = 'dikeluarkan';
                        $message = 'SK PTK berhasil dikeluarkan';
                        $historyNotes = 'SK PTK dikeluarkan oleh ' . $user->name;
                    } else {
                        throw new \Exception('Status PTK tidak valid untuk aksi ini');
                    }
                    break;

                default:
                    throw new \Exception('Aksi tidak dikenali');
            }

            // Update PTK status
            $ptk->status_ajuan = $newStatus;
            if ($notes && $action != 'tolak') {
                $ptk->catatan_verifikator = $notes;
            }
            $ptk->save();

            // Create status history
            PTKStatusHistory::createHistory(
                $ptk->id,
                $oldStatus,
                $newStatus,
                $historyNotes . ($notes ? '. Catatan: ' . $notes : '')
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function statistics()
    {
        try {
            $specificFilter = $this->specificFilter();
            
            $ptk = PTK::whereHas('npyp', function($q) use ($specificFilter) {
                    if (!empty($specificFilter)) {
                        $q->where($specificFilter);
                    }
                });
            $counts = [
                'verifikasi' => (clone $ptk)->where('status_ajuan', 'verifikasi')->count(),
                'revisi' => (clone $ptk)->where('status_ajuan', 'revisi')->count(),
                'proses' => (clone $ptk)->where('status_ajuan', 'proses')->count(),
                'approve' => (clone $ptk)->where('status_ajuan', 'approve')->count(),
                'dikeluarkan' => (clone $ptk)->where('status_ajuan', 'dikeluarkan')->count(),
                'total' => (clone $ptk)->count()
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
}