<?php

namespace App\Http\Controllers;

use App\Models\SuratTanah;
use App\Models\AktaNotaris;
use App\Models\DataAsetLembaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BrangkasController extends Controller
{
    // ==========================================
    // ARSIP SURAT TANAH
    // ==========================================
    public function suratTanah()
    {
        $data = SuratTanah::with(['user', 'handovers.user'])->orderBy('created_at', 'desc')->get();
        return view('brangkas.surat-tanah.index', compact('data'))->with('title', 'Arsip Surat Tanah');
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
            'file_dokumen'     => 'nullable|file|mimes:pdf|max:15360',
            'tgl_input'        => 'nullable|date',
            'keterangan'       => 'nullable|string',
        ], [
            'required' => 'Kolom :attribute wajib diisi.',
            'file_dokumen.mimes' => 'Berkas dokumen wajib berformat PDF.',
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
        $ket = strtolower($payload['keterangan'] ?? '');
        if (str_contains($ket, 'tidak ada') || str_contains($ket, 'fotocopy') || str_contains($ket, 'dipinjam') || str_contains($ket, 'diagunkan')) {
            $payload['warna_merah'] = true;
            $payload['status_handover'] = str_contains($ket, 'diagunkan') ? 'Diagunkan' : (str_contains($ket, 'dipinjam') ? 'Dipinjam' : 'Ditangguhkan');
        } else {
            $payload['warna_merah'] = false;
            $payload['status_handover'] = 'Tersedia';
        }

        if ($request->hasFile('file_dokumen')) {
            $path = $request->file('file_dokumen')->store('uploads','local');
            $payload['file_dokumen'] = $path;
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
            'jenis_sertifikat' => 'required|string|max:100',
            'nomor_sertifikat' => 'required|string|max:100',
            'luas'             => 'required|string|max:50',
            'nama_sertifikat'  => 'required|string|max:255',
            'file_dokumen'     => 'nullable|file|mimes:pdf|max:15360',
        ], [
            'file_dokumen.mimes' => 'Berkas dokumen wajib berformat PDF.',
        ]);

        $payload = $request->except('file_dokumen');
        $payload['nama_dokumen'] = $request->nama_sertifikat;
        $payload['atas_nama'] = $request->nama_sertifikat;
        $payload['alamat'] = trim(($request->desa_kelurahan ? $request->desa_kelurahan . ', ' : '') . ($request->kecamatan ? $request->kecamatan . ', ' : '') . ($request->kabupaten_kota ? $request->kabupaten_kota . ', ' : '') . ($request->provinsi ?? ''), ' ,');
        $payload['lokasi'] = $payload['alamat'];

        if ($request->keterangan === 'Isi Sendiri' && $request->filled('keterangan_custom')) {
            $payload['keterangan'] = $request->keterangan_custom;
        }

        if ($request->jenis_sertifikat === 'Isi Sendiri' && $request->filled('jenis_sertifikat_custom')) {
            $payload['jenis_sertifikat'] = $request->jenis_sertifikat_custom;
        }

        $ket = strtolower($payload['keterangan'] ?? '');
        if (str_contains($ket, 'tidak ada') || str_contains($ket, 'fotocopy') || str_contains($ket, 'dipinjam') || str_contains($ket, 'diagunkan')) {
            $payload['warna_merah'] = true;
            $payload['status_handover'] = str_contains($ket, 'diagunkan') ? 'Diagunkan' : (str_contains($ket, 'dipinjam') ? 'Dipinjam' : 'Ditangguhkan');
        } else {
            $payload['warna_merah'] = false;
            $payload['status_handover'] = 'Tersedia';
        }

        if ($request->hasFile('file_dokumen')) {
            if ($item->file_dokumen) {
                if (Storage::exists($item->file_dokumen)) {
                    Storage::delete($item->file_dokumen);
                } elseif (File::exists(public_path($item->file_dokumen))) {
                    File::delete(public_path($item->file_dokumen));
                }
            }
            $path = $request->file('file_dokumen')->store('uploads', 'local');
            $payload['file_dokumen'] = $path;
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
    public function aktaNotaris()
    {
        $data = AktaNotaris::with(['user', 'handovers.user'])->orderBy('created_at', 'desc')->get();
        return view('brangkas.akta-notaris.index', compact('data'))->with('title', 'Akta Notaris');
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
            'file_dokumen'   => 'nullable|file|mimes:pdf|max:15360',
            'nama_petugas'   => 'nullable|string|max:100',
            'tgl_input'      => 'nullable|date',
            'keterangan'     => 'nullable|string',
        ], [
            'file_dokumen.mimes' => 'Berkas dokumen wajib berformat PDF.',
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

        $ket = strtolower($payload['keterangan'] ?? '');
        if (str_contains($ket, 'tidak ada') || str_contains($ket, 'fotocopy') || str_contains($ket, 'dipinjam') || str_contains($ket, 'diagunkan')) {
            $payload['warna_merah'] = true;
            $payload['status_handover'] = str_contains($ket, 'diagunkan') ? 'Diagunkan' : (str_contains($ket, 'dipinjam') ? 'Dipinjam' : 'Ditangguhkan');
        } else {
            $payload['warna_merah'] = false;
            $payload['status_handover'] = 'Tersedia';
        }

        if ($request->hasFile('file_dokumen')) {
            $path = $request->file('file_dokumen')->store('uploads','local');
            $payload['file_dokumen'] = $path;
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
            'jenis_dokumen' => 'required|string|max:100',
            'nomor_dokumen' => 'required|string|max:100',
            'nama_dokumen'  => 'required|string|max:255',
            'file_dokumen'  => 'nullable|file|mimes:pdf|max:15360',
        ], [
            'file_dokumen.mimes' => 'Berkas dokumen wajib berformat PDF.',
        ]);

        $payload = $request->except('file_dokumen');
        $payload['nomor_akta'] = $request->nomor_dokumen;
        $payload['nomor_sertifikat'] = $request->nomor_dokumen;
        $payload['nama_sertifikat'] = $request->nama_dokumen;
        $payload['alamat'] = $request->alamat_notaris;
        $payload['tanggal_akta'] = $request->tgl_dokumen;

        if ($request->keterangan === 'Isi Sendiri' && $request->filled('keterangan_custom')) {
            $payload['keterangan'] = $request->keterangan_custom;
        }

        if ($request->jenis_dokumen === 'Isi Sendiri' && $request->filled('jenis_dokumen_custom')) {
            $payload['jenis_dokumen'] = $request->jenis_dokumen_custom;
        }

        $ket = strtolower($payload['keterangan'] ?? '');
        if (str_contains($ket, 'tidak ada') || str_contains($ket, 'fotocopy') || str_contains($ket, 'dipinjam') || str_contains($ket, 'diagunkan')) {
            $payload['warna_merah'] = true;
            $payload['status_handover'] = str_contains($ket, 'diagunkan') ? 'Diagunkan' : (str_contains($ket, 'dipinjam') ? 'Dipinjam' : 'Ditangguhkan');
        } else {
            $payload['warna_merah'] = false;
            $payload['status_handover'] = 'Tersedia';
        }

        if ($request->hasFile('file_dokumen')) {
            if ($item->file_dokumen) {
                if (Storage::exists($item->file_dokumen)) {
                    Storage::delete($item->file_dokumen);
                } elseif (File::exists(public_path($item->file_dokumen))) {
                    File::delete(public_path($item->file_dokumen));
                }
            }
            $path = $request->file('file_dokumen')->store('uploads', 'local');
            $payload['file_dokumen'] = $path;
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
            } elseif (File::exists(public_path($item->file_dokumen))) {
                File::delete(public_path($item->file_dokumen));
            }
        }
        $item->delete();
        return redirect()->route('brangkas.akta-notaris')->with('success', 'Data Akta Notaris berhasil dihapus.');
    }

    // ==========================================
    // DATA ASET LEMBAGA
    // ==========================================
    public function dataAset()
    {
        $data = DataAsetLembaga::with(['user', 'handovers.user'])->orderBy('created_at', 'desc')->get();
        // Generate auto next registration number
        $nextRegNo = 'AST-LPM-' . date('Ym') . '-' . str_pad(($data->count() + 1), 4, '0', STR_PAD_LEFT);
        return view('brangkas.data-aset.index', compact('data', 'nextRegNo'))->with('title', 'Data Aset Lembaga');
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
            'file_dokumen'       => 'nullable|file|mimes:pdf|max:15360',
        ], [
            'file_dokumen.mimes' => 'Foto/Berkas aset wajib berformat PDF.',
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

        $posisi = $payload['posisi_aset'] ?? 'Kantor';
        $kondisi = $payload['kondisi_aset'] ?? 'Sangat Baik';
        if ($posisi === 'Dipinjam' || $posisi === 'Dihibahkan' || $kondisi === 'Rusak Berat') {
            $payload['warna_merah'] = true;
            $payload['status_handover'] = $posisi;
        } else {
            $payload['warna_merah'] = false;
            $payload['status_handover'] = 'Tersedia';
        }

        if ($request->hasFile('file_dokumen')) {
            $path = $request->file('file_dokumen')->store('uploads', 'local');
            $payload['file_dokumen'] = $path;
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
            'nama_barang'  => 'required|string|max:255',
            'file_dokumen' => 'nullable|file|mimes:pdf|max:15360',
        ], [
            'file_dokumen.mimes' => 'Foto/Berkas aset wajib berformat PDF.',
        ]);

        $payload = $request->except('file_dokumen');
        $payload['nama_aset'] = $request->nama_barang;
        $payload['jenis_aset'] = $request->jenis_barang;
        $payload['lokasi'] = $request->posisi_aset === 'Kantor' ? ($request->nama_ruangan ?? 'Kantor') : ($request->nama_penerima ?? $request->posisi_aset);

        if ($request->keterangan === 'Isi Sendiri' && $request->filled('keterangan_custom')) {
            $payload['keterangan'] = $request->keterangan_custom;
        }

        $posisi = $payload['posisi_aset'] ?? 'Kantor';
        $kondisi = $payload['kondisi_aset'] ?? 'Sangat Baik';
        if ($posisi === 'Dipinjam' || $posisi === 'Dihibahkan' || $kondisi === 'Rusak Berat') {
            $payload['warna_merah'] = true;
            $payload['status_handover'] = $posisi;
        } else {
            $payload['warna_merah'] = false;
            $payload['status_handover'] = 'Tersedia';
        }

        if ($request->hasFile('file_dokumen')) {
            if ($item->file_dokumen && Storage::exists($item->file_dokumen)) {
                Storage::delete($item->file_dokumen);
            }
            $path = $request->file('file_dokumen')->store('uploads','local');
            $payload['file_dokumen'] = $path;
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
            } elseif (File::exists(public_path($item->file_dokumen))) {
                File::delete(public_path($item->file_dokumen));
            }
        }
        $item->delete();
        return redirect()->route('brangkas.data-aset')->with('success', 'Data Aset berhasil dihapus.');
    }
}