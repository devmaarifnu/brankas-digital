@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Keuangan</a></li>
            <li><a href="javascript:void(0)">Rekening Koran</a></li>
        </ul>
    </nav>

    {{-- Top Header Card --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                <i class="ti ti-file-text fs-5" style="color: #5D87FF;"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Rekening Koran</h4>
                <small class="text-muted">Upload dan kelola dokumen Rekening Koran</small>
            </div>
        </div>
    </div>

    @if(session("success"))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
        <i class="ti ti-check me-2 text-success fs-5"></i>{{ session("success") }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-upload" style="color: #5D87FF;"></i>
                        <span>Upload Dokumen</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route("keuangan.dokumen.store") }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="jenis" value="rekening_koran">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Periode <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-7">
                                    <select class="form-select" name="bulan" required>
                                        <option value="">Pilih bulan</option>
                                        @foreach([1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $monthNumber => $monthName)
                                            <option value="{{ $monthNumber }}" @selected(old('bulan', now()->month) == $monthNumber)>{{ $monthName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-5">
                                    <select class="form-select" name="tahun" required>
                                        <option value="">Tahun</option>
                                        @foreach($inputYears as $inputYear)
                                            <option value="{{ $inputYear }}" @selected(old('tahun', now()->year) == $inputYear)>{{ $inputYear }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Dokumen</label>
                            <input type="text" class="form-control" name="judul" placeholder="Judul dokumen (opsional)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary">File Dokumen <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="file_path" required accept="application/pdf,image/*">
                            <div class="form-text">Format: PDF, JPG, PNG. Maksimal 50MB.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="2" placeholder="Catatan dokumen..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary fw-semibold w-100 py-2 shadow-sm">
                            <i class="ti ti-device-floppy me-1"></i>Upload Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-files" style="color: #5D87FF;"></i>
                        <span>Daftar Dokumen Rekening Koran</span>
                    </h5>
                    <span class="badge bg-light text-primary border">{{ $data->count() }} Data</span>
                </div>
                <div class="card-body p-0">
                    <form method="GET" action="{{ route('keuangan.rekening-koran') }}" class="row g-2 p-3 border-bottom align-items-end">
                        <div class="col-sm-5 col-md-4">
                            <label class="form-label mb-1" for="filter-bulan">Filter bulan</label>
                            <select class="form-select form-select-sm" id="filter-bulan" name="bulan">
                                <option value="">Semua bulan</option>
                                @foreach([1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $monthNumber => $monthName)
                                    <option value="{{ $monthNumber }}" @selected((string) $selectedMonth === (string) $monthNumber)>{{ $monthName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4 col-md-3">
                            <label class="form-label mb-1" for="filter-tahun">Filter tahun</label>
                            <select class="form-select form-select-sm" id="filter-tahun" name="tahun">
                                <option value="">Semua tahun</option>
                                @foreach($years as $availableYear)
                                    <option value="{{ $availableYear }}" @selected((string) $selectedYear === (string) $availableYear)>{{ $availableYear }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3 col-md-5 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-filter me-1"></i>Filter</button>
                            <a href="{{ route('keuangan.rekening-koran') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;">
                                <tr>
                                    <th class="ps-3" width="40">#</th>
                                    <th>Periode</th>
                                    <th>Judul</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal Upload</th>
                                    <th class="pe-3 text-center" width="{{ $isSuperAdmin ? 120 : 90 }}">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $i => $item)
                                <tr>
                                    <td class="ps-3">{{ $i+1 }}</td>
                                    <td class="fw-semibold text-dark">{{ $item->periode_label }}</td>
                                    <td>{{ $item->judul ?? "-" }}</td>
                                    <td><small class="text-muted">{{ $item->keterangan ?? "-" }}</small></td>
                                    <td><small class="text-muted">{{ $item->created_at->format("d/m/Y") }}</small></td>
                                    <td class="pe-3 text-center text-nowrap">
                                        <a href="{{ asset($item->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat Dokumen">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @if($isSuperAdmin)
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" data-bs-toggle="collapse" data-bs-target="#edit-rekening-koran-{{ $item->id }}" aria-expanded="false" title="Edit teks">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @if($isSuperAdmin)
                                <tr class="collapse" id="edit-rekening-koran-{{ $item->id }}">
                                    <td colspan="6" class="bg-light p-3">
                                        <form action="{{ route('keuangan.rekening-koran.update', $item->id) }}" method="POST" class="row g-2 align-items-end">
                                            @csrf
                                            <div class="col-md-4">
                                                <label class="form-label mb-1">Judul</label>
                                                <input type="text" class="form-control form-control-sm" name="judul" maxlength="255" value="{{ $item->judul }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label mb-1">Keterangan</label>
                                                <input type="text" class="form-control form-control-sm" name="keterangan" value="{{ $item->keterangan }}">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success btn-sm w-100"><i class="ti ti-device-floppy me-1"></i>Simpan</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="{{ $isSuperAdmin ? 7 : 6 }}" class="text-center text-muted py-5">
                                        <i class="ti ti-inbox fs-2 d-block mb-2 text-muted"></i>
                                        Belum ada dokumen Rekening Koran.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
