<?php

namespace App\Http\Controllers;

use App\Models\SuratTanah;
use App\Models\AktaNotaris;
use App\Models\DataAsetLembaga;
use App\Models\SuratKendaraan;
use App\Exports\SuratTanahExport;
use App\Exports\AktaNotarisExport;
use App\Exports\DataAsetExport;
use App\Exports\SuratKendaraanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class BrangkasController extends Controller
{
    // ==========================================
    // ARSIP SURAT TANAH
    // ==========================================
    public function suratTanah(Request $request)
    {
        $query = SuratTanah::query();

        $data = $this->applyRecordFilters($query, $request, 'jenis_sertifikat')
                      ->with(['user', 'handovers.user'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();

        // Data for filter dropdowns (sesuai formulir input dan database)
        $defaultJenis = ['SHM', 'Wakaf', 'Hibah', 'SHGB', 'SHGU', 'Hak Guna Pakai'];
        $dbJenis = SuratTanah::whereNotNull('jenis_sertifikat')->where('jenis_sertifikat', '!=', '')->distinct()->pluck('jenis_sertifikat')->toArray();
        $jenisList = collect(array_values(array_filter(array_unique(array_merge($defaultJenis, $dbJenis)))));

        $defaultStatus = ['Tersedia', 'Dipinjam', 'Diagunkan', 'Dihibahkan', 'Dikembalikan'];
        $dbStatus = SuratTanah::whereNotNull('status_handover')->where('status_handover', '!=', '')->distinct()->pluck('status_handover')->toArray();
        $statusList = collect(array_values(array_filter(array_unique(array_merge($defaultStatus, $dbStatus)))));
        $officers = \App\Models\User::select('id_user as id', 'name')->orderBy('name')->get();

        return view('brangkas.surat-tanah.index', compact('data', 'jenisList', 'statusList', 'officers'))
            ->with('title', 'Arsip Surat Tanah');
    }

    public function storeSuratTanah(Request $request)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.surat-tanah')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat menambah data.');
        }
        $request->validate([
            'jenis_sertifikat' => 'required|string|max:100',
            'nomor_sertifikat' => 'required|string|max:100',
            'luas'             => 'required|string|max:50',
            'nama_sertifikat'  => 'required|string|max:255',
            'desa_kelurahan'   => 'nullable|string|max:100',
            'kecamatan'        => 'nullable|string|max:100',
            'kabupaten_kota'   => 'nullable|string|max:100',
            'provinsi'         => 'nullable|string|max:100',
            'file_dokumen'     => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
            'tgl_input'        => 'nullable|date',
            'keterangan'       => 'nullable|string',
        ], [
            'required' => 'Kolom :attribute wajib diisi.',
            'file_dokumen.mimes' => 'Berkas dokumen wajib berformat PDF, JPG, JPEG, PNG, atau WEBP.',
        ]);

        $payload = $request->except('file_dokumen');
        $payload['nama_dokumen'] = $request->nama_sertifikat;
        $payload['atas_nama'] = $request->nama_sertifikat;
        $payload['alamat'] = trim(($request->desa_kelurahan ? $request->desa_kelurahan . ', ' : '') . ($request->kecamatan ? $request->kecamatan . ', ' : '') . ($request->kabupaten_kota ? $request->kabupaten_kota . ', ' : '') . ($request->provinsi ?? ''), ' ,');
        $payload['lokasi'] = $payload['alamat'];
        $payload['tgl_input'] = $request->tgl_input ?? date('Y-m-d');
                $payload['user_id'] = auth()->id();

        // Keterangan khusus "Isi Sendiri"
        if ($request->keterangan === 'Isi Sendiri' && $request->filled('keterangan_custom')) {
            $payload['keterangan'] = $request->keterangan_custom;
        }

        if ($request->jenis_sertifikat === 'Isi Sendiri' && $request->filled('jenis_sertifikat_custom')) {
            $payload['jenis_sertifikat'] = $request->jenis_sertifikat_custom;
        }

        // Tentukan status handover & warna merah
        $statusInfo = $this->resolveStatusFields($payload['keterangan'] ?? 'Tersedia');
        $payload['keterangan'] = $statusInfo['keterangan'];
        $payload['status_handover'] = $statusInfo['status_handover'];
        $payload['warna_merah'] = $statusInfo['warna_merah'];

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destDir = storage_path('app/uploads/surat-tanah');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $filename);
            $payload['file_dokumen'] = 'uploads/surat-tanah/' . $filename;
        }

        SuratTanah::create($payload);

        return redirect()->route('brangkas.surat-tanah')->with('success', 'Data Surat Tanah berhasil ditambahkan.');
    }

    public function editSuratTanah($id)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.surat-tanah')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat mengedit data.');
        }
        $item = SuratTanah::findOrFail($id);
        return view('brangkas.surat-tanah.edit', compact('item'))->with('title', 'Edit Surat Tanah');
    }

    public function updateSuratTanah(Request $request, $id)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.surat-tanah')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat mengedit data.');
        }
        $item = SuratTanah::findOrFail($id);

        $request->validate([
            'luas'         => 'required|string|max:50',
            'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ], [
            'file_dokumen.mimes' => 'Berkas dokumen wajib berformat PDF, JPG, JPEG, PNG, atau WEBP.',
        ]);

        $payload = $request->except(['file_dokumen', 'jenis_sertifikat', 'nomor_sertifikat', 'nama_sertifikat', 'keterangan']);
        $payload['jenis_sertifikat'] = $item->jenis_sertifikat;
        $payload['nomor_sertifikat'] = $item->nomor_sertifikat;
        $payload['nama_sertifikat']  = $item->nama_sertifikat ?: $item->nama_dokumen;
        $payload['nama_dokumen']     = $payload['nama_sertifikat'];
        $payload['atas_nama']        = $payload['nama_sertifikat'];
        $payload['alamat']           = trim(($request->desa_kelurahan ? $request->desa_kelurahan . ', ' : '') . ($request->kecamatan ? $request->kecamatan . ', ' : '') . ($request->kabupaten_kota ? $request->kabupaten_kota . ', ' : '') . ($request->provinsi ?? ''), ' ,');
        $payload['lokasi']           = $payload['alamat'];

        $payload['keterangan']      = $item->keterangan;
        $payload['status_handover'] = $item->status_handover;
        $payload['warna_merah']     = $item->warna_merah;

        if ($request->hasFile('file_dokumen')) {
            if ($item->file_dokumen) {
                if (file_exists(storage_path('app/' . $item->file_dokumen))) {
                    @unlink(storage_path('app/' . $item->file_dokumen));
                } elseif (file_exists(public_path($item->file_dokumen))) {
                    @unlink(public_path($item->file_dokumen));
                }
            }
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destDir = storage_path('app/uploads/surat-tanah');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $filename);
            $payload['file_dokumen'] = 'uploads/surat-tanah/' . $filename;
        }

        $item->update($payload);

        return redirect()->route('brangkas.surat-tanah')->with('success', 'Data Surat Tanah berhasil diperbarui.');
    }

    public function destroySuratTanah($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('brangkas.surat-tanah')->with('error', 'Akses ditolak: Tombol dan aksi hapus hanya dapat dilakukan oleh Super admin.');
        }
        $item = SuratTanah::findOrFail($id);
        if ($item->file_dokumen) {
            if (Storage::exists($item->file_dokumen)) {
                Storage::delete($item->file_dokumen);
            } elseif (File::exists(storage_path('app/' . $item->file_dokumen))) {
                File::delete(storage_path('app/' . $item->file_dokumen));
            } elseif (File::exists(public_path($item->file_dokumen))) {
                File::delete(public_path($item->file_dokumen));
            }
        }
        $item->delete();
        return redirect()->route('brangkas.surat-tanah')->with('success', 'Data Surat Tanah berhasil dihapus.');
    }

    // ==========================================
    // AKTA NOTARIS
    // ==========================================
    public function aktaNotaris(Request $request)
    {
        $query = AktaNotaris::query();

        // Apply unified filters and pagination
        $data = $this->applyRecordFilters($query, $request, 'jenis_dokumen')
                      ->with(['user', 'handovers.user'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();

        // Data for filter dropdowns (sesuai formulir input dan database)
        $defaultJenis = ['Akta Notaris', 'SK Menkumham'];
        $dbJenis = AktaNotaris::whereNotNull('jenis_dokumen')->where('jenis_dokumen', '!=', '')->distinct()->pluck('jenis_dokumen')->toArray();
        $jenisList = collect(array_values(array_filter(array_unique(array_merge($defaultJenis, $dbJenis)))));

        $defaultStatus = ['Tersedia', 'Dipinjam', 'Diagunkan', 'Dihibahkan', 'Dikembalikan'];
        $dbStatus = AktaNotaris::whereNotNull('status_handover')->where('status_handover', '!=', '')->distinct()->pluck('status_handover')->toArray();
        $statusList = collect(array_values(array_filter(array_unique(array_merge($defaultStatus, $dbStatus)))));
        $officers = \App\Models\User::select('id_user as id', 'name')->orderBy('name')->get();

        return view('brangkas.akta-notaris.index', compact('data', 'jenisList', 'statusList', 'officers'))
            ->with('title', 'Akta Notaris');
    }

    public function storeAktaNotaris(Request $request)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.akta-notaris')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat menambah data.');
        }

        $request->validate([
            'jenis_dokumen'  => 'required|string|max:100',
            'nomor_dokumen'  => 'required|string|max:100',
            'nama_dokumen'   => 'required|string|max:255',
            'tgl_dokumen'    => 'nullable|date',
            'nama_notaris'   => 'nullable|string|max:150',
            'alamat_notaris' => 'nullable|string',
            'telp_notaris'   => 'nullable|string|max:50',
            'file_dokumen'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
            'nama_petugas'   => 'nullable|string|max:100',
            'tgl_input'      => 'nullable|date',
            'keterangan'     => 'nullable|string',
        ], [
            'file_dokumen.mimes' => 'Berkas dokumen wajib berformat PDF, JPG, JPEG, PNG, atau WEBP.',
        ]);

        $payload = $request->except('file_dokumen');
        $payload['nomor_akta'] = $request->nomor_dokumen;
        $payload['nomor_sertifikat'] = $request->nomor_dokumen;
        $payload['nama_sertifikat'] = $request->nama_dokumen;
        $payload['alamat'] = $request->alamat_notaris;
        $payload['tanggal_akta'] = $request->tgl_dokumen;
        $payload['tgl_input'] = $request->tgl_input ?? date('Y-m-d');
                $payload['user_id'] = auth()->id();

        if ($request->keterangan === 'Isi Sendiri' && $request->filled('keterangan_custom')) {
            $payload['keterangan'] = $request->keterangan_custom;
        }

        if ($request->jenis_dokumen === 'Isi Sendiri' && $request->filled('jenis_dokumen_custom')) {
            $payload['jenis_dokumen'] = $request->jenis_dokumen_custom;
        }

        // Tentukan status handover & warna merah
        $statusInfo = $this->resolveStatusFields($payload['keterangan'] ?? 'Tersedia');
        $payload['keterangan'] = $statusInfo['keterangan'];
        $payload['status_handover'] = $statusInfo['status_handover'];
        $payload['warna_merah'] = $statusInfo['warna_merah'];

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destDir = storage_path('app/uploads/akta-notaris');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $filename);
            $payload['file_dokumen'] = 'uploads/akta-notaris/' . $filename;
        }

        AktaNotaris::create($payload);

        return redirect()->route('brangkas.akta-notaris')->with('success', 'Data Akta Notaris berhasil ditambahkan.');
    }

    public function editAktaNotaris($id)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.akta-notaris')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat mengedit data.');
        }
        $item = AktaNotaris::findOrFail($id);
        return view('brangkas.akta-notaris.edit', compact('item'))->with('title', 'Edit Akta Notaris');
    }

    public function updateAktaNotaris(Request $request, $id)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.akta-notaris')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat mengedit data.');
        }
        $item = AktaNotaris::findOrFail($id);

        $request->validate([
            'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ], [
            'file_dokumen.mimes' => 'Berkas dokumen wajib berformat PDF, JPG, JPEG, PNG, atau WEBP.',
        ]);

        $payload = $request->except(['file_dokumen', 'jenis_dokumen', 'nomor_dokumen', 'nama_dokumen', 'keterangan']);
        $payload['jenis_dokumen']    = $item->jenis_dokumen ?: $item->jenis_sertifikat;
        $payload['nomor_dokumen']    = $item->nomor_dokumen ?: $item->nomor_akta;
        $payload['nomor_akta']       = $payload['nomor_dokumen'];
        $payload['nomor_sertifikat'] = $payload['nomor_dokumen'];
        $payload['nama_dokumen']     = $item->nama_dokumen ?: $item->nama_sertifikat;
        $payload['nama_sertifikat']  = $payload['nama_dokumen'];
        $payload['alamat']           = $request->alamat_notaris;
        $payload['tanggal_akta']     = $request->tgl_dokumen;

        $payload['keterangan']      = $item->keterangan;
        $payload['status_handover'] = $item->status_handover;
        $payload['warna_merah']     = $item->warna_merah;

        if ($request->hasFile('file_dokumen')) {
            if ($item->file_dokumen) {
                if (file_exists(storage_path('app/' . $item->file_dokumen))) {
                    @unlink(storage_path('app/' . $item->file_dokumen));
                } elseif (file_exists(public_path($item->file_dokumen))) {
                    @unlink(public_path($item->file_dokumen));
                }
            }
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destDir = storage_path('app/uploads/akta-notaris');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $filename);
            $payload['file_dokumen'] = 'uploads/akta-notaris/' . $filename;
        }

        $item->update($payload);

        return redirect()->route('brangkas.akta-notaris')->with('success', 'Data Akta Notaris berhasil diperbarui.');
    }

    public function destroyAktaNotaris($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('brangkas.akta-notaris')->with('error', 'Akses ditolak: Tombol dan aksi hapus hanya dapat dilakukan oleh Super admin.');
        }
        $item = AktaNotaris::findOrFail($id);
        if ($item->file_dokumen) {
            if (Storage::exists($item->file_dokumen)) {
                Storage::delete($item->file_dokumen);
            } elseif (File::exists(storage_path('app/' . $item->file_dokumen))) {
                File::delete(storage_path('app/' . $item->file_dokumen));
            } elseif (File::exists(public_path($item->file_dokumen))) {
                File::delete(public_path($item->file_dokumen));
            }
        }
        $item->delete();
        return redirect()->route('brangkas.akta-notaris')->with('success', 'Data Akta Notaris berhasil dihapus.');
    }

    /**
     * Apply common filters for Record of Transfer listings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param string $categoryColumn Column name for document category (e.g., 'jenis_sertifikat', 'jenis_dokumen', 'jenis_aset')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyRecordFilters($query, $request, $categoryColumn)
    {
        // Free‑text search across key columns
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search, $categoryColumn) {
                if ($categoryColumn === 'jenis_sertifikat') {
                    $q->where('nama_dokumen', 'like', "%{$search}%")
                      ->orWhere('nomor_sertifikat', 'like', "%{$search}%")
                      ->orWhere('nama_sertifikat', 'like', "%{$search}%")
                      ->orWhere('desa_kelurahan', 'like', "%{$search}%")
                      ->orWhere('kecamatan', 'like', "%{$search}%")
                      ->orWhere('kabupaten_kota', 'like', "%{$search}%")
                      ->orWhere('provinsi', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                } elseif ($categoryColumn === 'jenis_dokumen') {
                    $q->where('nama_dokumen', 'like', "%{$search}%")
                      ->orWhere('nomor_dokumen', 'like', "%{$search}%")
                      ->orWhere('nomor_akta', 'like', "%{$search}%")
                      ->orWhere('nama_notaris', 'like', "%{$search}%")
                      ->orWhere('alamat_notaris', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                } elseif ($categoryColumn === 'jenis_aset') {
                    $q->where('nama_barang', 'like', "%{$search}%")
                      ->orWhere('nama_aset', 'like', "%{$search}%")
                      ->orWhere('merek', 'like', "%{$search}%")
                      ->orWhere('nomor_seri_model', 'like', "%{$search}%")
                      ->orWhere('nomor_registrasi', 'like', "%{$search}%")
                      ->orWhere('lokasi', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                } elseif ($categoryColumn === 'jenis_surat') {
                    $q->where('nama_kendaraan', 'like', "%{$search}%")
                      ->orWhere('nama_pemilik', 'like', "%{$search}%")
                      ->orWhere('no_plat', 'like', "%{$search}%")
                      ->orWhere('no_rangka', 'like', "%{$search}%")
                      ->orWhere('no_mesin', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                } else {
                    $q->where('keterangan', 'like', "%{$search}%");
                }
            });
        }

        // Filter Enumerasi: Kategori
        $catValue = $request->filled($categoryColumn) ? $request->get($categoryColumn) : ($request->filled('kategori') ? $request->kategori : null);
        if ($catValue) {
            if ($categoryColumn === 'jenis_aset') {
                $query->where(function($q) use ($catValue) {
                    $q->where('jenis_aset', $catValue)->orWhere('jenis_barang', $catValue);
                });
            } else {
                $query->where($categoryColumn, $catValue);
            }
        }

        // Filter Enumerasi: Status Handover / Status
        $statusVal = $request->filled('status_handover') ? $request->status_handover : ($request->filled('status') ? $request->status : null);
        if ($statusVal) {
            $query->where(function($q) use ($statusVal) {
                $q->where('status_handover', $statusVal)
                  ->orWhere('keterangan', $statusVal)
                  ->orWhere('keterangan', 'like', "%{$statusVal}%");
            });
        }

        return $query;
    }

    /**
     * Helper to resolve status, status_handover, and color indicator
     */
    private function resolveStatusFields(?string $inputStatus = null): array
    {
        $status = $inputStatus ?: 'Tersedia';
        $ket = strtolower($status);
        $isRed = false;
        $handoverStatus = 'Tersedia';

        if (str_contains($ket, 'diagunkan')) {
            $isRed = true;
            $handoverStatus = 'Diagunkan';
        } elseif (str_contains($ket, 'dipinjam')) {
            $isRed = true;
            $handoverStatus = 'Dipinjam';
        } elseif (str_contains($ket, 'dihibahkan')) {
            $isRed = true;
            $handoverStatus = 'Dihibahkan';
        } elseif (str_contains($ket, 'dikembalikan')) {
            $isRed = false;
            $handoverStatus = 'Dikembalikan';
        } elseif (str_contains($ket, 'fotocopy') || str_contains($ket, 'tidak ada') || str_contains($ket, 'rusak')) {
            $isRed = true;
            $handoverStatus = $status;
        } else {
            $isRed = false;
            $handoverStatus = 'Tersedia';
        }

        return [
            'keterangan' => $status,
            'status_handover' => $handoverStatus,
            'warna_merah' => $isRed,
        ];
    }

    // ==========================================
    // DATA ASET LEMBAGA
    // ==========================================
    public function dataAset(Request $request)
    {
        $query = DataAsetLembaga::query();

        // Free‑text search across key columns
       // Apply unified filters and pagination
        $data = $this->applyRecordFilters($query, $request, 'jenis_aset')
                      ->with(['user', 'handovers.user', 'suratKendaraan'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();

        // Auto next registration number based on total count
        $nextRegNo = 'AST-LPM-' . date('Ym') . '-' . str_pad((DataAsetLembaga::count() + 1), 4, '0', STR_PAD_LEFT);

        // Data for filter dropdowns (sesuai formulir input dan database)
        $defaultJenis = ['Mobil', 'Sepeda Motor', 'Laptop', 'PC', 'Printer', 'TV', 'Lainnya'];
        $dbJenisBarang = DataAsetLembaga::whereNotNull('jenis_barang')->where('jenis_barang', '!=', '')->distinct()->pluck('jenis_barang')->toArray();
        $dbJenisAset = DataAsetLembaga::whereNotNull('jenis_aset')->where('jenis_aset', '!=', '')->distinct()->pluck('jenis_aset')->toArray();
        $jenisList = collect(array_values(array_filter(array_unique(array_merge($defaultJenis, $dbJenisBarang, $dbJenisAset)))));

        $defaultStatus = ['Tersedia', 'Dipinjam', 'Diagunkan', 'Dihibahkan', 'Dikembalikan'];
        $dbStatus = DataAsetLembaga::whereNotNull('status_handover')->where('status_handover', '!=', '')->distinct()->pluck('status_handover')->toArray();
        $statusList = collect(array_values(array_filter(array_unique(array_merge($defaultStatus, $dbStatus)))));

        $kondisiList = DataAsetLembaga::select('kondisi_aset')->distinct()->pluck('kondisi_aset');
        $posisiList  = DataAsetLembaga::select('posisi_aset')->distinct()->pluck('posisi_aset');
        $sumberList  = DataAsetLembaga::select('sumber_perolehan')->distinct()->pluck('sumber_perolehan');
        $officers    = \App\Models\User::select('id_user as id', 'name')->orderBy('name')->get();

        return view('brangkas.data-aset.index', compact('data', 'nextRegNo', 'jenisList', 'kondisiList', 'posisiList', 'sumberList', 'statusList', 'officers'))
            ->with('title', 'Data Aset Lembaga');
    }

    public function storeDataAset(Request $request)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.data-aset')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat menambah data.');
        }

        $request->validate([
            'nama_barang'        => 'required|string|max:255',
            'jenis_barang'       => 'nullable|string|max:100',
            'merek'              => 'nullable|string|max:100',
            'nomor_seri_model'   => 'nullable|string|max:100',
            'sumber_perolehan'   => 'nullable|string|max:50',
            'tgl_perolehan'      => 'nullable|date',
            'kondisi_aset'       => 'nullable|string|max:50',
            'posisi_aset'        => 'nullable|string|max:50',
            'file_dokumen'       => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ], [
            'file_dokumen.mimes' => 'Foto/Berkas aset wajib berformat PDF, JPG, JPEG, PNG, atau WEBP.',
        ]);

        $payload = $request->except('file_dokumen');
        $payload['nama_aset'] = $request->nama_barang;
        $payload['jenis_aset'] = $request->jenis_barang;
        $payload['lokasi'] = $request->posisi_aset === 'Kantor' ? ($request->nama_ruangan ?? 'Kantor') : ($request->nama_penerima ?? $request->posisi_aset);
        $payload['tgl_input'] = $request->tgl_input ?? date('Y-m-d');
        $payload['user_id'] = auth()->id();
        $payload['nama_petugas'] = $request->nama_petugas ?? (auth()->user()->name ?: auth()->user()->username);

        // Auto Registration Number
        if (empty($payload['nomor_registrasi'])) {
            $count = DataAsetLembaga::count() + 1;
            $payload['nomor_registrasi'] = 'AST-LPM-' . date('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        if ($request->keterangan === 'Isi Sendiri' && $request->filled('keterangan_custom')) {
            $payload['keterangan'] = $request->keterangan_custom;
        }

        $statusInfo = $this->resolveStatusFields($payload['keterangan'] ?? 'Tersedia');
        $payload['keterangan'] = $statusInfo['keterangan'];
        $payload['status_handover'] = $statusInfo['status_handover'];
        $payload['warna_merah'] = $statusInfo['warna_merah'];

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destDir = storage_path('app/uploads/data-aset');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $filename);
            $payload['file_dokumen'] = 'uploads/data-aset/' . $filename;
        }

        DataAsetLembaga::create($payload);

        return redirect()->route('brangkas.data-aset')->with('success', 'Data Aset berhasil ditambahkan.');
    }

    public function editDataAset($id)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.data-aset')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat mengedit data.');
        }
        $item = DataAsetLembaga::findOrFail($id);
        return view('brangkas.data-aset.edit', compact('item'))->with('title', 'Edit Data Aset');
    }

    public function updateDataAset(Request $request, $id)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.data-aset')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat mengedit data.');
        }
        $item = DataAsetLembaga::findOrFail($id);

        $request->validate([
            'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ], [
            'file_dokumen.mimes' => 'Foto/Berkas aset wajib berformat PDF, JPG, JPEG, PNG, atau WEBP.',
        ]);

        $payload = $request->except(['file_dokumen', 'jenis_barang', 'nama_barang', 'merek', 'nomor_seri_model', 'keterangan']);
        $payload['nama_barang']      = $item->nama_barang ?: $item->nama_aset;
        $payload['nama_aset']        = $payload['nama_barang'];
        $payload['jenis_barang']     = $item->jenis_barang ?: $item->jenis_aset;
        $payload['jenis_aset']       = $payload['jenis_barang'];
        $payload['merek']            = $item->merek;
        $payload['nomor_seri_model'] = $item->nomor_seri_model;
        $payload['lokasi']           = $request->posisi_aset === 'Kantor' ? ($request->nama_ruangan ?? 'Kantor') : ($request->nama_penerima ?? $request->posisi_aset);

        $payload['keterangan']      = $item->keterangan;
        $payload['status_handover'] = $item->status_handover;
        $payload['warna_merah']     = $item->warna_merah;

        if ($request->hasFile('file_dokumen')) {
            if ($item->file_dokumen) {
                if (file_exists(storage_path('app/' . $item->file_dokumen))) {
                    @unlink(storage_path('app/' . $item->file_dokumen));
                } elseif (file_exists(public_path($item->file_dokumen))) {
                    @unlink(public_path($item->file_dokumen));
                }
            }
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destDir = storage_path('app/uploads/data-aset');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $filename);
            $payload['file_dokumen'] = 'uploads/data-aset/' . $filename;
        }

        $item->update($payload);

        return redirect()->route('brangkas.data-aset')->with('success', 'Data Aset berhasil diperbarui.');
    }

    public function destroyDataAset($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('brangkas.data-aset')->with('error', 'Akses ditolak: Tombol dan aksi hapus hanya dapat dilakukan oleh Super admin.');
        }
        $item = DataAsetLembaga::findOrFail($id);
        if ($item->file_dokumen) {
            if (Storage::exists($item->file_dokumen)) {
                Storage::delete($item->file_dokumen);
            } elseif (File::exists(storage_path('app/' . $item->file_dokumen))) {
                File::delete(storage_path('app/' . $item->file_dokumen));
            } elseif (File::exists(public_path($item->file_dokumen))) {
                File::delete(public_path($item->file_dokumen));
            }
        }
        $item->delete();
        return redirect()->route('brangkas.data-aset')->with('success', 'Data Aset berhasil dihapus.');
    }

    public function exportSuratTanah(Request $request)
    {
        $query = SuratTanah::query();
        $this->applyRecordFilters($query, $request, 'jenis_sertifikat')
             ->orderBy('created_at', 'desc');

        $filename = 'Rekap-Surat-Tanah-' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new SuratTanahExport($query), $filename);
    }

    public function exportAktaNotaris(Request $request)
    {
        $query = AktaNotaris::query();
        $this->applyRecordFilters($query, $request, 'jenis_dokumen')
             ->orderBy('created_at', 'desc');

        $filename = 'Rekap-Akta-Notaris-' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new AktaNotarisExport($query), $filename);
    }

    public function exportDataAset(Request $request)
    {
        $query = DataAsetLembaga::query();
        $this->applyRecordFilters($query, $request, 'jenis_aset')
             ->orderBy('created_at', 'desc');

        $filename = 'Rekap-Data-Aset-' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new DataAsetExport($query), $filename);
    }

    // ==========================================
    // ARSIP SURAT KENDARAAN
    // ==========================================
    public function suratKendaraan(Request $request)
    {
        $query = SuratKendaraan::query();

        $data = $this->applyRecordFilters($query, $request, 'jenis_surat')
                      ->with(['user', 'dataAset', 'handovers.user'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();

        $jenisSuratList = collect(['BPKB', 'STNK']);
        $defaultStatus = ['Tersedia', 'Dipinjam', 'Diagunkan', 'Dihibahkan', 'Dikembalikan'];
        $dbStatus = SuratKendaraan::whereNotNull('status_handover')->where('status_handover', '!=', '')->distinct()->pluck('status_handover')->toArray();
        $statusList = collect(array_values(array_filter(array_unique(array_merge($defaultStatus, $dbStatus)))));
        $officers = \App\Models\User::select('id_user as id', 'name')->orderBy('name')->get();

        $kendaraanList = DataAsetLembaga::where(function($q) {
            $q->whereIn('jenis_barang', ['Mobil', 'Sepeda Motor', 'Motor', 'Kendaraan'])
              ->orWhereIn('jenis_aset', ['Mobil', 'Sepeda Motor', 'Motor', 'Kendaraan'])
              ->orWhere('jenis_barang', 'LIKE', '%mobil%')
              ->orWhere('jenis_barang', 'LIKE', '%motor%')
              ->orWhere('jenis_aset', 'LIKE', '%mobil%')
              ->orWhere('jenis_aset', 'LIKE', '%motor%');
        })->get();

        return view('brangkas.surat-kendaraan.index', compact('data', 'jenisSuratList', 'statusList', 'officers', 'kendaraanList'))
            ->with('title', 'Arsip Surat Kendaraan');
    }

    public function getKendaraanList()
    {
        $kendaraanList = DataAsetLembaga::where(function($q) {
            $q->whereIn('jenis_barang', ['Mobil', 'Sepeda Motor', 'Motor', 'Kendaraan'])
              ->orWhereIn('jenis_aset', ['Mobil', 'Sepeda Motor', 'Motor', 'Kendaraan'])
              ->orWhere('jenis_barang', 'LIKE', '%mobil%')
              ->orWhere('jenis_barang', 'LIKE', '%motor%')
              ->orWhere('jenis_aset', 'LIKE', '%mobil%')
              ->orWhere('jenis_aset', 'LIKE', '%motor%');
        })->get(['id', 'nama_barang', 'nama_aset', 'merek', 'nomor_registrasi', 'nomor_seri_model']);

        return response()->json($kendaraanList);
    }

    public function storeSuratKendaraan(Request $request)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.surat-kendaraan')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat menambah data.');
        }

        $request->validate([
            'jenis_surat'    => 'required|in:BPKB,STNK',
            'data_aset_id'   => 'nullable|exists:data_aset_lembaga,id',
            'nama_kendaraan' => 'required|string|max:255',
            'file_dokumen'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ], [
            'file_dokumen.mimes' => 'Berkas dokumen wajib berformat PDF, JPG, JPEG, PNG, atau WEBP.',
            'file_dokumen.max'   => 'Ukuran berkas dokumen tidak boleh melebihi 20 MB.',
        ]);

        $payload = $request->except('file_dokumen');
        $payload['tgl_input'] = $request->tgl_input ?? date('Y-m-d');
        $payload['user_id'] = auth()->id();
        $payload['nama_petugas'] = $request->nama_petugas ?? (auth()->user()->name ?: auth()->user()->username);

        if ($request->filled('data_aset_id')) {
            $aset = DataAsetLembaga::find($request->data_aset_id);
            if ($aset) {
                $payload['nama_kendaraan'] = ($aset->nama_barang ?: $aset->nama_aset) . ($aset->merek ? ' (' . $aset->merek . ')' : '');
            }
        }

        $statusInfo = $this->resolveStatusFields($payload['keterangan'] ?? 'Tersedia');
        $payload['keterangan'] = $statusInfo['keterangan'];
        $payload['status_handover'] = $statusInfo['status_handover'];
        $payload['warna_merah'] = $statusInfo['warna_merah'];

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destDir = storage_path('app/uploads/surat-kendaraan');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $filename);
            $payload['file_dokumen'] = 'uploads/surat-kendaraan/' . $filename;
        }

        SuratKendaraan::create($payload);

        return redirect()->route('brangkas.surat-kendaraan')->with('success', 'Data Surat Kendaraan berhasil ditambahkan.');
    }

    public function editSuratKendaraan($id)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.surat-kendaraan')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat mengedit data.');
        }
        $item = SuratKendaraan::findOrFail($id);
        $kendaraanList = DataAsetLembaga::where(function($q) {
            $q->whereIn('jenis_barang', ['Mobil', 'Sepeda Motor', 'Motor', 'Kendaraan'])
              ->orWhereIn('jenis_aset', ['Mobil', 'Sepeda Motor', 'Motor', 'Kendaraan'])
              ->orWhere('jenis_barang', 'LIKE', '%mobil%')
              ->orWhere('jenis_barang', 'LIKE', '%motor%')
              ->orWhere('jenis_aset', 'LIKE', '%mobil%')
              ->orWhere('jenis_aset', 'LIKE', '%motor%');
        })->get();

        return view('brangkas.surat-kendaraan.edit', compact('item', 'kendaraanList'))->with('title', 'Edit Surat Kendaraan');
    }

    public function updateSuratKendaraan(Request $request, $id)
    {
        if (!auth()->user()->canManageData()) {
            return redirect()->route('brangkas.surat-kendaraan')->with('error', 'Akses ditolak: Hanya Super admin dan Admin yang dapat mengedit data.');
        }
        $item = SuratKendaraan::findOrFail($id);

        $request->validate([
            'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ], [
            'file_dokumen.mimes' => 'Berkas dokumen wajib berformat PDF, JPG, JPEG, PNG, atau WEBP.',
            'file_dokumen.max'   => 'Ukuran berkas dokumen tidak boleh melebihi 20 MB.',
        ]);

        $payload = $request->except(['file_dokumen', 'jenis_surat', 'data_aset_id', 'nama_kendaraan', 'no_plat', 'no_rangka', 'no_mesin', 'keterangan']);
        $payload['jenis_surat']    = $item->jenis_surat;
        $payload['data_aset_id']   = $item->data_aset_id;
        $payload['nama_kendaraan'] = $item->nama_kendaraan;
        $payload['no_plat']        = $item->no_plat;
        $payload['no_rangka']      = $item->no_rangka;
        $payload['no_mesin']       = $item->no_mesin;

        $payload['keterangan']      = $item->keterangan;
        $payload['status_handover'] = $item->status_handover;
        $payload['warna_merah']     = $item->warna_merah;

        if ($request->hasFile('file_dokumen')) {
            if ($item->file_dokumen) {
                if (file_exists(storage_path('app/' . $item->file_dokumen))) {
                    @unlink(storage_path('app/' . $item->file_dokumen));
                } elseif (file_exists(public_path($item->file_dokumen))) {
                    @unlink(public_path($item->file_dokumen));
                }
            }
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destDir = storage_path('app/uploads/surat-kendaraan');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $filename);
            $payload['file_dokumen'] = 'uploads/surat-kendaraan/' . $filename;
        }

        $item->update($payload);

        return redirect()->route('brangkas.surat-kendaraan')->with('success', 'Data Surat Kendaraan berhasil diperbarui.');
    }

    public function destroySuratKendaraan($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('brangkas.surat-kendaraan')->with('error', 'Akses ditolak: Tombol dan aksi hapus hanya dapat dilakukan oleh Super admin.');
        }
        $item = SuratKendaraan::findOrFail($id);
        if ($item->file_dokumen) {
            if (Storage::exists($item->file_dokumen)) {
                Storage::delete($item->file_dokumen);
            } elseif (File::exists(storage_path('app/' . $item->file_dokumen))) {
                File::delete(storage_path('app/' . $item->file_dokumen));
            } elseif (File::exists(public_path($item->file_dokumen))) {
                File::delete(public_path($item->file_dokumen));
            }
        }
        $item->delete();
        return redirect()->route('brangkas.surat-kendaraan')->with('success', 'Data Surat Kendaraan berhasil dihapus.');
    }

    public function exportSuratKendaraan(Request $request)
    {
        $query = SuratKendaraan::query();
        $this->applyRecordFilters($query, $request, 'jenis_surat')
             ->orderBy('created_at', 'desc');

        $filename = 'Rekap-Surat-Kendaraan-' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new SuratKendaraanExport($query), $filename);
    }
}