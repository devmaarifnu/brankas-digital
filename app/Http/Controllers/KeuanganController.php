<?php
namespace App\Http\Controllers;
use App\Models\KeuanganPengajuan;
use App\Models\KeuanganDokumen;
use App\Models\KeuanganRekapBulanan;
use App\Models\KeuanganRekapTahunan;
use App\Models\KeuanganBukuKasTunai;
use App\Models\KeuanganBukuKasTunaiTransaksi;
use App\Models\KeuanganBukuKasTunaiAkun;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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

        $filteredData = $periodData
            ->when($selectedMonth, fn ($items) => $items->where('periode_bulan', (int) $selectedMonth))
            ->when($selectedYear, fn ($items) => $items->where('periode_tahun', (int) $selectedYear))
            ->sortByDesc(fn ($item) => sprintf('%04d-%02d-%010d', $item->periode_tahun ?? 0, $item->periode_bulan ?? 0, $item->id))
            ->values();

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $data = new LengthAwarePaginator(
            $filteredData->forPage($currentPage, $perPage)->values(),
            $filteredData->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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
    public function bukuKasTunai(Request $request)
    {
        $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $query = KeuanganBukuKasTunai::query()
            ->withSum(['transaksi as total_pemasukan' => fn ($query) => $query->where('jenis_transaksi', 'pemasukan')], 'jumlah')
            ->withSum(['transaksi as total_pengeluaran' => fn ($query) => $query->where('jenis_transaksi', 'pengeluaran')], 'jumlah');
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->integer('bulan'));
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->integer('tahun'));
        }
        if ($request->filled('q')) {
            $search = mb_strtolower(trim($request->query('q')));
            $matchingMonths = array_keys(array_filter($months, fn ($monthName) => str_contains(mb_strtolower($monthName), $search)));
            if (ctype_digit($search) && (int) $search >= 1 && (int) $search <= 12) {
                $matchingMonths[] = (int) $search;
            }
            $query->where(function ($builder) use ($search, $matchingMonths) {
                $builder->where('tahun', 'like', '%'.$search.'%')
                    ->orWhere('judul', 'like', '%'.$search.'%')
                    ->orWhere('keterangan', 'like', '%'.$search.'%');
                if ($matchingMonths) {
                    $builder->orWhereIn('bulan', array_unique($matchingMonths));
                }
            });
        }
        $data = $query->orderByDesc('tahun')->orderByDesc('bulan')->paginate(15)->withQueryString();
        $years = KeuanganBukuKasTunai::query()->distinct()->orderByDesc('tahun')->pluck('tahun');
        $inputYears = array_reverse(range(2000, max(2031, now()->year + 1)));

        $data->getCollection()->transform(function ($item) {
            $item->total_pemasukan = (float) ($item->total_pemasukan ?? 0);
            $item->total_pengeluaran = (float) ($item->total_pengeluaran ?? 0);
            $item->saldo_akhir = (float) $item->saldo_awal + $item->total_pemasukan - $item->total_pengeluaran;

            return $item;
        });

        return view('keuangan.buku-kas-tunai', [
            'data' => $data,
            'months' => $months,
            'years' => $years,
            'inputYears' => $inputYears,
            'search' => $request->query('q'),
            'selectedMonth' => $request->query('bulan'),
            'selectedYear' => $request->query('tahun'),
        ])->with('title', 'Buku Kas Tunai');
    }

    public function storeBukuKasTunai(Request $request)
    {
        if (auth()->user()->isViewer()) {
            return back()->with('error', 'Akses ditolak: Viewer tidak diizinkan membuat laporan.');
        }

        $validated = $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|between:2000,2100',
            'judul' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:5000',
            'saldo_awal' => 'required|numeric|min:0|max:9999999999999.99',
            'saldo_awal_lampiran' => 'nullable|array',
            'saldo_awal_lampiran.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $exists = KeuanganBukuKasTunai::where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])->exists();
        if ($exists) {
            return back()->withErrors(['bulan' => 'Laporan untuk bulan dan tahun tersebut sudah tersedia.'])->withInput();
        }

        $files = $request->file('saldo_awal_lampiran', []);
        unset($validated['saldo_awal_lampiran']);
        $report = KeuanganBukuKasTunai::create($validated);
        foreach ($files as $file) {
            $report->lampiranSaldoAwal()->create(['file_path' => $this->storeBukuKasTunaiLampiran($file)]);
        }

        return redirect()->route('keuangan.buku-kas-tunai')->with('success', 'Laporan Buku Kas Tunai berhasil dibuat.');
    }

    public function showBukuKasTunai(KeuanganBukuKasTunai $bukuKasTunai)
    {
        return $this->bukuKasTunaiDetail($bukuKasTunai, false, request());
    }

    public function editBukuKasTunai(Request $request, KeuanganBukuKasTunai $bukuKasTunai)
    {
        return $this->bukuKasTunaiDetail($bukuKasTunai, true, $request);
    }

    private function bukuKasTunaiDetail(KeuanganBukuKasTunai $report, bool $canManage, Request $request)
    {
        $report->load('lampiranSaldoAwal');
        $allTransactions = $report->transaksi()->with('lampiranFiles')->orderBy('tanggal')->orderBy('id')->get();
        $totalPemasukan = 0.0;
        $totalPengeluaran = 0.0;
        $runningBalanceCents = (int) round((float) $report->saldo_awal * 100);

        foreach ($allTransactions as $transaction) {
            $amountCents = (int) round((float) $transaction->jumlah * 100);
            if ($transaction->jenis_transaksi === 'pemasukan') {
                $totalPemasukan += (float) $transaction->jumlah;
                $runningBalanceCents += $amountCents;
            } else {
                $totalPengeluaran += (float) $transaction->jumlah;
                $runningBalanceCents -= $amountCents;
            }
            $transaction->saldo_berjalan = $runningBalanceCents / 100;
        }

        $accountCodes = KeuanganBukuKasTunaiAkun::sortForDisplay(KeuanganBukuKasTunaiAkun::all());
        $filteredTransactions = $allTransactions
            ->when($request->filled('kode_akun'), fn ($rows) => $rows->where('kode_akun', $request->query('kode_akun')))
            ->when($request->filled('q'), function ($rows) use ($request) {
                $needle = mb_strtolower(trim($request->query('q')));
                return $rows->filter(fn ($row) => str_contains(mb_strtolower($row->transaksi), $needle)
                    || str_contains(mb_strtolower($row->kode_akun), $needle));
            })
            ->values();

        $totalFilteredPemasukan = $filteredTransactions->where('jenis_transaksi', 'pemasukan')->sum(fn ($item) => (float) $item->jumlah);
        $totalFilteredPengeluaran = $filteredTransactions->where('jenis_transaksi', 'pengeluaran')->sum(fn ($item) => (float) $item->jumlah);

        return view('keuangan.buku-kas-tunai-detail', [
            'report' => $report,
            'transactions' => $filteredTransactions,
            'accountCodes' => $accountCodes,
            'canManage' => $canManage && ! auth()->user()->isViewer() && ! $report->ditutup_pada,
            'canMutate' => $canManage && ! auth()->user()->isViewer(),
            'canVerify' => auth()->user()->isAproval() || auth()->user()->isSuperAdmin(),
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoAkhir' => (float) $report->saldo_awal + $totalPemasukan - $totalPengeluaran,
            'totalFilteredPemasukan' => $totalFilteredPemasukan,
            'totalFilteredPengeluaran' => $totalFilteredPengeluaran,
            'selectedAccountCode' => $request->query('kode_akun'),
            'search' => $request->query('q'),
        ])->with('title', 'Buku Kas Tunai - '.$report->bulan.'/'.$report->tahun);
    }

    public function storeBukuKasTunaiTransaksi(Request $request, KeuanganBukuKasTunai $bukuKasTunai)
    {
        abort_if($bukuKasTunai->ditutup_pada, 403, 'Buku sudah ditutup. Buka buku terlebih dahulu untuk mengubah transaksi.');
        if (auth()->user()->isViewer()) {
            return back()->with('error', 'Akses ditolak: Viewer tidak diizinkan menambah transaksi.');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date_format:Y-m-d',
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran',
            'kode_akun' => 'required|string|max:100',
            'transaksi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|gt:0|max:9999999999999.99',
            'lampiran' => 'nullable|array',
            'lampiran.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $accountExists = KeuanganBukuKasTunaiAkun::where('nama', $validated['kode_akun'])
            ->where('jenis_transaksi', $validated['jenis_transaksi'])->exists();
        if (! $accountExists) {
            return back()->withErrors(['kode_akun' => 'Pilih kode akun yang sesuai dengan jenis transaksi.'])->withInput();
        }

        $tanggal = Carbon::createFromFormat('Y-m-d', $validated['tanggal']);
        if ((int) $tanggal->month !== (int) $bukuKasTunai->bulan || (int) $tanggal->year !== (int) $bukuKasTunai->tahun) {
            return back()->withErrors(['tanggal' => 'Tanggal transaksi harus berada pada bulan dan tahun laporan ini.'])->withInput();
        }

        $files = $request->file('lampiran', []);
        unset($validated['lampiran']);
        $transaction = $bukuKasTunai->transaksi()->create($validated);
        foreach ($files as $file) {
            $transaction->lampiranFiles()->create([
                'buku_kas_tunai_id' => $bukuKasTunai->id,
                'file_path' => $this->storeBukuKasTunaiLampiran($file),
            ]);
        }

        return back()->with('success', 'Transaksi berhasil ditambahkan dan saldo telah dihitung ulang.');
    }

    public function toggleBukuKasTunaiTransaksi(KeuanganBukuKasTunai $bukuKasTunai, KeuanganBukuKasTunaiTransaksi $transaksi)
    {
        if (! auth()->user()->isAproval() && ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Approval dan Super Admin yang dapat memverifikasi transaksi.');
        }

        abort_if($bukuKasTunai->ditutup_pada, 403, 'Buku sudah ditutup.');
        abort_unless($transaksi->buku_kas_tunai_id === $bukuKasTunai->id, 404);
        $transaksi->update(['terverifikasi' => ! $transaksi->terverifikasi]);

        return back()->with('success', 'Status transaksi diperbarui.');
    }

    public function destroyBukuKasTunaiTransaksi(KeuanganBukuKasTunai $bukuKasTunai, KeuanganBukuKasTunaiTransaksi $transaksi)
    {
        abort_if($bukuKasTunai->ditutup_pada, 403, 'Buku sudah ditutup. Buka buku terlebih dahulu untuk mengubah transaksi.');
        if (auth()->user()->isViewer()) {
            return back()->with('error', 'Akses ditolak: Viewer tidak diizinkan menghapus transaksi.');
        }

        abort_unless($transaksi->buku_kas_tunai_id === $bukuKasTunai->id, 404);
        $transaksi->delete();

        return back()->with('success', 'Transaksi dihapus dan saldo telah dihitung ulang.');
    }

    public function updateBukuKasTunaiTransaksi(Request $request, KeuanganBukuKasTunai $bukuKasTunai, KeuanganBukuKasTunaiTransaksi $transaksi)
    {
        abort_if($bukuKasTunai->ditutup_pada, 403, 'Buku sudah ditutup. Buka buku terlebih dahulu untuk mengubah transaksi.');
        abort_unless($transaksi->buku_kas_tunai_id === $bukuKasTunai->id, 404);
        abort_if(auth()->user()->isViewer(), 403);

        $validated = $request->validate([
            'tanggal' => 'required|date_format:Y-m-d',
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran',
            'kode_akun' => 'required|string|max:100',
            'transaksi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|gt:0|max:9999999999999.99',
            'lampiran' => 'nullable|array',
            'lampiran.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        $accountExists = KeuanganBukuKasTunaiAkun::where('nama', $validated['kode_akun'])->where('jenis_transaksi', $validated['jenis_transaksi'])->exists();
        if (! $accountExists) return back()->withErrors(['kode_akun' => 'Pilih kode akun yang sesuai dengan jenis transaksi.'])->withInput();
        $tanggal = Carbon::createFromFormat('Y-m-d', $validated['tanggal']);
        if ((int) $tanggal->month !== (int) $bukuKasTunai->bulan || (int) $tanggal->year !== (int) $bukuKasTunai->tahun) {
            return back()->withErrors(['tanggal' => 'Tanggal transaksi harus berada pada bulan dan tahun laporan ini.'])->withInput();
        }
        $files = $request->file('lampiran', []);
        unset($validated['lampiran']);
        $transaksi->update($validated);
        foreach ($files as $file) {
            $transaksi->lampiranFiles()->create([
                'buku_kas_tunai_id' => $bukuKasTunai->id,
                'file_path' => $this->storeBukuKasTunaiLampiran($file),
            ]);
        }
        return back()->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function storeBukuKasTunaiSaldoAwalLampiran(Request $request, KeuanganBukuKasTunai $bukuKasTunai)
    {
        abort_if($bukuKasTunai->ditutup_pada, 403, 'Buku sudah ditutup. Buka buku terlebih dahulu untuk mengubah lampiran.');
        abort_if(auth()->user()->isViewer(), 403);
        $request->validate([
            'saldo_awal_lampiran' => 'required|array|min:1',
            'saldo_awal_lampiran.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        foreach ($request->file('saldo_awal_lampiran') as $file) {
            $bukuKasTunai->lampiranSaldoAwal()->create(['file_path' => $this->storeBukuKasTunaiLampiran($file)]);
        }
        return back()->with('success', 'Lampiran saldo awal berhasil ditambahkan.');
    }

    private function storeBukuKasTunaiLampiran($file): string
    {
        $destination = public_path('uploads/buku-kas-tunai');
        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        $filename = uniqid('kas-', true).'.'.$file->getClientOriginalExtension();
        $file->move($destination, $filename);
        return 'uploads/buku-kas-tunai/'.$filename;
    }

    public function toggleStatusBukuKasTunai(KeuanganBukuKasTunai $bukuKasTunai)
    {
        abort_if(auth()->user()->isViewer(), 403);
        $bukuKasTunai->update(['ditutup_pada' => $bukuKasTunai->ditutup_pada ? null : now()]);
        return back()->with('success', $bukuKasTunai->ditutup_pada ? 'Buku berhasil ditutup.' : 'Buku berhasil dibuka kembali.');
    }

    public function destroyBukuKasTunai(KeuanganBukuKasTunai $bukuKasTunai)
    {
        if (auth()->user()->isViewer()) {
            return back()->with('error', 'Akses ditolak: Viewer tidak diizinkan menghapus laporan.');
        }

        $bukuKasTunai->delete();

        return redirect()->route('keuangan.buku-kas-tunai')->with('success', 'Laporan dan seluruh transaksinya berhasil dihapus.');
    }

    public function bukuKasTunaiAkun()
    {
        abort_if(auth()->user()->isViewer(), 403);

        $accounts = KeuanganBukuKasTunaiAkun::sortForDisplay(
            KeuanganBukuKasTunaiAkun::withCount('transaksi')->get()
        );

        return view('keuangan.buku-kas-tunai-akun', compact('accounts'))->with('title', 'Pengaturan Kode Akun Buku Kas Tunai');
    }

    public function storeBukuKasTunaiAkun(Request $request)
    {
        if (auth()->user()->isViewer()) {
            abort(403);
        }

        $validated = $request->validate([
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran',
            'nama' => 'required|string|max:100|unique:keuangan_buku_kas_tunai_akun,nama',
        ]);
        KeuanganBukuKasTunaiAkun::create($validated);

        return back()->with('success', 'Kode akun berhasil ditambahkan.');
    }

    public function destroyBukuKasTunaiAkun(KeuanganBukuKasTunaiAkun $akun)
    {
        if (auth()->user()->isViewer()) {
            abort(403);
        }
        if ($akun->transaksi()->exists()) {
            return back()->with('error', 'Kode akun tidak dapat dihapus karena sudah digunakan pada transaksi.');
        }

        $akun->delete();

        return back()->with('success', 'Kode akun berhasil dihapus.');
    }
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

    public function destroyRekeningKoran(int $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat menghapus dokumen rekening koran.');
        }

        $document = KeuanganDokumen::where('jenis', 'rekening_koran')->findOrFail($id);
        $filename = basename($document->file_path);
        $storedFile = storage_path('app/uploads/keuangan/'.$filename);
        $publicFile = public_path('uploads/keuangan/'.$filename);

        if (is_file($storedFile)) {
            @unlink($storedFile);
        } elseif (is_file($publicFile)) {
            @unlink($publicFile);
        }

        $document->delete();

        return back()->with('success', 'Dokumen Rekening Koran berhasil dihapus.');
    }
}
