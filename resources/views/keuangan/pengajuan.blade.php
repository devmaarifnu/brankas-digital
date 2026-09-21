@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Keuangan</a></li>
            <li><a href="javascript:void(0)">Pengajuan</a></li>
        </ul>
    </nav>

    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-clipboard-list fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Pengajuan Keuangan</h4>
                    <small class="text-muted">Input dan kelola pengajuan dana operasional lembaga</small>
                </div>
            </div>
        </div>
    </div>

    @if(session("success"))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
        <i class="ti ti-check me-2 text-dark fs-5"></i>{{ session("success") }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-plus" style="color: #5D87FF;"></i>
                        <span>Buat Pengajuan</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route("keuangan.pengajuan.store") }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Pengajuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" required placeholder="Judul pengajuan dana">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah (Rp)</label>
                            <input type="number" class="form-control" name="jumlah" placeholder="Contoh: 5000000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="3" placeholder="Uraian peruntukan dana..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-primary">Upload Dokumen Pendukung</label>
                            <input type="file" class="form-control" name="file_bukti" accept="application/pdf,image/*">
                        </div>
                        <button type="submit" class="btn btn-primary fw-semibold w-100 py-2 shadow-sm">
                            <i class="ti ti-device-floppy me-1"></i>Kirim Pengajuan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-list" style="color: #5D87FF;"></i>
                        <span>Daftar Pengajuan</span>
                    </h5>
                    <span class="badge bg-light text-primary border">{{ $data->count() }} Data</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;">
                                <tr>
                                    <th class="ps-3" width="40">#</th>
                                    <th>Judul</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Dokumen</th>
                                    <th class="pe-3">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $i => $item)
                                <tr>
                                    <td class="ps-3">{{ $i+1 }}</td>
                                    <td class="fw-semibold text-dark">{{ $item->judul }}</td>
                                    <td class="fw-bold text-success">{{ $item->jumlah ? "Rp ".number_format($item->jumlah,0,",",".") : "-" }}</td>
                                    <td><small class="text-muted">{{ Str::limit($item->keterangan, 35) }}</small></td>
                                    <td>
                                        @if($item->status==="Menunggu")
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Menunggu</span>
                                        @elseif($item->status==="Disetujui")
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->file_bukti)
                                        <a href="{{ asset($item->file_bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="pe-3"><small class="text-muted">{{ $item->created_at->format("d/m/Y") }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="ti ti-inbox fs-2 d-block mb-2 text-muted"></i>
                                        Belum ada data pengajuan keuangan.
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