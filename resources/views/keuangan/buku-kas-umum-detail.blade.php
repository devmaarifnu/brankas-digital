@extends('template.layout')
@section('navbar') @include('template.nav') @endsection
@section('container')
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Keuangan</a></li>
            <li><a href="javascript:void(0)">Buku Kas Umum</a></li>
        </ul>
    </nav>

    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-report-money fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Buku Kas Umum</h4>
                    <small class="text-muted">Gabungan transaksi Buku Kas Tunai dan Buku Bank per periode</small>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('keuangan.buku-kas-umum') }}" class="btn btn-sm btn-outline-secondary">Daftar Periode</a>
                @if($cashReport)<a href="{{ route('keuangan.buku-kas-tunai.show', $cashReport) }}" class="btn btn-sm btn-outline-primary">Buka Kas Tunai</a>@endif
                @if($bankReport)<a href="{{ route('keuangan.buku-bank.show', $bankReport) }}" class="btn btn-sm btn-outline-primary">Buka Buku Bank</a>@endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="ti ti-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="ti ti-alert-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="alert alert-primary bg-light-primary border-0 py-2 px-3 mb-3">
        <i class="ti ti-info-circle me-1"></i>Data di halaman ini merupakan gabungan dari dua buku. Tambah, ubah, verifikasi, dan tutup buku tetap dilakukan dari Buku Kas Tunai atau Buku Bank agar transaksi tidak tercatat dua kali.
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xl-3 col-sm-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Sisa saldo sebelumnya</small><div class="fw-bold fs-5 text-primary">Rp {{ number_format($displayOpeningBalanceCents / 100, 0, ',', '.') }}</div></div></div></div>
        <div class="col-xl-3 col-sm-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Total penerimaan</small><div class="fw-bold fs-5 text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div></div></div></div>
        <div class="col-xl-3 col-sm-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Total pengeluaran</small><div class="fw-bold fs-5 text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div></div></div></div>
        <div class="col-xl-3 col-sm-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Saldo gabungan</small><div class="fw-bold fs-5 text-dark">Rp {{ number_format(($displayOpeningBalanceCents / 100) + $totalPemasukan - $totalPengeluaran, 0, ',', '.') }}</div></div></div></div>
    </div>

    <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2"><i class="ti ti-list" style="color: #5D87FF;"></i>Rincian Buku Kas Umum</h5>
            <span class="badge bg-light text-primary border">{{ $months[$selectedMonth] ?? $selectedMonth }} {{ $selectedYear }}</span>
        </div>
        <div class="card-body p-0">
            <form method="GET" action="{{ route('keuangan.buku-kas-umum.show', [$selectedYear, $selectedMonth]) }}" class="row g-2 p-3 border-bottom align-items-end">
                <div class="col-xl-3 col-md-4">
                    <label class="form-label mb-1" for="filter-bulan">Bulan</label>
                    <select class="form-select" id="filter-bulan" name="bulan">
                        @foreach($months as $monthNumber => $monthName)<option value="{{ $monthNumber }}" @selected((int) $selectedMonth === $monthNumber)>{{ $monthName }}</option>@endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-md-3">
                    <label class="form-label mb-1" for="filter-tahun">Tahun</label>
                    <select class="form-select" id="filter-tahun" name="tahun">
                        @foreach($availableYears as $year)<option value="{{ $year }}" @selected((int) $selectedYear === (int) $year)>{{ $year }}</option>@endforeach
                        @if(!$availableYears->contains((int) $selectedYear))<option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>@endif
                    </select>
                </div>
                <div class="col-xl-2 col-md-3">
                    <label class="form-label mb-1" for="filter-sumber">Sumber</label>
                    <select class="form-select" id="filter-sumber" name="sumber">
                        <option value="semua" @selected($sourceFilter === 'semua')>Semua sumber</option>
                        <option value="Kas Tunai" @selected($sourceFilter === 'Kas Tunai')>Kas Tunai</option>
                        <option value="Bank" @selected($sourceFilter === 'Bank')>Bank</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-8"><label class="form-label mb-1" for="filter-cari">Cari transaksi</label><input class="form-control" id="filter-cari" name="q" value="{{ $search }}" placeholder="Kode akun atau transaksi"></div>
                <div class="col-xl-2 col-md-4 d-flex gap-2"><button type="submit" class="btn btn-primary"><i class="ti ti-filter me-1"></i>Filter</button><a href="{{ route('keuangan.buku-kas-umum.show', [$selectedYear, $selectedMonth]) }}" class="btn btn-outline-secondary">Reset</a></div>
            </form>

            @if(!$cashReport && !$bankReport)
            <div class="text-center text-muted py-5"><i class="ti ti-inbox fs-2 d-block mb-2"></i>Belum ada laporan Kas Tunai maupun Buku Bank untuk periode ini.</div>
            @else
            <div class="px-3 py-2 border-bottom d-flex flex-wrap gap-3 small text-muted">
                <span>Kas Tunai: <strong class="text-dark">{{ $cashReport ? ($cashReport->ditutup_pada ? 'Tutup buku' : 'Terbuka') : 'Belum ada laporan' }}</strong></span>
                <span>Buku Bank: <strong class="text-dark">{{ $bankReport ? ($bankReport->ditutup_pada ? 'Tutup buku' : 'Terbuka') : 'Belum ada laporan' }}</strong></span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;">
                        <tr><th class="ps-3" width="50">No</th><th>Tanggal</th><th>Sumber</th><th>Kode Akun</th><th>Transaksi</th><th>Lampiran</th><th class="text-end">Penerimaan</th><th class="text-end">Pengeluaran</th><th class="text-end">Saldo Gabungan</th><th class="text-center">Status</th><th class="text-center pe-3">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $index => $entry)
                        <tr @if($entry['transaksi'] === 'Sisa Saldo Bulan Sebelumnya') class="table-light" @endif>
                            <td class="ps-3">{{ $index + 1 }}</td>
                            <td class="text-nowrap">{{ $entry['tanggal']->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $entry['source'] === 'Bank' ? 'bg-light-info text-info' : 'bg-light-warning text-warning' }}">{{ $entry['source'] }}</span></td>
                            <td>{{ $entry['kode_akun'] }}</td>
                            <td class="fw-semibold">{{ $entry['transaksi'] }}</td>
                            <td>
                                @forelse($entry['lampiran'] as $file)
                                    <a href="{{ asset($file->file_path) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary py-0 px-1 mb-1" title="Lihat bukti"><i class="ti ti-paperclip"></i></a>
                                @empty <span class="text-muted">—</span> @endforelse
                            </td>
                            <td class="text-end text-success">{{ $entry['pemasukan'] !== null ? 'Rp '.number_format($entry['pemasukan'], 0, ',', '.') : '—' }}</td>
                            <td class="text-end text-danger">{{ $entry['pengeluaran'] !== null ? 'Rp '.number_format($entry['pengeluaran'], 0, ',', '.') : '—' }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($entry['saldo'], 0, ',', '.') }}</td>
                            <td class="text-center"><span class="badge {{ $entry['terverifikasi'] ? 'bg-light-success text-success' : 'bg-light-warning text-warning' }}">{{ $entry['terverifikasi'] ? 'Disetujui' : 'Menunggu' }}</span></td>
                            <td class="text-center pe-3"><a href="{{ $entry['report_url'] }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat buku sumber"><i class="ti ti-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="11" class="text-center text-muted py-5"><i class="ti ti-search fs-2 d-block mb-2"></i>Tidak ada transaksi yang sesuai filter.</td></tr>
                        @endforelse
                    </tbody>
                    @if($entries->isNotEmpty())
                    <tfoot class="table-light fw-semibold"><tr><td colspan="6" class="ps-3">Total transaksi periode ini</td><td class="text-end text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td><td class="text-end text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td><td class="text-end">Rp {{ number_format(($displayOpeningBalanceCents / 100) + $totalPemasukan - $totalPengeluaran, 0, ',', '.') }}</td><td colspan="2"></td></tr></tfoot>
                    @endif
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
