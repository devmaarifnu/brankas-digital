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
        $status = $request->get('status'); // Status di Record of Transfer: Dipinjam, Diagunkan, Dihibahkan, Dikembalikan
        $items = collect();

        if ($kategori === 'Arsip Surat Tanah') {
            $query = SuratTanah::query();
            // Untuk "Dikembalikan": hanya tampilkan yg sedang dipinjam/diagunkan (warna_merah=true)
            if ($status === 'Dikembalikan') {
                $query->where('warna_merah', true);
            }
            // Untuk Dipinjam/Diagunkan/Dihibahkan: tampilkan SEMUA (sudah tersedia maupun belum)
            $items = $query->orderBy('created_at', 'desc')->get()->map(function($i) {
                $statusTag = $i->warna_merah ? ' [Sedang ' . ($i->status_handover ?: 'Dipinjam') . ']' : '';
                $rawName = ($i->nama_sertifikat ?: ($i->nama_dokumen ?: 'Surat Tanah')) . ' (' . ($i->nomor_sertifikat ?: '-') . ')';
                return [
                    'id' => $i->id,
                    'nama_dokumen' => $rawName,
                    'display_name' => $rawName . $statusTag,
                    'status_handover' => $i->status_handover,
                    'is_borrowed' => (bool)$i->warna_merah,
                ];
            });
        } elseif ($kategori === 'Akta Notaris') {
            $query = AktaNotaris::query();
            if ($status === 'Dikembalikan') {
                $query->where('warna_merah', true);
            }
            $items = $query->orderBy('created_at', 'desc')->get()->map(function($i) {
                $statusTag = $i->warna_merah ? ' [Sedang ' . ($i->status_handover ?: 'Dipinjam') . ']' : '';
                $rawName = ($i->nama_dokumen ?: 'Akta Notaris') . ' (' . ($i->nomor_dokumen ?: ($i->nomor_akta ?: '-')) . ')';
                return [
                    'id' => $i->id,
                    'nama_dokumen' => $rawName,
                    'display_name' => $rawName . $statusTag,
                    'status_handover' => $i->status_handover,
                    'is_borrowed' => (bool)$i->warna_merah,
                ];
            });
        } elseif ($kategori === 'Data Aset Lembaga') {
            $query = DataAsetLembaga::query();
            if ($status === 'Dikembalikan') {
                $query->where('warna_merah', true);
            }
            $items = $query->orderBy('created_at', 'desc')->get()->map(function($i) {
                $statusTag = $i->warna_merah ? ' [Sedang ' . ($i->status_handover ?: 'Dipinjam') . ']' : '';
                $rawName = ($i->nama_barang ?: ($i->nama_aset ?: 'Aset')) . ' [' . ($i->nomor_registrasi ?: '-') . ']';
                return [
                    'id' => $i->id,
                    'nama_dokumen' => $rawName,
                    'display_name' => $rawName . $statusTag,
                    'status_handover' => $i->status_handover,
                    'is_borrowed' => (bool)$i->warna_merah,
                ];
            });
        } elseif ($kategori === 'Arsip Surat Kendaraan') {
            $query = SuratKendaraan::query();
            if ($status === 'Dikembalikan') {
                $query->where('warna_merah', true);
            }
            $items = $query->orderBy('created_at', 'desc')->get()->map(function($i) {
                $statusTag = $i->warna_merah ? ' [Sedang ' . ($i->status_handover ?: 'Dipinjam') . ']' : '';
                $displayName = $i->jenis_surat . ' - ' . $i->nama_kendaraan . ($i->no_plat ? ' (' . $i->no_plat . ')' : '') . $statusTag;
                return [
                    'id' => $i->id,
                    'nama_dokumen' => $i->jenis_surat . ' - ' . $i->nama_kendaraan . ($i->no_plat ? ' (' . $i->no_plat . ')' : ''),
                    'display_name' => $displayName,
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
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Hanya Super Admin dan Admin yang dapat memproses serah terima dokumen.');
        }

        $request->validate([
            'kategori'        => 'required|in:Arsip Surat Tanah,Akta Notaris,Data Aset Lembaga,Arsip Surat Kendaraan',
            'ref_id'          => 'required|integer',
            'nama_dokumen'    => 'required|string',
            'status'          => 'required|in:Dipinjam,Diagunkan,Dihibahkan,Dikembalikan',
            'tgl_serahterima' => 'required|date',
            'file_bukti'      => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:51200',
        ], [
            'file_bukti.mimes'    => 'Berkas bukti serah terima wajib berformat JPG, JPEG, PNG, WEBP, atau PDF.',
            'file_bukti.max'      => 'Ukuran berkas bukti serah terima tidak boleh melebihi 50 MB.',
            'file_bukti.uploaded' => 'Berkas bukti gagal diunggah. Ukuran file kemungkinan melebihi batas upload server (maksimal 50 MB).',
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

    public function destroy($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Tombol dan aksi hapus hanya dapat dilakukan oleh Super admin.');
        }

        $handover = RecordOfHandover::findOrFail($id);
        $kategori = $handover->kategori;
        $refId = $handover->ref_id;

        // Cari model sumber
        $sourceModel = null;
        if ($kategori === 'Arsip Surat Tanah') {
            $sourceModel = SuratTanah::find($refId);
        } elseif ($kategori === 'Akta Notaris') {
            $sourceModel = AktaNotaris::find($refId);
        } elseif ($kategori === 'Data Aset Lembaga') {
            $sourceModel = DataAsetLembaga::find($refId);
        } elseif ($kategori === 'Arsip Surat Kendaraan') {
            $sourceModel = SuratKendaraan::find($refId);
        }

        // Hapus file bukti jika ada
        if ($handover->file_bukti) {
            if (Storage::exists($handover->file_bukti)) {
                Storage::delete($handover->file_bukti);
            } elseif (file_exists(storage_path('app/' . $handover->file_bukti))) {
                @unlink(storage_path('app/' . $handover->file_bukti));
            } elseif (file_exists(public_path($handover->file_bukti))) {
                @unlink(public_path($handover->file_bukti));
            }
        }

        $handover->delete();

        // Sinkronisasi status dokumen/aset sumber dengan riwayat serah terima terakhir yang tersisa
        if ($sourceModel) {
            $latest = RecordOfHandover::where('kategori', $kategori)
                ->where('ref_id', $refId)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($latest) {
                $tglFormat = date('d/m/Y', strtotime($latest->tgl_serahterima));
                if ($latest->status === 'Dikembalikan') {
                    $payload = [
                        'status_handover' => 'Tersedia',
                        'keterangan'      => 'Dokumen Asli Ada (Dikembalikan tgl ' . $tglFormat . ')',
                        'warna_merah'     => false,
                    ];
                    if ($kategori === 'Data Aset Lembaga') {
                        $payload['posisi_aset'] = 'Kantor';
                        $payload['nama_penerima'] = null;
                    }
                    $sourceModel->update($payload);
                } else {
                    $payload = [
                        'status_handover' => $latest->status,
                        'keterangan'      => $latest->status . ' (Tgl: ' . $tglFormat . ')',
                        'warna_merah'     => true,
                    ];
                    if ($kategori === 'Data Aset Lembaga') {
                        $payload['posisi_aset'] = $latest->status;
                        $payload['nama_penerima'] = $latest->nama_peminjam ?: ($latest->nama_penerima ?: $latest->penanggung_agunan);
                    }
                    $sourceModel->update($payload);
                }
            } else {
                // Tidak ada riwayat serah terima lagi, kembalikan status ke Tersedia
                $payload = [
                    'status_handover' => 'Tersedia',
                    'keterangan'      => 'Dokumen Asli Ada',
                    'warna_merah'     => false,
                ];
                if ($kategori === 'Data Aset Lembaga') {
                    $payload['posisi_aset'] = 'Kantor';
                    $payload['nama_penerima'] = null;
                }
                $sourceModel->update($payload);
            }
        }

        return redirect()->route('handover.index')->with('success', 'Data Record of Transfer berhasil dihapus.');
    }
}
