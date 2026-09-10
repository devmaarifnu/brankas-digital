@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Surat Surat Berharga</a></li>
            <li><a href="javascript:void(0)">Akta Notaris</a></li>
        </ul>
    </nav>

    {{-- Top Header Card --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-certificate fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Akta Notaris</h4>
                    <small class="text-muted">Rekapitulasi dokumen akta notaris & SK yang tersimpan di brankas</small>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAktaNotaris">
                    <i class="ti ti-plus me-1"></i>Tambah Akta Notaris
                </button>
            </div>
        </div>
    </div>

    @if(session("success"))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="ti ti-check me-2 text-success fs-5"></i>{{ session("success") }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="ti ti-alert-triangle me-2 fs-5"></i><strong>Terdapat kolom yang belum terisi lengkap:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- REKAP DATA (TABEL HORIZONTAL) --}}
    <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ti ti-list" style="color: #5D87FF;"></i>
                <span>Daftar Akta Notaris</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-primary border">{{ $data->count() }} Dokumen</span>
                @if($data->where("warna_merah",true)->count() > 0)
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                    <i class="ti ti-alert-triangle me-1"></i>{{ $data->where("warna_merah",true)->count() }} status khusus / dipindah tangan
                </span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tblAktaNotaris">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;">
                        <tr>
                            <th class="ps-3" width="40">#</th>
                            <th>Jenis Dokumen</th>
                            <th>Nomor Dokumen</th>
                            <th>Nama Dokumen</th>
                            <th>Tanggal Dokumen</th>
                            <th>Nama Notaris</th>
                            <th>Alamat Notaris</th>
                            <th>Telp. Notaris</th>
                            <th>Nama Petugas</th>
                            <th>Tanggal Input</th>
                            <th>Dokumen</th>
                            <th>Keterangan</th>
                            <th class="text-center pe-3" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $item)
                        <tr class="{{ $item->warna_merah ? 'table-danger' : '' }}">
                            <td class="ps-3">{{ $i+1 }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->jenis_dokumen ?? ($item->jenis_sertifikat ?? 'Akta Notaris') }}</span></td>
                            <td><code class="fw-semibold">{{ $item->nomor_dokumen ?? ($item->nomor_akta ?? '-') }}</code></td>
                            <td class="fw-semibold text-dark">{{ $item->nama_dokumen ?? ($item->nama_sertifikat ?? '-') }}</td>
                            <td><small>{{ $item->tgl_dokumen ? $item->tgl_dokumen->format('d/m/Y') : ($item->tanggal_akta ? $item->tanggal_akta->format('d/m/Y') : '-') }}</small></td>
                            <td><small>{{ $item->nama_notaris ?? '-' }}</small></td>
                            <td><small>{{ Str::limit($item->alamat_notaris ?? ($item->alamat ?? '-'), 30) }}</small></td>
                            <td><small>{{ $item->telp_notaris ?? '-' }}</small></td>
                            <td><small class="text-muted">{{ $item->nama_petugas ?? '-' }}</small></td>
                            <td><small class="text-muted">{{ $item->tgl_input ? $item->tgl_input->format('d/m/Y') : ($item->created_at ? $item->created_at->format('d/m/Y') : '-') }}</small></td>
                            <td class="text-center">
                                @if($item->file_dokumen)
                                    <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 shadow-sm btn-preview"
                                            data-title="{{ $item->nama_dokumen ?? $item->nomor_dokumen }}"
                                            data-url="{{ asset($item->file_dokumen) }}"
                                            title="Lihat Dokumen PDF">
                                        <i class="ti ti-file-text me-1"></i>PDF
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->warna_merah)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">{{ $item->keterangan ?? 'Status Khusus' }}</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">{{ $item->keterangan ?? 'Dokumen Asli Ada' }}</span>
                                @endif
                            </td>
                            <td class="text-center pe-3" style="width: 120px;">
                                <div class="d-flex flex-column gap-1 mx-auto" style="width: 85px;">
                                    {{-- Lihat Detail --}}
                                    <button type="button" class="btn btn-sm btn-outline-info w-100 py-1 px-2 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap shadow-sm btn-detail"
                                            data-item="{{ json_encode($item) }}"
                                            data-url="{{ $item->file_dokumen ? asset($item->file_dokumen) : '' }}"
                                            title="Lihat Detail Lengkap">
                                        <i class="ti ti-eye"></i> Detail
                                    </button>

                                    {{-- Edit --}}
                                    <a href="{{ route('brangkas.akta-notaris.edit', $item->id) }}" class="btn btn-sm btn-outline-warning w-100 py-1 px-2 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap shadow-sm" title="Edit Data">
                                        <i class="ti ti-pencil"></i> Edit
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('brangkas.akta-notaris.destroy', $item->id) }}" method="POST" class="w-100 m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-1 px-2 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap shadow-sm" title="Hapus Data">
                                            <i class="ti ti-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted py-5">
                                <i class="ti ti-inbox fs-2 d-block mb-2 text-muted"></i>
                                <span>Belum ada data akta notaris.</span>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAktaNotaris">
                                        <i class="ti ti-plus me-1"></i>Tambah Sekarang
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL INPUT DATA (FORM VERTIKAL SESUAI KUESIONER) --}}
<div class="modal fade" id="modalTambahAktaNotaris" tabindex="-1" aria-labelledby="modalTambahAktaNotarisLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3 shadow border-0">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalTambahAktaNotarisLabel">
                    <i class="ti ti-plus" style="color: #5D87FF;"></i>
                    <span>Input Data Akta Notaris</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('brangkas.akta-notaris.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-primary bg-light-primary text-primary py-2 px-3 mb-3 d-flex align-items-center gap-2 border-0" style="font-size: 13px;">
                        <i class="ti ti-info-circle fs-5"></i>
                        <span>Isilah formulir kuesioner akta notaris di bawah ini secara lengkap.</span>
                    </div>

                    {{-- 1. Jenis Dokumen --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">1. Jenis Dokumen <span class="text-danger">*</span></label>
                        <select class="form-select" name="jenis_dokumen" id="an_jenis_dokumen" required>
                            <option value="Akta Notaris" selected>Akta Notaris</option>
                            <option value="SK Menkumham">SK Menkumham</option>
                            <option value="Isi Sendiri">Isi Sendiri...</option>
                        </select>
                        <input type="text" class="form-control mt-2" name="jenis_dokumen_custom" id="an_jenis_custom" placeholder="Tuliskan jenis dokumen..." style="display:none;">
                    </div>

                    {{-- 2. Nomor Dokumen --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">2. Nomor Dokumen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nomor_dokumen" required placeholder="Contoh: No. 12 / AHU-00129.AH.01.04.Tahun 2021">
                    </div>

                    {{-- 3. Nama Dokumen --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">3. Nama Dokumen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_dokumen" required placeholder="Contoh: Akta Pendirian LP Ma'arif NU / SK Pengesahan">
                    </div>

                    {{-- 4. Tanggal Dokumen & 5. Nama Notaris --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">4. Tanggal Dokumen</label>
                            <input type="date" class="form-control" name="tgl_dokumen" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">5. Nama Notaris</label>
                            <input type="text" class="form-control" name="nama_notaris" placeholder="Nama lengkap notaris & gelar">
                        </div>
                    </div>

                    {{-- 6. Alamat Notaris --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">6. Alamat Notaris</label>
                        <textarea class="form-control" name="alamat_notaris" rows="2" placeholder="Alamat kantor / kedudukan notaris..."></textarea>
                    </div>

                    {{-- 7. Telp. Notaris --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">7. Telp. Notaris</label>
                        <input type="text" class="form-control" name="telp_notaris" placeholder="Nomor telepon / HP notaris">
                    </div>

                    {{-- 8. Nama Petugas & 9. Tanggal Input --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">8. Nama Petugas</label>
                            <input type="text" class="form-control" name="nama_petugas" value="{{ auth()->user()->name ?: auth()->user()->username }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">9. Tanggal Input</label>
                            <input type="date" class="form-control" name="tgl_input" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    {{-- 10. Upload Dokumen (Kunci PDF) --}}
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-semibold text-primary">
                            <i class="ti ti-file-upload me-1"></i>10. Upload Dokumen (Wajib PDF)
                        </label>
                        <input type="file" class="form-control" name="file_dokumen" accept=".pdf,application/pdf">
                        <div class="form-text text-danger"><i class="ti ti-info-circle me-1"></i>Hanya format file <strong>PDF</strong> yang diizinkan. Maksimal 15MB.</div>
                    </div>

                    {{-- 11. Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">11. Keterangan <span class="text-danger">*</span></label>
                        <select class="form-select" name="keterangan" id="an_keterangan" required>
                            <option value="Dokumen Asli Ada" selected>Dokumen Asli Ada</option>
                            <option value="Hanya Fotocopy">Hanya Fotocopy</option>
                            <option value="Diagunkan">Diagunkan</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Isi Sendiri">Isi Sendiri...</option>
                        </select>
                        <input type="text" class="form-control mt-2" name="keterangan_custom" id="an_keterangan_custom" placeholder="Tuliskan keterangan..." style="display:none;">
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3 bg-white">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-sm">
                        <i class="ti ti-device-floppy me-1"></i>Simpan Akta Notaris
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL LENGKAP POPUP --}}
<div class="modal fade" id="modalDetailAkta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3 shadow border-0">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-certificate" style="color: #5D87FF;"></i>
                    <span>Detail Akta Notaris / SK</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr><th width="35%" class="bg-light">Jenis Dokumen</th><td id="dt_an_jenis">-</td></tr>
                        <tr><th class="bg-light">Nomor Dokumen</th><td id="dt_an_nomor">-</td></tr>
                        <tr><th class="bg-light">Nama Dokumen</th><td id="dt_an_nama" class="fw-bold">-</td></tr>
                        <tr><th class="bg-light">Tanggal Dokumen</th><td id="dt_an_tgldok">-</td></tr>
                        <tr><th class="bg-light">Nama Notaris</th><td id="dt_an_notaris">-</td></tr>
                        <tr><th class="bg-light">Alamat Notaris</th><td id="dt_an_alamat">-</td></tr>
                        <tr><th class="bg-light">Telp. Notaris</th><td id="dt_an_telp">-</td></tr>
                        <tr><th class="bg-light">Nama Petugas</th><td id="dt_an_petugas">-</td></tr>
                        <tr><th class="bg-light">Tanggal Input</th><td id="dt_an_tgl">-</td></tr>
                        <tr><th class="bg-light">Keterangan / Status</th><td id="dt_an_keterangan">-</td></tr>
                        <tr><th class="bg-light">Dokumen Terlampir</th><td id="dt_an_file">-</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer bg-white border-top px-4 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pratinjau Dokumen PDF --}}
<div class="modal fade" id="modalPreviewDoc" tabindex="-1" aria-labelledby="modalPreviewDocLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-3 shadow border-0">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalPreviewDocLabel">
                    <i class="ti ti-file-search" style="color: #5D87FF;"></i>
                    <span id="previewTitle">Pratinjau Dokumen PDF</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-light" style="min-height: 500px;" id="previewContainer">
            </div>
            <div class="modal-footer justify-content-between bg-white border-top px-4 py-3">
                <small class="text-muted" id="previewFilename"></small>
                <div class="d-flex gap-2">
                    <a href="#" target="_blank" class="btn btn-primary btn-sm fw-semibold shadow-sm" id="btnOpenTab">
                        <i class="ti ti-external-link me-1"></i>Buka PDF di Tab Baru
                    </a>
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section("scripts")
<script>
$(document).ready(function() {
    $('#an_jenis_dokumen').on('change', function() {
        if ($(this).val() === 'Isi Sendiri') {
            $('#an_jenis_custom').slideDown().prop('required', true);
        } else {
            $('#an_jenis_custom').slideUp().prop('required', false);
        }
    });

    $('#an_keterangan').on('change', function() {
        if ($(this).val() === 'Isi Sendiri') {
            $('#an_keterangan_custom').slideDown().prop('required', true);
        } else {
            $('#an_keterangan_custom').slideUp().prop('required', false);
        }
    });

    // Preview PDF
    $('.btn-preview').on('click', function() {
        var docUrl = $(this).data('url');
        var docTitle = $(this).data('title');
        $('#previewTitle').text('Pratinjau Dokumen PDF: ' + docTitle);
        $('#btnOpenTab').attr('href', docUrl);
        $('#previewFilename').text(docTitle);
        $('#previewContainer').html('<iframe src="' + docUrl + '" style="width: 100%; height: 75vh; border: none;"></iframe>');
        new bootstrap.Modal(document.getElementById('modalPreviewDoc')).show();
    });

    // Detail Modal
    $('.btn-detail').on('click', function() {
        var item = $(this).data('item');
        var fileUrl = $(this).data('url');

        $('#dt_an_jenis').text(item.jenis_dokumen || item.jenis_sertifikat || '-');
        $('#dt_an_nomor').text(item.nomor_dokumen || item.nomor_akta || '-');
        $('#dt_an_nama').text(item.nama_dokumen || item.nama_sertifikat || '-');
        $('#dt_an_tgldok').text(item.tgl_dokumen || item.tanggal_akta || '-');
        $('#dt_an_notaris').text(item.nama_notaris || '-');
        $('#dt_an_alamat').text(item.alamat_notaris || item.alamat || '-');
        $('#dt_an_telp').text(item.telp_notaris || '-');
        $('#dt_an_petugas').text(item.nama_petugas || '-');
        $('#dt_an_tgl').text(item.tgl_input || '-');
        $('#dt_an_keterangan').html(item.warna_merah ? '<span class="badge bg-danger">' + (item.keterangan || 'Status Khusus') + '</span>' : '<span class="badge bg-success">' + (item.keterangan || 'Dokumen Asli Ada') + '</span>');
        
        if (fileUrl) {
            $('#dt_an_file').html('<a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-download me-1"></i>Unduh PDF</a>');
        } else {
            $('#dt_an_file').text('Tidak ada dokumen PDF');
        }

        new bootstrap.Modal(document.getElementById('modalDetailAkta')).show();
    });
});
</script>
@endsection