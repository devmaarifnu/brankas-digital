@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Surat Surat Berharga</a></li>
            <li><a href="javascript:void(0)">Arsip Surat Tanah</a></li>
        </ul>
    </nav>

    {{-- Top Header Card --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-file-certificate fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Arsip Surat Tanah</h4>
                    <small class="text-muted">Rekapitulasi dokumen sertifikat tanah yang tersimpan di brankas</small>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSuratTanah">
                    <i class="ti ti-plus me-1"></i>Tambah Surat Tanah
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
                <span>Daftar Arsip Surat Tanah</span>
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
                <table class="table table-hover align-middle mb-0" id="tblSuratTanah">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;">
                        <tr>
                            <th class="ps-3" width="40">#</th>
                            <th>Jenis Sertifikat</th>
                            <th>Nomor Sertifikat</th>
                            <th>Luas (M2)</th>
                            <th>Nama Sertifikat</th>
                            <th>Desa/Kelurahan</th>
                            <th>Kecamatan</th>
                            <th>Kabupaten/Kota</th>
                            <th>Provinsi</th>
                            <th>Dokumen</th>
                            <th>Nama Petugas</th>
                            <th>Tanggal Input</th>
                            <th>Keterangan</th>
                            <th class="text-center pe-3" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $item)
                        <tr class="{{ $item->warna_merah ? 'table-danger' : '' }}">
                            <td class="ps-3">{{ $i+1 }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->jenis_sertifikat ?? 'SHM' }}</span></td>
                            <td><code class="fw-semibold">{{ $item->nomor_sertifikat ?? '-' }}</code></td>
                            <td>{{ $item->luas ? $item->luas . ' m²' : '-' }}</td>
                            <td class="fw-semibold text-dark">{{ $item->nama_sertifikat ?? $item->nama_dokumen }}</td>
                            <td><small>{{ $item->desa_kelurahan ?? '-' }}</small></td>
                            <td><small>{{ $item->kecamatan ?? '-' }}</small></td>
                            <td><small>{{ $item->kabupaten_kota ?? '-' }}</small></td>
                            <td><small>{{ $item->provinsi ?? '-' }}</small></td>
                            <td class="text-center">
                                @if($item->file_dokumen)
                                    <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 shadow-sm btn-preview"
                                            data-title="{{ $item->nama_sertifikat ?? $item->nama_dokumen }}"
                                            data-url="{{ asset($item->file_dokumen) }}"
                                            title="Lihat Dokumen PDF">
                                        <i class="ti ti-file-text me-1"></i>PDF
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $item->nama_petugas ?? '-' }}</small></td>
                            <td><small class="text-muted">{{ $item->tgl_input ? $item->tgl_input->format('d/m/Y') : ($item->created_at ? $item->created_at->format('d/m/Y') : '-') }}</small></td>
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
                                    <a href="{{ route('brangkas.surat-tanah.edit', $item->id) }}" class="btn btn-sm btn-outline-warning w-100 py-1 px-2 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap shadow-sm" title="Edit Data">
                                        <i class="ti ti-pencil"></i> Edit
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('brangkas.surat-tanah.destroy', $item->id) }}" method="POST" class="w-100 m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                            <td colspan="14" class="text-center text-muted py-5">
                                <i class="ti ti-inbox fs-2 d-block mb-2 text-muted"></i>
                                <span>Belum ada data arsip surat tanah.</span>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSuratTanah">
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
<div class="modal fade" id="modalTambahSuratTanah" tabindex="-1" aria-labelledby="modalTambahSuratTanahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3 shadow border-0">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalTambahSuratTanahLabel">
                    <i class="ti ti-plus" style="color: #5D87FF;"></i>
                    <span>Input Data Arsip Surat Tanah</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('brangkas.surat-tanah.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-primary bg-light-primary text-primary py-2 px-3 mb-3 d-flex align-items-center gap-2 border-0" style="font-size: 13px;">
                        <i class="ti ti-info-circle fs-5"></i>
                        <span>Isilah data kuesioner sertifikat tanah di bawah ini secara lengkap.</span>
                    </div>

                    {{-- 1. Jenis Sertifikat --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">1. Jenis Sertifikat <span class="text-danger">*</span></label>
                        <select class="form-select" name="jenis_sertifikat" id="st_jenis_sertifikat" required>
                            <option value="" selected disabled>-- Pilih Jenis Sertifikat --</option>
                            <option value="SHM">SHM (Sertifikat Hak Milik)</option>
                            <option value="Wakaf">Wakaf</option>
                            <option value="Hibah">Hibah</option>
                            <option value="SHGB">SHGB (Sertifikat Hak Guna Bangunan)</option>
                            <option value="SHGU">SHGU (Sertifikat Hak Guna Usaha)</option>
                            <option value="Hak Guna Pakai">Hak Guna Pakai</option>
                            <option value="Isi Sendiri">Isi Sendiri...</option>
                        </select>
                        <input type="text" class="form-control mt-2" name="jenis_sertifikat_custom" id="st_jenis_custom" placeholder="Tuliskan jenis sertifikat..." style="display:none;">
                    </div>

                    {{-- 2. Nomor Sertifikat --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">2. Nomor Sertifikat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nomor_sertifikat" required placeholder="Contoh: SHM-1029/Semarang/2021">
                    </div>

                    {{-- 3. Luas Sertifikat (M2) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">3. Luas Sertifikat (M2) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="luas" required placeholder="Contoh: 1.500">
                    </div>

                    {{-- 4. Nama Sertifikat (Atas Nama) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">4. Nama Sertifikat (Atas Nama) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_sertifikat" required placeholder="Nama pemegang hak atas tanah">
                    </div>

                    {{-- 5. Desa/Kelurahan & 6. Kecamatan --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">5. Desa/Kelurahan</label>
                            <input type="text" class="form-control" name="desa_kelurahan" placeholder="Nama desa / kelurahan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">6. Kecamatan</label>
                            <input type="text" class="form-control" name="kecamatan" placeholder="Nama kecamatan">
                        </div>
                    </div>

                    {{-- 7. Kabupaten/Kota & 8. Provinsi --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">7. Kabupaten/Kota</label>
                            <input type="text" class="form-control" name="kabupaten_kota" placeholder="Nama kabupaten / kota">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">8. Provinsi</label>
                            <input type="text" class="form-control" name="provinsi" placeholder="Nama provinsi">
                        </div>
                    </div>

                    {{-- 9. Upload Dokumen (Kunci Hanya PDF) --}}
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-semibold text-primary">
                            <i class="ti ti-file-upload me-1"></i>9. Upload Dokumen (Wajib PDF)
                        </label>
                        <input type="file" class="form-control" name="file_dokumen" accept=".pdf,application/pdf">
                        <div class="form-text text-danger"><i class="ti ti-info-circle me-1"></i>Hanya format file <strong>PDF</strong> yang diizinkan. Maksimal 15MB.</div>
                    </div>

                    {{-- 10. Nama Petugas & 11. Tanggal Input --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">10. Nama Petugas</label>
                            <input type="text" class="form-control" name="nama_petugas" value="{{ auth()->user()->name ?: auth()->user()->username }}" placeholder="Nama petugas penginput">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">11. Tanggal Input</label>
                            <input type="date" class="form-control" name="tgl_input" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    {{-- 12. Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">12. Keterangan <span class="text-danger">*</span></label>
                        <select class="form-select" name="keterangan" id="st_keterangan" required>
                            <option value="Dokumen Asli Ada" selected>Dokumen Asli Ada</option>
                            <option value="Hanya Fotocopy">Hanya Fotocopy</option>
                            <option value="Diagunkan">Diagunkan</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Isi Sendiri">Isi Sendiri...</option>
                        </select>
                        <input type="text" class="form-control mt-2" name="keterangan_custom" id="st_keterangan_custom" placeholder="Tuliskan keterangan khusus..." style="display:none;">
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3 bg-white">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-sm">
                        <i class="ti ti-device-floppy me-1"></i>Simpan Surat Tanah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL LENGKAP POPUP --}}
<div class="modal fade" id="modalDetailSuratTanah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3 shadow border-0">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-file-certificate" style="color: #5D87FF;"></i>
                    <span>Detail Arsip Surat Tanah</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr><th width="35%" class="bg-light">Jenis Sertifikat</th><td id="dt_jenis">-</td></tr>
                        <tr><th class="bg-light">Nomor Sertifikat</th><td id="dt_nomor">-</td></tr>
                        <tr><th class="bg-light">Luas Sertifikat</th><td id="dt_luas">-</td></tr>
                        <tr><th class="bg-light">Nama Sertifikat</th><td id="dt_nama" class="fw-bold">-</td></tr>
                        <tr><th class="bg-light">Desa / Kelurahan</th><td id="dt_desa">-</td></tr>
                        <tr><th class="bg-light">Kecamatan</th><td id="dt_kecamatan">-</td></tr>
                        <tr><th class="bg-light">Kabupaten / Kota</th><td id="dt_kabupaten">-</td></tr>
                        <tr><th class="bg-light">Provinsi</th><td id="dt_provinsi">-</td></tr>
                        <tr><th class="bg-light">Nama Petugas</th><td id="dt_petugas">-</td></tr>
                        <tr><th class="bg-light">Tanggal Input</th><td id="dt_tgl">-</td></tr>
                        <tr><th class="bg-light">Keterangan / Status</th><td id="dt_keterangan">-</td></tr>
                        <tr><th class="bg-light">Dokumen Terlampir</th><td id="dt_file">-</td></tr>
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
    // Dropdown custom "Isi Sendiri"
    $('#st_jenis_sertifikat').on('change', function() {
        if ($(this).val() === 'Isi Sendiri') {
            $('#st_jenis_custom').slideDown().prop('required', true);
        } else {
            $('#st_jenis_custom').slideUp().prop('required', false);
        }
    });

    $('#st_keterangan').on('change', function() {
        if ($(this).val() === 'Isi Sendiri') {
            $('#st_keterangan_custom').slideDown().prop('required', true);
        } else {
            $('#st_keterangan_custom').slideUp().prop('required', false);
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

        $('#dt_jenis').text(item.jenis_sertifikat || '-');
        $('#dt_nomor').text(item.nomor_sertifikat || '-');
        $('#dt_luas').text((item.luas || '-') + ' m²');
        $('#dt_nama').text(item.nama_sertifikat || item.nama_dokumen || '-');
        $('#dt_desa').text(item.desa_kelurahan || '-');
        $('#dt_kecamatan').text(item.kecamatan || '-');
        $('#dt_kabupaten').text(item.kabupaten_kota || '-');
        $('#dt_provinsi').text(item.provinsi || '-');
        $('#dt_petugas').text(item.nama_petugas || '-');
        $('#dt_tgl').text(item.tgl_input || '-');
        $('#dt_keterangan').html(item.warna_merah ? '<span class="badge bg-danger">' + (item.keterangan || 'Status Khusus') + '</span>' : '<span class="badge bg-success">' + (item.keterangan || 'Dokumen Asli Ada') + '</span>');
        
        if (fileUrl) {
            $('#dt_file').html('<a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-download me-1"></i>Unduh PDF</a>');
        } else {
            $('#dt_file').text('Tidak ada dokumen PDF');
        }

        new bootstrap.Modal(document.getElementById('modalDetailSuratTanah')).show();
    });
});
</script>
@endsection