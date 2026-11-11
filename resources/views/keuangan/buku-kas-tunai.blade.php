@extends('template.layout')
@section('navbar') @include('template.nav') @endsection
@section('container')
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Keuangan</a></li>
            <li><a href="javascript:void(0)">Buku Kas Tunai</a></li>
        </ul>
    </nav>

    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-wallet fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Buku Kas Tunai</h4>
                    <small class="text-muted">Kelola laporan dan transaksi kas tunai per periode</small>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
        <i class="ti ti-check me-2 text-success fs-5"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
        <i class="ti ti-alert-circle me-2 fs-5"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
        <i class="ti ti-alert-triangle me-2 fs-5"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ti ti-list" style="color: #5D87FF;"></i><span>Daftar Laporan Buku Kas Tunai</span>
            </h5>
            <span class="badge bg-light text-primary border">{{ $data->total() }} Laporan</span>
        </div>
        <div class="card-body p-0">
            <form method="GET" action="{{ route('keuangan.buku-kas-tunai') }}" class="row g-2 p-3 border-bottom align-items-end">
                <div class="col-xl-3 col-md-4">
                    <label class="form-label mb-1" for="cari-laporan">Pencarian</label>
                    <input type="search" class="form-control" id="cari-laporan" name="q" value="{{ $search }}" placeholder="Cari bulan atau tahun">
                </div>
                <div class="col-xl-2 col-md-3">
                    <label class="form-label mb-1" for="filter-bulan">Filter bulan</label>
                    <select class="form-select" id="filter-bulan" name="bulan">
                        <option value="">Semua bulan</option>
                        @foreach($months as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected((string) $selectedMonth === (string) $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-md-3">
                    <label class="form-label mb-1" for="filter-tahun">Filter tahun</label>
                    <select class="form-select" id="filter-tahun" name="tahun">
                        <option value="">Semua tahun</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" @selected((string) $selectedYear === (string) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-5 col-md-12 d-flex flex-wrap justify-content-xl-end gap-2">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-filter me-1"></i>Filter</button>
                    <a href="{{ route('keuangan.buku-kas-tunai') }}" class="btn btn-outline-secondary">Reset</a>
                    @if(!auth()->user()->isViewer())
                    <button type="button" class="btn btn-success fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahLaporan"><i class="ti ti-plus me-1"></i>Tambah Laporan</button>
                    @endif
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;">
                        <tr>
                            <th class="ps-3" width="50">No</th>
                            <th>Periode</th>
                            <th>Judul Dokumen</th>
                            <th>Keterangan</th>
                            <th class="text-center pe-3" width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $report)
                        <tr>
                            <td class="ps-3">{{ ($data->firstItem() ?? 1) + $index }}</td>
                            <td class="fw-semibold text-dark">{{ $months[$report->bulan] ?? $report->bulan }} {{ $report->tahun }}</td>
                            <td>{{ $report->judul ?: '—' }}</td>
                            <td><small class="text-muted">{{ $report->keterangan ?: '—' }}</small></td>
                            <td class="pe-3">
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <a href="{{ route('keuangan.buku-kas-tunai.show', $report) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat laporan"><i class="ti ti-eye"></i></a>
                                    @if(!auth()->user()->isViewer())
                                    <a href="{{ route('keuangan.buku-kas-tunai.edit', $report) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Edit laporan"><i class="ti ti-edit"></i></a>
                                    <form action="{{ route('keuangan.buku-kas-tunai.destroy', $report) }}" method="POST" class="mb-0" onsubmit="return confirm('Hapus laporan ini beserta seluruh transaksinya?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus laporan"><i class="ti ti-trash"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-5"><i class="ti ti-inbox fs-2 d-block mb-2"></i>Belum ada laporan Buku Kas Tunai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($data->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">{{ $data->links() }}</div>
            @endif
        </div>
    </div>
</div>

@if(!auth()->user()->isViewer())
<div class="modal fade" id="modalTambahLaporan" tabindex="-1" aria-labelledby="modalTambahLaporanLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded-3 shadow border-0">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalTambahLaporanLabel"><i class="ti ti-plus" style="color:#5D87FF"></i>Tambah Laporan Buku Kas Tunai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('keuangan.buku-kas-tunai.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-primary bg-light-primary text-primary py-2 px-3 mb-3 border-0" style="font-size:13px"><i class="ti ti-info-circle me-1"></i>Buat satu laporan untuk setiap bulan dan tahun.</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bulan <span class="text-danger">*</span></label>
                            <select name="bulan" class="form-select" required>
                                <option value="">Pilih bulan</option>
                                @foreach($months as $monthNumber => $monthName)
                                    <option value="{{ $monthNumber }}" @selected(old('bulan', now()->month) == $monthNumber)>{{ $monthName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                            <select name="tahun" class="form-select" required>
                                <option value="">Pilih tahun</option>
                                @foreach($inputYears as $inputYear)
                                    <option value="{{ $inputYear }}" @selected(old('tahun', now()->year) == $inputYear)>{{ $inputYear }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Saldo Awal <span class="text-danger">*</span></label>
                            <div class="input-group"><span class="input-group-text">Rp</span><input type="number" name="saldo_awal" class="form-control" min="0" step="0.01" value="{{ old('saldo_awal', 0) }}" required></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Dokumen</label>
                        <input type="text" name="judul" class="form-control" maxlength="255" value="{{ old('judul') }}" placeholder="Judul laporan/dokumen">
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" maxlength="5000" placeholder="Keterangan laporan">{{ old('keterangan') }}</textarea>
                        <div class="form-text">Saldo awal menjadi dasar perhitungan transaksi pada periode ini.</div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3 bg-white">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4"><i class="ti ti-device-floppy me-1"></i>Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
