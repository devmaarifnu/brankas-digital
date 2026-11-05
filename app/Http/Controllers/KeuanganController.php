<?php
namespace App\Http\Controllers;
use App\Models\KeuanganPengajuan;
use App\Models\KeuanganDokumen;
use App\Models\KeuanganRekapBulanan;
use App\Models\KeuanganRekapTahunan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KeuanganController extends Controller {
    public function pengajuan() {
        $data = KeuanganPengajuan::orderBy('created_at','desc')->get();
        return view('keuangan.pengajuan', compact('data'))->with('title', 'Pengajuan Keuangan');
    }
    public function storePengajuan(Request $request) {
        if (auth()->user()->isViewer()) {
            return back()->with('error', 'Akses ditolak: Viewer tidak diizinkan membuat pengajuan.');
        }
        $request->validate(['judul' => 'required|string']);
        $d = $request->except('file_bukti');
        if ($request->hasFile('file_bukti')) {
            $f = $request->file('file_bukti');
            $fname = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $f->getClientOriginalName());
            $destDir = storage_path('app/uploads/keuangan');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $f->move($destDir, $fname);
            $d['file_bukti'] = 'uploads/keuangan/'.$fname;
        }
        $d['status'] = 'Menunggu';
        KeuanganPengajuan::create($d);
        return back()->with('success','Pengajuan berhasil disimpan.');
    }

    public function approvePengajuan($id) {
        if (!auth()->user()->isAproval() && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Akses ditolak: Hanya role Aproval dan Super Admin yang berhak menyetujui.');
        }
        $item = KeuanganPengajuan::findOrFail($id);
        $item->status = 'Disetujui';
        $item->save();
        return back()->with('success', 'Pengajuan dana berhasil disetujui.');
    }

    public function rejectPengajuan($id) {
        if (!auth()->user()->isAproval() && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Akses ditolak: Hanya role Aproval dan Super Admin yang berhak menolak.');
        }
        $item = KeuanganPengajuan::findOrFail($id);
        $item->status = 'Ditolak';
        $item->save();
        return back()->with('success', 'Pengajuan dana telah ditolak.');
    }

    private function dokumenView(string $jenis, string $title, string $viewName) {
        $data = KeuanganDokumen::where('jenis', $jenis)->orderBy('created_at','desc')->get();
        return view($viewName, compact('data'))->with('title', $title);
    }
    public function rekeningKoran(Request $request)
    {
        $allData = KeuanganDokumen::where('jenis', 'rekening_koran')->get();
        $selectedMonth = $request->query('bulan');
        $selectedYear = $request->query('tahun');
        $periodData = $allData->map(function ($item) {
            $parts = $this->parsePeriode($item->periode);
            $item->periode_bulan = $parts['month'];
            $item->periode_tahun = $parts['year'];
            $item->periode_label = $parts['month'] && $parts['year']
                ? Carbon::createFromDate($parts['year'], $parts['month'], 1)->locale('id')->translatedFormat('F Y')
                : $item->periode;

            return $item;
        });

        $years = collect($periodData->map(fn ($item) => $item->periode_tahun)
            ->filter()->unique()->sortDesc()->values()->all());

        $data = $periodData
            ->when($selectedMonth, fn ($items) => $items->where('periode_bulan', (int) $selectedMonth))
            ->when($selectedYear, fn ($items) => $items->where('periode_tahun', (int) $selectedYear))
            ->sortByDesc(fn ($item) => sprintf('%04d-%02d-%010d', $item->periode_tahun ?? 0, $item->periode_bulan ?? 0, $item->id))
            ->values();

        $inputYears = range(2000, max(2031, now()->year + 1));

        return view('keuangan.rekening-koran', [
            'data' => $data,
            'years' => $years,
            'inputYears' => array_reverse($inputYears),
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'isSuperAdmin' => auth()->user()->isSuperAdmin(),
        ])->with('title', 'Rekening Koran');
    }

    private function parsePeriode(?string $periode): array
    {
        $value = trim((string) $periode);
        if (preg_match('/^(\d{4})-(\d{1,2})$/', $value, $matches)) {
            return ['year' => (int) $matches[1], 'month' => (int) $matches[2]];
        }
        if (preg_match('/^(\d{1,2})[\/-](\d{4})$/', $value, $matches)) {
            return ['year' => (int) $matches[2], 'month' => (int) $matches[1]];
        }

        $months = [
            'januari' => 1, 'january' => 1, 'jan' => 1,
            'februari' => 2, 'february' => 2, 'feb' => 2,
            'maret' => 3, 'march' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'june' => 6, 'jun' => 6,
            'juli' => 7, 'july' => 7, 'jul' => 7,
            'agustus' => 8, 'agu' => 8, 'august' => 8, 'aug' => 8,
            'september' => 9, 'sep' => 9,
            'oktober' => 10, 'october' => 10, 'okt' => 10, 'oct' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'december' => 12, 'des' => 12, 'dec' => 12,
        ];
        if (preg_match('/^([[:alpha:]]+)\s+(\d{4})$/u', $value, $matches)) {
            $month = $months[mb_strtolower($matches[1])] ?? null;
            if ($month) {
                return ['year' => (int) $matches[2], 'month' => $month];
            }
        }

        return ['year' => null, 'month' => null];
    }
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
        if (auth()->user()->isViewer()) {
            return back()->with('error', 'Akses ditolak: Viewer tidak diizinkan mengunggah dokumen.');
        }
        $rules = [
            'jenis' => 'required|in:rekening_koran,buku_bank,buku_kas_tunai,buku_kas_umum',
            'file_path' => 'required|file|mimes:pdf,jpg,jpeg,png|max:51200',
            'judul' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ];
        if ($request->input('jenis') === 'rekening_koran') {
            $rules['bulan'] = 'required|integer|between:1,12';
            $rules['tahun'] = 'required|integer|between:2000,2100';
        } else {
            $rules['periode'] = 'required|string|max:100';
        }
        $request->validate($rules);
        $f = $request->file('file_path');
        $fname = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $f->getClientOriginalName());
        $destDir = storage_path('app/uploads/keuangan');
        if (!file_exists($destDir)) {
            @mkdir($destDir, 0755, true);
        }
        $f->move($destDir, $fname);
        $periode = $request->input('jenis') === 'rekening_koran'
            ? sprintf('%04d-%02d', $request->integer('tahun'), $request->integer('bulan'))
            : $request->input('periode');
        KeuanganDokumen::create(['jenis'=>$request->jenis,'periode'=>$periode,'file_path'=>'uploads/keuangan/'.$fname,'judul'=>$request->judul,'keterangan'=>$request->keterangan]);
        return back()->with('success','Dokumen berhasil diunggah.');
    }

    public function updateRekeningKoran(Request $request, int $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengubah teks rekening koran.');
        }

        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $document = KeuanganDokumen::where('jenis', 'rekening_koran')->findOrFail($id);
        $document->update($validated);

        return back()->with('success', 'Judul dan keterangan rekening koran berhasil diperbarui.');
    }
}
