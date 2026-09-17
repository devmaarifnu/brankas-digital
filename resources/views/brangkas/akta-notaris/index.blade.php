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
                @if(auth()->user()->canManageData())
                <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAktaNotaris">
                    <i class="ti ti-plus me-1"></i>Tambah Akta Notaris
                </button>
                @endif
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

    {{-- Filter & Search Card (Sama Persis Record of Transfer) --}}
    @include('brangkas.partials._record_table', [
        'filterField' => 'jenis_dokumen',
        'filterOptions' => $jenisList,
        'statusList' => $statusList,
        'placeholder' => 'Cari nomor dokumen, nama dokumen, notaris...'
    ])

    {{-- REKAP DATA (TABEL HORIZONTAL) --}}
    <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ti ti-list" style="color: #5D87FF;"></i>
                <span>Daftar Akta Notaris</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-primary border">{{ $data->total() ?? $data->count() }} Dokumen</span>
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
                            <td><small class="text-muted">{{ $item->user->name ?? auth()->user()->name }}</small></td>
                            <td><small class="text-muted">{{ $item->tgl_input ? $item->tgl_input->format('d/m/Y') : ($item->created_at ? $item->created_at->format('d/m/Y') : '-') }}</small></td>
                            <td class="text-center">
                                @if($item->file_dokumen)
                                    @php
                                        $ext = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
                                        $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    @endphp
                                    <button type="button" class="btn btn-sm {{ $isImg ? 'btn-outline-success' : 'btn-outline-primary' }} px-2 py-1 shadow-sm btn-preview"
                                            data-title="{{ $item->nama_dokumen ?? $item->nomor_dokumen }}"
                                            data-url="{{ asset($item->file_dokumen) }}"
                                            title="Lihat {{ $isImg ? 'Foto' : 'Dokumen PDF' }}">
                                        <i class="ti {{ $isImg ? 'ti-photo' : 'ti-file-text' }} me-1"></i>{{ $isImg ? 'Foto' : 'PDF' }}
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
                                            data-item='@json($item)'
                                            data-url='{{ $item->file_dokumen ? asset($item->file_dokumen) : '' }}'
                                            title="Lihat Detail Lengkap">
                                        <i class="ti ti-eye"></i> Detail
                                    </button>

                                    {{-- Edit --}}
                                    @if(auth()->user()->canManageData())
                                    <a href="{{ route('brangkas.akta-notaris.edit', $item->id) }}" class="btn btn-sm btn-outline-warning w-100 py-1 px-2 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap shadow-sm" title="Edit Data">
                                        <i class="ti ti-pencil"></i> Edit
                                    </a>
                                    @endif

                                    {{-- Hapus --}}
                                    @if(auth()->user()->isSuperAdmin())
                                    <form action="{{ route('brangkas.akta-notaris.destroy', $item->id) }}" method="POST" class="w-100 m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-1 px-2 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap shadow-sm" title="Hapus Data">
                                            <i class="ti ti-trash"></i> Hapus
                                        </button>
                                    </form>
                                    @endif
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
        @if(method_exists($data, 'hasPages') && $data->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4">
            {{ $data->links() }}
        </div>
        @endif
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
                            <input type="text" class="form-control bg-light" value="{{ auth()->user()->name ?: auth()->user()->username }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">9. Tanggal Input</label>
                            <input type="date" class="form-control" name="tgl_input" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    {{-- 10. Upload Dokumen / Foto (Kamera, Galeri, PDF) --}}
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-semibold text-primary d-flex align-items-center justify-content-between flex-wrap gap-1 mb-2">
                            <span><i class="ti ti-camera me-1"></i>10. Upload Dokumen / Foto Fisik</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fs-1">PDF / Foto / Kamera HP</span>
                        </label>
                        
                        {{-- Tombol Cepat Pilihan: Kamera HP, Galeri Foto, atau PDF --}}
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 btn-trigger-camera">
                                <i class="ti ti-camera fs-4"></i> Buka Kamera HP
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 btn-trigger-gallery">
                                <i class="ti ti-photo fs-4"></i> Pilih Foto / Galeri
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 btn-trigger-pdf">
                                <i class="ti ti-file-type-pdf fs-4"></i> Pilih File PDF
                            </button>
                        </div>

                        {{-- Hidden specialized inputs --}}
                        <input type="file" class="d-none input-camera" accept="image/*" capture="environment">
                        <input type="file" class="d-none input-gallery" accept="image/*">
                        <input type="file" class="d-none input-pdf" accept=".pdf,application/pdf">

                        {{-- Input file utama --}}
                        <input type="file" class="form-control main-upload-input" name="file_dokumen" accept=".pdf,application/pdf,image/*">
                        <div class="form-text text-muted small mt-1">
                            <i class="ti ti-info-circle me-1"></i>Mendukung <strong>PDF, Foto Kamera, atau Galeri HP</strong> (Maksimal 20MB).
                        </div>
                        <div class="preview-selected-file mt-2" style="display: none;"></div>
                    </div>

                    {{-- 11. Status --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">11. Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="keterangan" id="an_keterangan" required>
                            <option value="Tersedia" selected>Tersedia</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Diagunkan">Diagunkan</option>
                            <option value="Dihibahkan">Dihibahkan</option>
                            <option value="Dikembalikan">Dikembalikan</option>
                            <option value="Hanya Fotocopy">Hanya Fotocopy</option>
                            <option value="Isi Sendiri">Isi Sendiri...</option>
                        </select>
                        <input type="text" class="form-control mt-2" name="keterangan_custom" id="an_keterangan_custom" placeholder="Tuliskan status khusus..." style="display:none;">
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
                        <tr><th class="bg-light">Status</th><td id="dt_an_keterangan">-</td></tr>
                        <tr><th class="bg-light">Dokumen Terlampir</th><td id="dt_an_file">-</td></tr>
                    </tbody>
                </table>

                {{-- Riwayat Record of Transfer --}}
                <hr class="my-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-arrows-left-right text-primary"></i>
                        <span>Riwayat Record of Transfer / Serah Terima</span>
                    </h6>
                    <span class="badge bg-light text-primary border" id="dt_an_handovers_count">0 Riwayat</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="35">#</th>
                                <th>Status</th>
                                <th>Pihak Terkait</th>
                                <th>Petugas</th>
                                <th>Tgl Transaksi</th>
                                <th>Catatan</th>
                                <th width="60">Bukti</th>
                            </tr>
                        </thead>
                        <tbody id="dt_an_handovers_body">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
                <div id="dt_an_handovers_pagination"></div>
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

    // Helper Function Pratinjau Dokumen / Foto
    function openPreviewDoc(docUrl, docTitle) {
        if (!docUrl) return;
        var ext = docUrl.split('.').pop().toLowerCase().split('?')[0];
        var isImage = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'].includes(ext);

        $('#previewTitle').text((isImage ? 'Pratinjau Foto/Gambar: ' : 'Pratinjau Dokumen PDF: ') + docTitle);
        $('#btnOpenTab').attr('href', docUrl);
        $('#previewFilename').text(docTitle);

        if (isImage) {
            $('#previewContainer').html('<div class="p-3 d-flex align-items-center justify-content-center" style="min-height: 450px;"><img src="' + docUrl + '" class="img-fluid rounded shadow-sm" style="max-height: 75vh; max-width: 100%; object-fit: contain;"></div>');
        } else {
            $('#previewContainer').html('<iframe src="' + docUrl + '" style="width: 100%; height: 75vh; border: none;"></iframe>');
        }
        new bootstrap.Modal(document.getElementById('modalPreviewDoc')).show();
    }

    // Preview Button Click (Tabel & Modal)
    $(document).on('click', '.btn-preview', function() {
        var docUrl = $(this).data('url');
        var docTitle = $(this).data('title') || 'Dokumen / Foto';
        openPreviewDoc(docUrl, docTitle);
    });

    // Helper Upload: Buka Kamera HP, Galeri, dan PDF
    $(document).on('click', '.btn-trigger-camera', function() {
        $(this).closest('.p-3').find('.input-camera').trigger('click');
    });
    $(document).on('click', '.btn-trigger-gallery', function() {
        $(this).closest('.p-3').find('.input-gallery').trigger('click');
    });
    $(document).on('click', '.btn-trigger-pdf', function() {
        $(this).closest('.p-3').find('.input-pdf').trigger('click');
    });

    $(document).on('change', '.input-camera, .input-gallery, .input-pdf', function() {
        if (this.files && this.files[0]) {
            var $parent = $(this).closest('.p-3');
            var mainInput = $parent.find('.main-upload-input')[0];
            var dt = new DataTransfer();
            dt.items.add(this.files[0]);
            mainInput.files = dt.files;
            $(mainInput).trigger('change');
        }
    });

    $(document).on('change', '.main-upload-input', function() {
        var $parent = $(this).closest('.p-3');
        var $preview = $parent.find('.preview-selected-file');
        if (this.files && this.files[0]) {
            var file = this.files[0];
            var isImg = file.type.startsWith('image/');
            var sizeMb = (file.size / (1024 * 1024)).toFixed(2);
            var html = '<div class="alert alert-info py-2 px-3 mb-0 d-flex align-items-center gap-2 flex-wrap">' +
                       '  <i class="ti ' + (isImg ? 'ti-photo text-success' : 'ti-file-type-pdf text-danger') + ' fs-5"></i>' +
                       '  <div class="flex-grow-1"><strong class="d-block text-truncate" style="max-width:250px;">' + file.name + '</strong><small class="text-muted">' + sizeMb + ' MB</small></div>';
            if (isImg) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $preview.html(html + '<img src="' + e.target.result + '" class="rounded border ms-auto" style="height:45px;width:45px;object-fit:cover;"></div>').slideDown();
                };
                reader.readAsDataURL(file);
            } else {
                $preview.html(html + '</div>').slideDown();
            }
        } else {
            $preview.empty().slideUp();
        }
    });

    // Detail Modal
    $('.btn-detail').on('click', function() {
        console.log('btn-detail clicked (akta)');
        var item = $(this).data('item');
        console.log('item data:', item, typeof item);
        var fileUrl = $(this).data('url');

        if (!item || typeof item !== 'object') {
            try { item = JSON.parse(item); } catch(e) { console.error('JSON parse error:', e); alert('Gagal membaca data item. Cek console browser.'); return; }
        }

        $('#dt_an_jenis').text(item.jenis_dokumen || item.jenis_sertifikat || '-');
        $('#dt_an_nomor').text(item.nomor_dokumen || item.nomor_akta || '-');
        $('#dt_an_nama').text(item.nama_dokumen || item.nama_sertifikat || '-');
        $('#dt_an_tgldok').text(item.tgl_dokumen || item.tanggal_akta || '-');
        $('#dt_an_notaris').text(item.nama_notaris || '-');
        $('#dt_an_alamat').text(item.alamat_notaris || item.alamat || '-');
        $('#dt_an_telp').text(item.telp_notaris || '-');
        $('#dt_an_petugas').text(item.nama_petugas || (item.user ? item.user.name : '-'));
        $('#dt_an_tgl').text(item.tgl_input || '-');
        $('#dt_an_keterangan').html(item.warna_merah ? '<span class="badge bg-danger">' + (item.keterangan || 'Status Khusus') + '</span>' : '<span class="badge bg-success">' + (item.keterangan || 'Dokumen Asli Ada') + '</span>');
        
        if (fileUrl) {
            var ext = fileUrl.split('.').pop().toLowerCase().split('?')[0];
            var isImage = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'].includes(ext);
            var btnIcon = isImage ? 'ti-photo' : 'ti-file-type-pdf';
            var btnLabel = isImage ? 'Lihat Foto' : 'Lihat Dokumen';

            $('#dt_an_file').html(
                '<button type="button" class="btn btn-sm btn-primary me-2 btn-preview" data-url="' + fileUrl + '" data-title="' + (item.nama_dokumen || item.nama_sertifikat || '') + '"><i class="ti ' + btnIcon + ' me-1"></i>' + btnLabel + '</button>' +
                '<a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="ti ti-external-link me-1"></i>Buka Tab Baru</a>'
            );
        } else {
            $('#dt_an_file').text('Tidak ada berkas/foto terlampir');
        }

        // Render Riwayat Handover
        var handovers = item.handovers || [];
        $('#dt_an_handovers_count').text(handovers.length + ' Riwayat');
        var $tbody = $('#dt_an_handovers_body');
        $tbody.empty();

        if (handovers.length === 0) {
            $tbody.append('<tr><td colspan="7" class="text-center text-muted py-3"><i class="ti ti-inbox me-1"></i>Belum ada riwayat serah terima untuk dokumen ini.</td></tr>');
        } else {
            $.each(handovers, function(i, h) {
                var badge = '<span class="badge bg-secondary">' + (h.status || '-') + '</span>';
                if (h.status === 'Dipinjam') badge = '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"><i class="ti ti-hand-stop me-1"></i>Dipinjam</span>';
                else if (h.status === 'Diagunkan') badge = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25"><i class="ti ti-building-bank me-1"></i>Diagunkan</span>';
                else if (h.status === 'Dihibahkan') badge = '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"><i class="ti ti-gift me-1"></i>Dihibahkan</span>';
                else if (h.status === 'Dikembalikan') badge = '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="ti ti-check me-1"></i>Dikembalikan</span>';

                var pihak = '-';
                if (h.status === 'Dipinjam') {
                    pihak = '<div><strong>' + (h.nama_peminjam || '-') + '</strong></div><small class="text-muted">' + (h.no_telp_peminjam || '') + '</small>';
                } else if (h.status === 'Diagunkan') {
                    pihak = '<div><strong>' + (h.nama_bank || '-') + '</strong></div><small class="text-muted">PJ: ' + (h.penanggung_agunan || '-') + ' (' + (h.jangka_agunan || '-') + ')</small>';
                } else if (h.status === 'Dihibahkan') {
                    pihak = '<div><strong>' + (h.nama_penerima || '-') + '</strong></div><small class="text-muted">' + (h.no_telp_penerima || '') + '</small>';
                } else if (h.status === 'Dikembalikan') {
                    pihak = '<div><strong>Dari: ' + (h.nama_peminjam || h.nama_penerima || '-') + '</strong></div><small class="text-muted">' + (h.no_telp_peminjam || h.no_telp_penerima || '') + '</small>';
                }

                var tgl = h.tgl_serahterima ? h.tgl_serahterima.substring(0, 10) : '-';
                var petugas = (h.user && h.user.name) ? h.user.name : (h.nama_petugas || '-');
                var bukti = h.file_bukti ? '<a href="/' + h.file_bukti.replace(/^\//, '') + '" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat Bukti"><i class="ti ti-eye"></i></a>' : '<span class="text-muted">-</span>';

                $tbody.append('<tr>' +
                    '<td class="text-center">' + (i+1) + '</td>' +
                    '<td>' + badge + '</td>' +
                    '<td>' + pihak + '</td>' +
                    '<td><small class="text-muted">' + petugas + '</small></td>' +
                    '<td><small class="text-muted">' + tgl + '</small></td>' +
                    '<td><small>' + (h.catatan || '-') + '</small></td>' +
                    '<td class="text-center">' + bukti + '</td>' +
                '</tr>');
            });
            setupHistoryPagination('dt_an_handovers_body', 'dt_an_handovers_pagination', 5);
        }

        if (handovers.length === 0) {
            $('#dt_an_handovers_pagination').empty();
        }

        new bootstrap.Modal(document.getElementById('modalDetailAkta')).show();
    });

    function setupHistoryPagination(tbodyId, paginationContainerId, pageSize) {
        pageSize = pageSize || 5;
        var $rows = $('#' + tbodyId + ' tr');
        var totalRows = $rows.length;
        var $pager = $('#' + paginationContainerId);
        $pager.empty();

        if (totalRows <= pageSize) {
            $rows.show();
            return;
        }

        var totalPages = Math.ceil(totalRows / pageSize);
        var currentPage = 1;

        function showPage(page) {
            currentPage = page;
            var start = (page - 1) * pageSize;
            var end = start + pageSize;
            $rows.hide().slice(start, end).show();

            $pager.find('.page-info').text('Halaman ' + currentPage + ' dari ' + totalPages + ' (' + totalRows + ' riwayat)');
            $pager.find('.btn-prev').prop('disabled', currentPage === 1);
            $pager.find('.btn-next').prop('disabled', currentPage === totalPages);
        }

        var html = '<div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">' +
                   '  <small class="text-muted page-info"></small>' +
                   '  <div class="btn-group btn-group-sm">' +
                   '    <button type="button" class="btn btn-outline-primary btn-prev"><i class="ti ti-chevron-left me-1"></i>Prev</button>' +
                   '    <button type="button" class="btn btn-outline-primary btn-next">Next<i class="ti ti-chevron-right ms-1"></i></button>' +
                   '  </div>' +
                   '</div>';
        $pager.html(html);

        $pager.find('.btn-prev').on('click', function() {
            if (currentPage > 1) showPage(currentPage - 1);
        });
        $pager.find('.btn-next').on('click', function() {
            if (currentPage < totalPages) showPage(currentPage + 1);
        });

        showPage(1);
    }
});
</script>
@endsection