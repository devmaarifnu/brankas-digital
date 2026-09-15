<?php

namespace App\Http\Controllers;

use App\Models\RecordOfHandover;
use App\Models\SuratTanah;
use App\Models\AktaNotaris;
use App\Models\DataAsetLembaga;
use Illuminate\Http\Request;

class HandoverController extends Controller
{
    public function index()
    {
        $records = RecordOfHandover::orderBy('created_at', 'desc')->get();
        return view('handover.index', compact('records'))->with('title', 'Record of Transfer');
    }

    public function getItemsByKategori(Request $request)
    {
        $kategori = $request->get('kategori');
        $items = [];
        if ($kategori === 'Arsip Surat Tanah') {
            $items = SuratTanah::select('id', 'nama_sertifikat as nama_dokumen', 'nomor_sertifikat')->get()->map(function($i) {
                return ['id' => $i->id, 'nama_dokumen' => ($i->nama_dokumen ?: 'Surat Tanah') . ' (' . ($i->nomor_sertifikat ?: '-') . ')'];
            });
        } elseif ($kategori === 'Akta Notaris') {
            $items = AktaNotaris::select('id', 'nama_dokumen', 'nomor_dokumen')->get()->map(function($i) {
                return ['id' => $i->id, 'nama_dokumen' => ($i->nama_dokumen ?: 'Akta') . ' (' . ($i->nomor_dokumen ?: '-') . ')'];
            });
        } elseif ($kategori === 'Data Aset Lembaga') {
            $items = DataAsetLembaga::select('id', 'nama_barang as nama_dokumen', 'nomor_registrasi')->get()->map(function($i) {
                return ['id' => $i->id, 'nama_dokumen' => ($i->nama_dokumen ?: 'Aset') . ' [' . ($i->nomor_registrasi ?: '-') . ']'];
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
            'kategori'        => 'required',
            'ref_id'          => 'required|integer',
            'nama_dokumen'    => 'required|string',
            'status'          => 'required|in:Dipinjam,Diagunkan,Dihibahkan',
            'tgl_serahterima' => 'required|date',
            'file_bukti'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:15360',
        ]);

        $data = $request->except('file_bukti');

        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/handover'), $filename);
            $data['file_bukti'] = 'uploads/handover/' . $filename;
        }

        RecordOfHandover::create($data);

        // Auto-update status dan warna merah di tabel sumber
        $refId = $request->ref_id;
        $status = $request->status;
        $keteranganUpdate = $status . ' (Tgl: ' . date('d/m/Y', strtotime($request->tgl_serahterima)) . ')';

        if ($request->kategori === 'Arsip Surat Tanah') {
            SuratTanah::where('id', $refId)->update([
                'status_handover' => $status,
                'keterangan'      => $keteranganUpdate,
                'warna_merah'     => true
            ]);
        } elseif ($request->kategori === 'Akta Notaris') {
            AktaNotaris::where('id', $refId)->update([
                'status_handover' => $status,
                'keterangan'      => $keteranganUpdate,
                'warna_merah'     => true
            ]);
        } elseif ($request->kategori === 'Data Aset Lembaga') {
            DataAsetLembaga::where('id', $refId)->update([
                'status_handover' => $status,
                'posisi_aset'     => $status,
                'nama_penerima'   => $request->nama_peminjam ?: ($request->nama_penerima ?: $request->penanggung_agunan),
                'keterangan'      => $keteranganUpdate,
                'warna_merah'     => true
            ]);
        }

        return back()->with('success', 'Record of Transfer berhasil disimpan. Rekap telah diperbarui otomatis & ditandai merah.');
    }
}