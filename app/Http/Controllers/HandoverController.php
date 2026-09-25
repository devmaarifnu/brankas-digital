<?php

namespace App\Http\Controllers;

use App\Models\RecordOfHandover;
use App\Models\SuratTanah;
use App\Models\AktaNotaris;
use App\Models\DataAsetLembaga;
use App\Models\SuratKendaraan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RecordOfHandoverExport;
class HandoverController extends Controller
{
    public function index(Request $request)
    {
        $query = RecordOfHandover::with('user');

        // Filter Enumerasi: Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Enumerasi: Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } elseif ($request->filled('status_handover')) {
            $query->where('status', $request->status_handover);
        }

        // Pencarian Free-Text (Keyword nama dokumen, pihak terkait, bank, dll)
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function($w) use ($q) {
                $w->where('nama_dokumen', 'like', "%{$q}%")
                  ->orWhere('nama_peminjam', 'like', "%{$q}%")
                  ->orWhere('no_telp_peminjam', 'like', "%{$q}%")
                  ->orWhere('nama_bank', 'like', "%{$q}%")
                  ->orWhere('penanggung_agunan', 'like', "%{$q}%")
                  ->orWhere('nama_penerima', 'like', "%{$q}%")
                  ->orWhere('catatan', 'like', "%{$q}%")
                  ->orWhere('tgl_serahterima', 'like', "%{$q}%");
            });
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('handover.index', compact('records'))->with('title', 'Record of Transfer');
    }

    public function getItemsByKategori(Request $request)
    {
        $kategori = $request->get('kategori');
        $status = $request->get('status'); // 'Dipinjam', 'Diagunkan', 'Dihibahkan', 'Dikembalikan', or null
        $items = collect();

        if ($kategori === 'Arsip Surat Tanah') {
            $query = SuratTanah::query();
            if ($status === 'Dikembalikan') {
                $query->where(function($q) {
                    $q->whereIn('status_handover', ['Dipinjam', 'Diagunkan'])
                      ->orWhere('warna_merah', true);
                });
            } elseif (in_array($status, ['Dipinjam', 'Diagunkan', 'Dihibahkan'])) {
                $query->where(function($q) {
                    $q->whereNotIn('status_handover', ['Dipinjam', 'Diagunkan'])
                      ->where('warna_merah', false);
                });
            }
            $items = $query->get()->map(function($i) {
                $statusTag = $i->warna_merah ? ' [Sedang ' . ($i->status_handover ?: 'Dipinjam') . ']' : '';
                return [
                    'id' => $i->id,
                    'nama_dokumen' => ($i->nama_sertifikat ?: ($i->nama_dokumen ?: 'Surat Tanah')) . ' (' . ($i->nomor_sertifikat ?: '-') . ')' . $statusTag,
                    'status_handover' => $i->status_handover,
                    'is_borrowed' => (bool)$i->warna_merah,
                ];
            });
        } elseif ($kategori === 'Akta Notaris') {
            $query = AktaNotaris::query();
            if ($status === 'Dikembalikan') {
                $query->where(function($q) {
                    $q->whereIn('status_handover', ['Dipinjam', 'Diagunkan'])
                      ->orWhere('warna_merah', true);
                });
            } elseif (in_array($status, ['Dipinjam', 'Diagunkan', 'Dihibahkan'])) {
                $query->where(function($q) {
                    $q->whereNotIn('status_handover', ['Dipinjam', 'Diagunkan'])
                      ->where('warna_merah', false);
                });
            }
            $items = $query->get()->map(function($i) {
                $statusTag = $i->warna_merah ? ' [Sedang ' . ($i->status_handover ?: 'Dipinjam') . ']' : '';
                return [
                    'id' => $i->id,
                    'nama_dokumen' => ($i->nama_dokumen ?: 'Akta Notaris') . ' (' . ($i->nomor_dokumen ?: ($i->nomor_akta ?: '-')) . ')' . $statusTag,
                    'status_handover' => $i->status_handover,
                    'is_borrowed' => (bool)$i->warna_merah,
                ];
            });
        } elseif ($kategori === 'Data Aset Lembaga') {
            $query = DataAsetLembaga::query();
            if ($status === 'Dikembalikan') {
                $query->where(function($q) {
                    $q->whereIn('status_handover', ['Dipinjam', 'Diagunkan'])
                      ->orWhere('warna_merah', true);
                });
            } elseif (in_array($status, ['Dipinjam', 'Diagunkan', 'Dihibahkan'])) {
                $query->where(function($q) {
                    $q->whereNotIn('status_handover', ['Dipinjam', 'Diagunkan'])
                      ->where('warna_merah', false);
                });
            }
            $items = $query->get()->map(function($i) {
                $statusTag = $i->warna_merah ? ' [Sedang ' . ($i->status_handover ?: 'Dipinjam') . ']' : '';
                return [
                    'id' => $i->id,
                    'nama_dokumen' => ($i->nama_barang ?: ($i->nama_aset ?: 'Aset')) . ' [' . ($i->nomor_registrasi ?: '-') . ']' . $statusTag,
                    'status_handover' => $i->status_handover,
                    'is_borrowed' => (bool)$i->warna_merah,
                ];
            });
        } elseif ($kategori === 'Arsip Surat Kendaraan') {
            $query = SuratKendaraan::query();
            if ($status === 'Dikembalikan') {
                $query->where(function($q) {
                    $q->whereIn('status_handover', ['Dipinjam', 'Diagunkan'])
                      ->orWhere('warna_merah', true);
                });
            } elseif (in_array($status, ['Dipinjam', 'Diagunkan', 'Dihibahkan'])) {
                $query->where(function($q) {
                    $q->whereNotIn('status_handover', ['Dipinjam', 'Diagunkan'])
                      ->where('warna_merah', false);
                });
            }
            $items = $query->get()->map(function($i) {
                $statusTag = $i->warna_merah ? ' [Sedang ' . ($i->status_handover ?: 'Dipinjam') . ']' : '';
                return [
                    'id' => $i->id,
                    'nama_dokumen' => $i->jenis_surat . ' - ' . $i->nama_kendaraan . ($i->no_plat ? ' (' . $i->no_plat . ')' : '') . $statusTag,
                    'status_handover' => $i->status_handover,
                    'is_borrowed' => (bool)$i->warna_merah,
                ];
            });
        }

        return response()->json($items);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat memproses serah terima dokumen.');
        }

        $request->validate([
            'kategori'        => 'required|in:Arsip Surat Tanah,Akta Notaris,Data Aset Lembaga,Arsip Surat Kendaraan',
            'ref_id'          => 'required|integer',
            'nama_dokumen'    => 'required|string',
            'status'          => 'required|in:Dipinjam,Diagunkan,Dihibahkan,Dikembalikan',
            'tgl_serahterima' => 'required|date',
            'file_bukti'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
        ]);

        // Cari item sumber
        $sourceModel = null;
        if ($request->kategori === 'Arsip Surat Tanah') {
            $sourceModel = SuratTanah::find($request->ref_id);
        } elseif ($request->kategori === 'Akta Notaris') {
            $sourceModel = AktaNotaris::find($request->ref_id);
        } elseif ($request->kategori === 'Data Aset Lembaga') {
            $sourceModel = DataAsetLembaga::find($request->ref_id);
        } elseif ($request->kategori === 'Arsip Surat Kendaraan') {
            $sourceModel = SuratKendaraan::find($request->ref_id);
        }

        if (!$sourceModel) {
            return back()->with('error', 'Dokumen/Aset terkait tidak ditemukan dalam database.');
        }

        $isCurrentlyOut = in_array($sourceModel->status_handover, ['Dipinjam', 'Diagunkan']) || $sourceModel->warna_merah;

        // Validasi aturan peralihan status:
        if ($request->status === 'Dikembalikan') {
            if (!$isCurrentlyOut) {
                return back()->with('error', 'Dokumen/Aset ini saat ini berstatus Tersedia (tidak sedang dipinjam atau diagunkan).');
            }
        } else {
            if ($isCurrentlyOut) {
                return back()->with('error', 'Dokumen/Aset ini sedang berstatus "' . ($sourceModel->status_handover ?: 'Dipinjam') . '". Dokumen harus melalui proses pengembalian terlebih dahulu sebelum dapat dipindahtangankan kembali.');
            }
        }

        $data = $request->except('file_bukti');
        $data['user_id'] = auth()->id();

        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destDir = storage_path('app/uploads/handover');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $filename);
            $data['file_bukti'] = 'uploads/handover/' . $filename;
        }

        RecordOfHandover::create($data);

        // Update status dokumen/aset sumber
        $tglFormat = date('d/m/Y', strtotime($request->tgl_serahterima));

        if ($request->status === 'Dikembalikan') {
            $updatePayload = [
                'status_handover' => 'Tersedia',
                'keterangan'      => 'Dokumen Asli Ada (Dikembalikan tgl ' . $tglFormat . ')',
                'warna_merah'     => false,
            ];
            if ($request->kategori === 'Data Aset Lembaga') {
                $updatePayload['posisi_aset'] = 'Kantor';
                $updatePayload['nama_penerima'] = null;
            }
            $sourceModel->update($updatePayload);

            return back()->with('success', 'Pengembalian dokumen/aset berhasil dicatat. Status telah kembali Tersedia (Asli Ada).');
        } else {
            $keteranganUpdate = $request->status . ' (Tgl: ' . $tglFormat . ')';
            $updatePayload = [
                'status_handover' => $request->status,
                'keterangan'      => $keteranganUpdate,
                'warna_merah'     => true,
            ];
            if ($request->kategori === 'Data Aset Lembaga') {
                $updatePayload['posisi_aset'] = $request->status;
                $updatePayload['nama_penerima'] = $request->nama_peminjam ?: ($request->nama_penerima ?: $request->penanggung_agunan);
            }
            $sourceModel->update($updatePayload);

            return back()->with('success', 'Record of Transfer berhasil disimpan. Rekap telah diperbarui otomatis & ditandai merah.');
        }
    }

    public function export(Request $request)
    {
        $query = RecordOfHandover::with('user');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function($w) use ($q) {
                $w->where('nama_dokumen', 'like', "%{$q}%")
                  ->orWhere('nama_peminjam', 'like', "%{$q}%")
                  ->orWhere('no_telp_peminjam', 'like', "%{$q}%")
                  ->orWhere('nama_bank', 'like', "%{$q}%")
                  ->orWhere('penanggung_agunan', 'like', "%{$q}%")
                  ->orWhere('nama_penerima', 'like', "%{$q}%")
                  ->orWhere('catatan', 'like', "%{$q}%")
                  ->orWhere('tgl_serahterima', 'like', "%{$q}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        $filename = 'Rekap-Record-of-Transfer-' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new RecordOfHandoverExport($query), $filename);
    }
}