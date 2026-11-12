@extends('template.layout')
@section('navbar') @include('template.nav') @endsection
@section('container')
@php
    $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $periodLabel = ($months[$report->bulan] ?? $report->bulan).' '.$report->tahun;
    $dateDefault = $report->tahun === now()->year && $report->bulan === now()->month
        ? now()->format('Y-m-d')
        : sprintf('%04d-%02d-01', $report->tahun, $report->bulan);
@endphp
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Keuangan</a></li>
            <li><a href="{{ route('keuangan.buku-kas-tunai') }}">Buku Kas Tunai</a></li>
            <li><a href="javascript:void(0)">{{ $periodLabel }}</a></li>
        </ul>
    </nav>

    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-wallet fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Buku Kas Tunai — {{ $periodLabel }}</h4>
                    <small class="text-muted">{{ $canManage ? 'Kelola transaksi laporan kas tunai' : 'Rincian transaksi laporan kas tunai' }}</small>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($report->ditutup_pada)<span class="btn btn-outline-danger disabled"><i class="ti ti-lock me-1"></i>Buku Ditutup</span>@endif
                @if($canManage)
                    <a href="{{ route('keuangan.buku-kas-tunai.show', $report) }}" class="btn btn-outline-primary fw-semibold"><i class="ti ti-eye me-1"></i>Lihat</a>
                    <a href="{{ route('keuangan.buku-kas-tunai.akun') }}" class="btn btn-outline-secondary fw-semibold"><i class="ti ti-settings me-1"></i>Pengaturan Kode Akun</a>
                    <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTransaksi"><i class="ti ti-plus me-1"></i>Tambah Transaksi</button>
                @endif
                @if(!auth()->user()->isViewer())
                    <form method="POST" action="{{ route('keuangan.buku-kas-tunai.status', $report) }}" class="d-inline" onsubmit="return confirm('{{ $report->ditutup_pada ? 'Buka kembali buku ini?' : 'Tutup buku ini? Transaksi tidak dapat diubah sampai buku dibuka kembali.' }}')">
                        @csrf
                        <button class="btn {{ $report->ditutup_pada ? 'btn-warning' : 'btn-outline-danger' }} fw-semibold"><i class="ti {{ $report->ditutup_pada ? 'ti-lock-open' : 'ti-lock' }} me-1"></i>{{ $report->ditutup_pada ? 'Buka Buku' : 'Tutup Buku' }}</button>
                    </form>
                @endif
                <a href="{{ route('keuangan.buku-kas-tunai') }}" class="btn btn-light"><i class="ti ti-arrow-left me-1"></i>Kembali</a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="ti ti-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="ti ti-alert-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
        <i class="ti ti-alert-triangle me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted d-block mb-1">Periode</small><div class="fs-5 fw-bold text-dark">{{ $periodLabel }}</div></div></div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted d-block mb-1">Saldo Awal</small><div class="fs-5 fw-bold text-dark">Rp {{ number_format((float) $report->saldo_awal, 0, ',', '.') }}</div></div></div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted d-block mb-1">Total Penerimaan</small><div class="fs-5 fw-bold text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div></div></div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted d-block mb-1">Total Pengeluaran</small><div class="fs-5 fw-bold text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div></div></div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted d-block mb-1">Saldo Akhir</small><div class="fs-5 fw-bold text-primary">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div></div></div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2"><i class="ti ti-list" style="color:#5D87FF"></i><span>Daftar Transaksi</span></h5>
            <span class="badge bg-light text-primary border">{{ $transactions->count() }} Transaksi</span>
        </div>

        @if($canManage)
        <form method="GET" action="{{ route('keuangan.buku-kas-tunai.edit', $report) }}" class="row g-2 p-3 border-bottom align-items-end">
            <div class="col-md-5">
                <label class="form-label mb-1" for="cari-transaksi">Pencarian</label>
                <input type="search" class="form-control" id="cari-transaksi" name="q" value="{{ $search }}" placeholder="Cari transaksi atau kode akun">
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1" for="filter-kode-akun">Filter kode akun</label>
                <select class="form-select" id="filter-kode-akun" name="kode_akun">
                    <option value="">Semua kode akun</option>
                    @foreach($accountCodes as $accountCode)
                        <option value="{{ $accountCode->nama }}" @selected($selectedAccountCode === $accountCode->nama)>{{ $accountCode->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex justify-content-md-end gap-2">
                <button type="submit" class="btn btn-primary"><i class="ti ti-filter me-1"></i>Filter</button>
                <a href="{{ route('keuangan.buku-kas-tunai.edit', $report) }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;border-bottom:2px solid #ebf1f6">
                    <tr>
                        <th class="ps-3" width="50">No</th>
                        <th>Tanggal</th>
                        <th>Kode Akun</th>
                        <th>Transaksi</th>
                        <th class="text-end">Penerimaan</th>
                        <th class="text-end">Pengeluaran</th>
                        <th class="text-end">Saldo</th>
                        @if($canMutate)<th class="text-center pe-3" width="90">Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $transaction)
                    <tr>
                        <td class="ps-3">{{ $index + 1 }}</td>
                        <td class="text-nowrap">{{ $transaction->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $transaction->kode_akun }}</td>
                        <td>
                            <div class="fw-semibold">{{ $transaction->transaksi }}</div>
                            @if($transaction->terverifikasi)<small class="text-success"><i class="ti ti-circle-check me-1"></i>Terverifikasi</small>@endif
                        </td>
                        <td class="text-end text-success">{{ $transaction->jenis_transaksi === 'pemasukan' ? 'Rp '.number_format((float) $transaction->jumlah, 0, ',', '.') : '—' }}</td>
                        <td class="text-end text-danger">{{ $transaction->jenis_transaksi === 'pengeluaran' ? 'Rp '.number_format((float) $transaction->jumlah, 0, ',', '.') : '—' }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($transaction->saldo_berjalan, 0, ',', '.') }}</td>
                        @if($canMutate)
                        <td class="pe-3">
                            <div class="d-flex flex-column align-items-center gap-1">
                                @if($canVerify && !$report->ditutup_pada)<form action="{{ route('keuangan.buku-kas-tunai.transaksi.verify', [$report, $transaction]) }}" method="POST" class="mb-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $transaction->terverifikasi ? 'btn-success' : 'btn-outline-success' }} py-0 px-2" title="{{ $transaction->terverifikasi ? 'Batalkan verifikasi' : 'Verifikasi transaksi' }}">
                                        <i class="ti {{ $transaction->terverifikasi ? 'ti-check' : 'ti-check' }}"></i>
                                    </button>
                                </form>@endif
                                @if($canManage)
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" title="Edit transaksi" data-bs-toggle="modal" data-bs-target="#modalEditTransaksi{{ $transaction->id }}"><i class="ti ti-edit"></i></button>
                                <form action="{{ route('keuangan.buku-kas-tunai.transaksi.destroy', [$report, $transaction]) }}" method="POST" class="mb-0" onsubmit="return confirm('Hapus transaksi ini? Saldo akan dihitung ulang.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus transaksi"><i class="ti ti-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ $canMutate ? 8 : 7 }}" class="text-center text-muted py-5"><i class="ti ti-inbox fs-2 d-block mb-2"></i>Belum ada transaksi pada laporan ini.</td></tr>
                    @endforelse
                </tbody>
                <tfoot style="background:#f8fafc">
                    <tr class="fw-bold">
                        <td colspan="4" class="ps-3">Total</td>
                        <td class="text-end text-success">Rp {{ number_format($totalFilteredPemasukan, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($totalFilteredPengeluaran, 0, ',', '.') }}</td>
                        <td class="text-end text-primary">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                        @if($canMutate)<td></td>@endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@if($canManage)
@foreach($transactions as $transaction)
<div class="modal fade" id="modalEditTransaksi{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content rounded-3 shadow border-0">
        <div class="modal-header"><h5 class="modal-title fw-bold">Edit Transaksi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('keuangan.buku-kas-tunai.transaksi.update', [$report, $transaction]) }}" method="POST">@csrf
            <div class="modal-body"><div class="row g-3">
                <div class="col-md-6"><label class="form-label">Tanggal <span class="text-danger">*</span></label><input type="date" name="tanggal" class="form-control" value="{{ $transaction->tanggal->format('Y-m-d') }}" min="{{ sprintf('%04d-%02d-01', $report->tahun, $report->bulan) }}" max="{{ \Carbon\Carbon::create($report->tahun, $report->bulan, 1)->endOfMonth()->format('Y-m-d') }}" required></div>
                <div class="col-md-6"><label class="form-label">Jenis Transaksi <span class="text-danger">*</span></label><select name="jenis_transaksi" class="form-select js-edit-jenis" data-target="akunEdit{{ $transaction->id }}"><option value="pemasukan" @selected($transaction->jenis_transaksi === 'pemasukan')>Pemasukan</option><option value="pengeluaran" @selected($transaction->jenis_transaksi === 'pengeluaran')>Pengeluaran</option></select></div>
                <div class="col-md-6"><label class="form-label">Kode Akun <span class="text-danger">*</span></label><select name="kode_akun" id="akunEdit{{ $transaction->id }}" class="form-select" required>@foreach($accountCodes as $accountCode)<option value="{{ $accountCode->nama }}" data-jenis="{{ $accountCode->jenis_transaksi }}" @selected($transaction->kode_akun === $accountCode->nama)>{{ $accountCode->nama }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Nominal <span class="text-danger">*</span></label><input type="number" name="jumlah" class="form-control" min="0.01" step="0.01" value="{{ $transaction->jumlah }}" required></div>
                <div class="col-12"><label class="form-label">Transaksi <span class="text-danger">*</span></label><input type="text" name="transaksi" class="form-control" maxlength="255" value="{{ $transaction->transaksi }}" required></div>
            </div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan Perubahan</button></div>
        </form>
    </div></div>
</div>
@endforeach
<div class="modal fade" id="modalTambahTransaksi" tabindex="-1" aria-labelledby="modalTambahTransaksiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3 shadow border-0">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalTambahTransaksiLabel"><i class="ti ti-arrows-exchange" style="color:#5D87FF"></i>Tambah Transaksi — {{ $periodLabel }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('keuangan.buku-kas-tunai.transaksi.store', $report) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-primary bg-light-primary text-primary py-2 px-3 mb-3 border-0" style="font-size:13px"><i class="ti ti-info-circle me-1"></i>Pilih pemasukan atau pengeluaran. Saldo akan dihitung otomatis berdasarkan tanggal transaksi.</div>
                    <label class="form-label fw-semibold d-block">Jenis Transaksi <span class="text-danger">*</span></label>
                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <input class="btn-check" type="radio" name="jenis_transaksi" id="jenisPemasukan" value="pemasukan" autocomplete="off" {{ old('jenis_transaksi', 'pemasukan') === 'pemasukan' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-success w-100 py-2" for="jenisPemasukan"><i class="ti ti-trending-up me-1"></i>Pemasukan</label>
                        </div>
                        <div class="col-sm-6">
                            <input class="btn-check" type="radio" name="jenis_transaksi" id="jenisPengeluaran" value="pengeluaran" autocomplete="off" {{ old('jenis_transaksi') === 'pengeluaran' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-danger w-100 py-2" for="jenisPengeluaran"><i class="ti ti-trending-down me-1"></i>Pengeluaran</label>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $dateDefault) }}" min="{{ sprintf('%04d-%02d-01', $report->tahun, $report->bulan) }}" max="{{ \Carbon\Carbon::create($report->tahun, $report->bulan, 1)->endOfMonth()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kode Akun <span class="text-danger">*</span></label>
                            <select name="kode_akun" id="inputKodeAkun" class="form-select" required>
                                <option value="">Pilih kode akun</option>
                                @foreach($accountCodes as $accountCode)
                                    <option value="{{ $accountCode->nama }}" data-jenis="{{ $accountCode->jenis_transaksi }}" @selected(old('kode_akun') === $accountCode->nama)>{{ $accountCode->nama }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Daftar pilihan mengikuti jenis transaksi. Tambahkan pilihan di Pengaturan Kode Akun.</div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Transaksi <span class="text-danger">*</span></label>
                            <input type="text" name="transaksi" class="form-control" value="{{ old('transaksi') }}" maxlength="255" placeholder="Contoh: Pembelian alat tulis" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Jumlah Transaksi <span class="text-danger">*</span></label>
                            <div class="input-group"><span class="input-group-text">Rp</span><input type="number" name="jumlah" class="form-control" min="0.01" step="0.01" value="{{ old('jumlah') }}" placeholder="0" required></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3 bg-white">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4"><i class="ti ti-device-floppy me-1"></i>Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-edit-jenis').forEach(function (typeSelect) {
        const accountSelect = document.getElementById(typeSelect.dataset.target);
        const filter = function () {
            Array.from(accountSelect.options).forEach(function (option) {
                if (!option.dataset.jenis) return;
                option.hidden = option.dataset.jenis !== typeSelect.value;
            });
            if (accountSelect.selectedOptions[0]?.hidden) accountSelect.value = '';
        };
        typeSelect.addEventListener('change', filter);
        filter();
    });
    const typeInputs = document.querySelectorAll('input[name="jenis_transaksi"]');
    const accountSelect = document.getElementById('inputKodeAkun');
    if (!accountSelect) return;

    const filterAccounts = function () {
        const selectedType = document.querySelector('input[name="jenis_transaksi"]:checked')?.value;
        Array.from(accountSelect.options).forEach(function (option) {
            if (!option.dataset.jenis) return;
            option.hidden = Boolean(selectedType && option.dataset.jenis !== selectedType);
        });
        if (accountSelect.selectedOptions[0]?.hidden) accountSelect.value = '';
    };

    typeInputs.forEach(function (input) { input.addEventListener('change', filterAccounts); });
    filterAccounts();
});
</script>
@endif
@endsection
