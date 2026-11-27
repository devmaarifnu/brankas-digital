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
        <div class="card-body py-3 px-4 d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;"><i class="ti ti-report-money fs-5" style="color: #5D87FF;"></i></div>
            <div><h4 class="mb-0 fw-bold text-dark">Buku Kas Umum</h4><small class="text-muted">Rekap gabungan Buku Kas Tunai dan Buku Bank per bulan</small></div>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="ti ti-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="ti ti-alert-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="alert alert-primary bg-light-primary border-0 py-2 px-3 mb-3"><i class="ti ti-info-circle me-1"></i>Setiap periode mengikuti laporan yang tersedia di Buku Kas Tunai dan/atau Buku Bank. Buka rincian untuk melihat transaksi gabungannya.</div>

    <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2"><i class="ti ti-calendar-month" style="color: #5D87FF;"></i>Daftar Periode Buku Kas Umum</h5>
            <span class="badge bg-light text-primary border">{{ $data->count() }} Periode</span>
        </div>
        <div class="card-body p-0">
            <form method="GET" action="{{ route('keuangan.buku-kas-umum') }}" class="row g-2 p-3 border-bottom align-items-end">
                <div class="col-xl-3 col-md-4"><label class="form-label mb-1" for="filter-cari">Pencarian</label><input type="search" class="form-control" id="filter-cari" name="q" value="{{ $search }}" placeholder="Cari bulan atau tahun"></div>
                <div class="col-xl-3 col-md-4">
                    <label class="form-label mb-1" for="filter-bulan">Filter bulan</label>
                    <select class="form-select" id="filter-bulan" name="bulan"><option value="">Semua bulan</option>@foreach($months as $monthNumber => $monthName)<option value="{{ $monthNumber }}" @selected((string) $selectedMonth === (string) $monthNumber)>{{ $monthName }}</option>@endforeach</select>
                </div>
                <div class="col-xl-3 col-md-4">
                    <label class="form-label mb-1" for="filter-tahun">Filter tahun</label>
                    <select class="form-select" id="filter-tahun" name="tahun"><option value="">Semua tahun</option>@foreach($availableYears as $year)<option value="{{ $year }}" @selected((string) $selectedYear === (string) $year)>{{ $year }}</option>@endforeach</select>
                </div>
                <div class="col-xl-3 col-md-12 d-flex flex-nowrap align-items-end justify-content-xl-end gap-2"><button type="submit" class="btn btn-primary flex-shrink-0"><i class="ti ti-filter me-1"></i>Filter</button><a href="{{ route('keuangan.buku-kas-umum') }}" class="btn btn-outline-secondary flex-shrink-0">Reset</a></div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;"><tr><th class="ps-3" width="50">No</th><th>Periode</th><th>Status Kas Tunai</th><th>Status Buku Bank</th><th class="text-end">Total Penerimaan</th><th class="text-end">Total Pengeluaran</th><th class="text-end">Saldo Gabungan</th><th class="text-center pe-3" width="100">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($data as $index => $period)
                        <tr>
                            <td class="ps-3">{{ $index + 1 }}</td>
                            <td class="fw-semibold text-dark">{{ $months[$period->bulan] ?? $period->bulan }} {{ $period->tahun }}</td>
                            <td>@if($period->kas_tunai)<span class="badge {{ $period->kas_tunai->ditutup_pada ? 'bg-light-secondary text-secondary' : 'bg-light-success text-success' }}">{{ $period->kas_tunai->ditutup_pada ? 'Tutup buku' : 'Terbuka' }}</span>@else<span class="text-muted">Belum ada</span>@endif</td>
                            <td>@if($period->bank)<span class="badge {{ $period->bank->ditutup_pada ? 'bg-light-secondary text-secondary' : 'bg-light-success text-success' }}">{{ $period->bank->ditutup_pada ? 'Tutup buku' : 'Terbuka' }}</span>@else<span class="text-muted">Belum ada</span>@endif</td>
                            <td class="text-end text-success">Rp {{ number_format($period->pemasukan, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">Rp {{ number_format($period->pengeluaran, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($period->saldo_akhir, 0, ',', '.') }}</td>
                            <td class="text-center pe-3"><a href="{{ route('keuangan.buku-kas-umum.show', [$period->tahun, $period->bulan]) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat rincian"><i class="ti ti-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-5"><i class="ti ti-inbox fs-2 d-block mb-2"></i>Belum ada laporan pada periode yang dipilih.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
