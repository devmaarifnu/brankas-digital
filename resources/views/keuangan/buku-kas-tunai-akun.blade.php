@extends('template.layout')
@section('navbar') @include('template.nav') @endsection
@section('container')
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Keuangan</a></li>
            <li><a href="{{ route('keuangan.buku-kas-tunai') }}">Buku Kas Tunai</a></li>
            <li><a href="javascript:void(0)">Pengaturan Kode Akun</a></li>
        </ul>
    </nav>

    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border:1px solid #ebf1f6">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background:rgba(93,135,255,.1);width:46px;height:46px;min-width:46px"><i class="ti ti-settings fs-5" style="color:#5D87FF"></i></div>
                <div><h4 class="mb-0 fw-bold text-dark">Pengaturan Kode Akun</h4><small class="text-muted">Kelola pilihan kode akun untuk Buku Kas Tunai</small></div>
            </div>
            <a href="{{ route('keuangan.buku-kas-tunai') }}" class="btn btn-light"><i class="ti ti-arrow-left me-1"></i>Kembali ke Laporan</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="ti ti-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="ti ti-alert-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">{{ $errors->first() }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 h-100" style="border:1px solid #ebf1f6">
                <div class="card-header bg-white border-bottom py-3 px-4"><h5 class="mb-0 fw-bold text-dark">Tambah Kode Akun</h5></div>
                <div class="card-body p-4">
                    <form action="{{ route('keuangan.buku-kas-tunai.akun.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Transaksi <span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_transaksi" required>
                                <option value="">Pilih jenis transaksi</option>
                                <option value="pemasukan" @selected(old('jenis_transaksi') === 'pemasukan')>Pemasukan</option>
                                <option value="pengeluaran" @selected(old('jenis_transaksi', 'pengeluaran') === 'pengeluaran')>Pengeluaran</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Kode Akun / Nama Akun <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" value="{{ old('nama') }}" maxlength="100" placeholder="Contoh: 1.22. Biaya Lain-lain" required>
                        </div>
                        <button type="submit" class="btn btn-primary fw-semibold w-100"><i class="ti ti-plus me-1"></i>Tambah Kode Akun</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3" style="border:1px solid #ebf1f6">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold text-dark">Daftar Kode Akun</h5>
                    <span class="badge bg-light text-primary border">{{ $accounts->count() }} Akun</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc;border-bottom:2px solid #ebf1f6">
                            <tr><th class="ps-3">No</th><th>Jenis Transaksi</th><th>Kode Akun / Nama Akun</th><th>Dipakai</th><th class="text-center pe-3">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $index => $account)
                            <tr>
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td><span class="badge {{ $account->jenis_transaksi === 'pemasukan' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($account->jenis_transaksi) }}</span></td>
                                <td class="fw-semibold">{{ $account->nama }}</td>
                                <td>{{ $account->transaksi_count }} transaksi</td>
                                <td class="pe-3">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <form action="{{ route('keuangan.buku-kas-tunai.akun.destroy', $account) }}" method="POST" class="mb-0" onsubmit="return confirm('Hapus kode akun ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus kode akun" {{ $account->transaksi_count ? 'disabled' : '' }}><i class="ti ti-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-5">Belum ada kode akun.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
