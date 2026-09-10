<?php
namespace App\Http\Controllers;
use App\Models\KeuanganPengajuan;
use App\Models\KeuanganDokumen;
use App\Models\KeuanganRekapBulanan;
use App\Models\KeuanganRekapTahunan;
use Illuminate\Http\Request;

class KeuanganController extends Controller {
    public function pengajuan() {
        $data = KeuanganPengajuan::orderBy('created_at','desc')->get();
        return view('keuangan.pengajuan', compact('data'))->with('title', 'Pengajuan Keuangan');
    }
    public function storePengajuan(Request $request) {
        $request->validate(['judul' => 'required|string']);
        $d = $request->except('file_bukti');
        if ($request->hasFile('file_bukti')) {
            $f = $request->file('file_bukti');
            $fname = time().'_'.$f->getClientOriginalName();
            $f->move(public_path('uploads/keuangan'), $fname);
            $d['file_bukti'] = 'uploads/keuangan/'.$fname;
        }
        KeuanganPengajuan::create($d);
        return back()->with('success','Pengajuan berhasil disimpan.');
    }
    private function dokumenView(string $jenis, string $title, string $viewName) {
        $data = KeuanganDokumen::where('jenis', $jenis)->orderBy('created_at','desc')->get();
        return view($viewName, compact('data'))->with('title', $title);
    }
    public function rekeningKoran() { return $this->dokumenView('rekening_koran', 'Rekening Koran', 'keuangan.rekening-koran'); }
    public function bukuBank() { return $this->dokumenView('buku_bank', 'Buku Bank', 'keuangan.buku-bank'); }
    public function bukuKasTunai() { return $this->dokumenView('buku_kas_tunai', 'Buku Kas Tunai', 'keuangan.buku-kas-tunai'); }
    public function bukuKasUmum() { return $this->dokumenView('buku_kas_umum', 'Buku Kas Umum', 'keuangan.buku-kas-umum'); }
    public function rekapBulanan() {
        $data = KeuanganRekapBulanan::orderBy('tahun','desc')->orderBy('id','desc')->get();
        return view('keuangan.rekap-bulanan', compact('data'))->with('title','Rekap Bulanan');
    }
    public function rekapTahunan() {
        $data = KeuanganRekapTahunan::orderBy('tahun','desc')->get();
        return view('keuangan.rekap-tahunan', compact('data'))->with('title','Rekap Tahunan');
    }
    public function storeDokumen(Request $request) {
        $request->validate(['jenis'=>'required','periode'=>'required','file_path'=>'required|file|mimes:pdf,jpg,jpeg,png|max:10240']);
        $f = $request->file('file_path');
        $fname = time().'_'.$f->getClientOriginalName();
        $f->move(public_path('uploads/keuangan'), $fname);
        KeuanganDokumen::create(['jenis'=>$request->jenis,'periode'=>$request->periode,'file_path'=>'uploads/keuangan/'.$fname,'judul'=>$request->judul,'keterangan'=>$request->keterangan]);
        return back()->with('success','Dokumen berhasil diunggah.');
    }
}
