@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Surat Surat Berharga</a></li>
            <li><a href="javascript:void(0)">Surat Kendaraan</a></li>
        </ul>
    </nav>

    {{-- Top Header Card --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-car fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Arsip Surat Kendaraan</h4>
                    <small class="text-muted">Rekapitulasi BPKB & STNK kendaraan lembaga yang tersimpan di brankas</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('brangkas.surat-kendaraan.export', request()->query()) }}" class="btn btn-success fw-semibold shadow-sm d-flex align-items-center gap-1" title="Export Rekap Surat Kendaraan ke Excel">
                    <i class="ti ti-file-spreadsheet fs-5"></i>
                    <span>Export Excel</span>
                </a>
                @if(auth()->user()->canManageData())
                <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSuratKendaraan">
                    <i class="ti ti-plus me-1"></i>Tambah Surat Kendaraan
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

    @if(session("error"))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="ti ti-alert-triangle me-2 fs-5"></i>{{ session("error") }}
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

    {{-- Filter & Search Card --}}
    @include('brangkas.partials._record_table', [
        'filterField' => 'jenis_surat',
        'filterLabel' => 'Jenis Surat',
        'filterOptions' => $jenisSuratList,
        'statusOptions' => $statusList,
        'officers' => $officers
    ])

    {{-- Table List --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-3" width="50">No</th>
                            <th>Jenis Surat</th>
                            <th>Nama Kendaraan</th>
                            <th>Nama Pemilik</th>
                            <th>No. Plat</th>
                            <th>No. Rangka</th>
                            <th>No. Mesin</th>
                            <th>Foto/PDF</th>
                            <th>Petugas</th>
                            <th>Tgl Input</th>
                            <th>Status</th>
                            <th class="text-center pe-3" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $item)
                        <tr class="{{ $item->warna_merah ? 'table-danger' : '' }}">
                            <td class="ps-3">{{ $data->firstItem() + $i }}</td>
                            <td>
                                <span class="badge {{ $item->jenis_surat === 'BPKB' ? 'bg-primary' : 'bg-info' }} bg-opacity-10 text-{{ $item->jenis_surat === 'BPKB' ? 'primary' : 'info' }} border border-{{ $item->jenis_surat === 'BPKB' ? 'primary' : 'info' }} border-opacity-25 fw-bold px-2 py-1">
                                    {{ $item->jenis_surat }}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark">{{ $item->nama_kendaraan }}</td>
                            <td><small>{{ $item->nama_pemilik ?? '-' }}</small></td>
                            <td><span class="badge bg-dark bg-opacity-10 text-dark border border-secondary border-opacity-25 fw-bold">{{ $item->no_plat ?? '-' }}</span></td>
                            <td><code>{{ $item->no_rangka ?? '-' }}</code></td>
                            <td><code>{{ $item->no_mesin ?? '-' }}</code></td>
                            <td class="text-center">
                                @if($item->file_dokumen)
                                    @php
                                        $ext = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
                                        $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    @endphp
                                    <button type="button" class="btn btn-sm {{ $isImg ? 'btn-outline-success' : 'btn-outline-primary' }} px-2 py-1 shadow-sm btn-preview"
                                            data-title="{{ $item->jenis_surat }} - {{ $item->nama_kendaraan }}"
                                            data-url="{{ asset($item->file_dokumen) }}"
                                            title="Lihat {{ $isImg ? 'Foto' : 'Dokumen PDF' }}">
                                        <i class="ti {{ $isImg ? 'ti-photo' : 'ti-file-text' }} me-1"></i>{{ $isImg ? 'Foto' : 'PDF' }}
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $item->user->name ?? auth()->user()->name }}</small></td>
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
                                            data-item='@json($item)'
                                            data-url="{{ $item->file_dokumen ? asset($item->file_dokumen) : '' }}"
                                            title="Lihat Detail Lengkap">
                                        <i class="ti ti-eye"></i> Detail
                                    </button>

                                    {{-- Edit --}}
                                    @if(auth()->user()->canManageData())
                                    <a href="{{ route('brangkas.surat-kendaraan.edit', $item->id) }}" class="btn btn-sm btn-outline-warning w-100 py-1 px-2 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap shadow-sm" title="Edit Surat Kendaraan">
                                        <i class="ti ti-pencil"></i> Edit
                                    </a>
                                    @endif

                                    {{-- Hapus --}}
                                    @if(auth()->user()->isSuperAdmin())
                                    <form action="{{ route('brangkas.surat-kendaraan.destroy', $item->id) }}" method="POST" class="w-100 m-0" onsubmit="return confirm('Yakin ingin menghapus data surat kendaraan ini?')">
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
                            <td colspan="12" class="text-center py-4 text-muted">
                                <i class="ti ti-folder-off fs-6 d-block mb-1"></i>
                                Belum ada data Arsip Surat Kendaraan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($data->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $data->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL TAMBAH --}}
@if(auth()->user()->canManageData())
<div class="modal fade" id="modalTambahSuratKendaraan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-car text-primary fs-5"></i> Tambah Arsip Surat Kendaraan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('brangkas.surat-kendaraan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Surat <span class="text-danger">*</span></label>
                            <select name="jenis_surat" class="form-select" required>
                                <option value="BPKB">BPKB (Buku Pemilik Kendaraan Bermotor)</option>
                                <option value="STNK">STNK (Surat Tanda Nomor Kendaraan)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pilih Kendaraan dari Data Aset</label>
                            <select name="data_aset_id" id="select_data_aset_id" class="form-select">
                                <option value="">-- Pilih Kendaraan (Opsional) --</option>
                                @foreach($kendaraanList as $k)
                                    <option value="{{ $k->id }}" data-nama="{{ $k->nama_barang ?: $k->nama_aset }}" data-merek="{{ $k->merek }}" data-sn="{{ $k->nomor_seri_model }}">
                                        {{ $k->nama_barang ?: $k->nama_aset }} {{ $k->merek ? '('.$k->merek.')' : '' }} - [{{ $k->nomor_registrasi }}]
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted fs-2">Pilih jika surat kendaraan ini berhubungan dengan Data Aset Lembaga</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Kendaraan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kendaraan" id="input_nama_kendaraan" class="form-control" placeholder="Contoh: Toyota Avanza Silver" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Pemilik (STNK/BPKB)</label>
                            <input type="text" name="nama_pemilik" class="form-control" placeholder="Nama atas nama kendaraan">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nomor Plat (No. Polisi)</label>
                            <input type="text" name="no_plat" class="form-control" placeholder="Contoh: B 1234 ABC">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nomor Rangka (VIN)</label>
                            <input type="text" name="no_rangka" id="input_no_rangka" class="form-control" placeholder="Nomor Rangka">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nomor Mesin</label>
                            <input type="text" name="no_mesin" class="form-control" placeholder="Nomor Mesin">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status / Keterangan</label>
                            <select name="keterangan" id="select_keterangan_tambah" class="form-select">
                                <option value="Dokumen Asli Ada">Tersedia (Dokumen Asli Ada)</option>
                                <option value="Dipinjam">Dipinjam</option>
                                <option value="Diagunkan">Diagunkan</option>
                                <option value="Dihibahkan">Dihibahkan</option>
                                <option value="Isi Sendiri">-- Isi Sendiri --</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="wrapper_keterangan_custom_tambah" style="display:none;">
                            <label class="form-label fw-semibold">Keterangan Custom</label>
                            <input type="text" name="keterangan_custom" class="form-control" placeholder="Ketik keterangan khusus...">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Input</label>
                            <input type="date" name="tgl_input" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Petugas Input</label>
                            <input type="text" name="nama_petugas" class="form-control bg-light text-dark fw-semibold" value="{{ auth()->user()->name ?: auth()->user()->username }}" readonly>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Upload Berkas / Foto Dokumen Surat Kendaraan</label>
                            <div class="card border bg-light p-3 rounded-3 mb-1">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-7">
                                        <label class="form-label small fw-semibold text-dark mb-1"><i class="ti ti-file-upload me-1 text-primary"></i>Pilih File (Galeri Foto / PDF)</label>
                                        <input type="file" name="file_dokumen" id="input_file_dokumen" class="form-control form-control-sm" accept="image/*,application/pdf,.pdf">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-semibold text-dark mb-1"><i class="ti ti-camera me-1 text-success"></i>Ambil Foto dari Kamera</label>
                                        <input type="file" id="input_camera_dokumen" class="form-control form-control-sm" accept="image/*" capture="environment">
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="ti ti-info-circle me-1 text-info"></i>Pilihan file: <span class="fw-semibold text-dark">Kamera Langsung</span>, <span class="fw-semibold text-dark">Foto Galeri (JPG/PNG)</span>, atau <span class="fw-semibold text-dark">Dokumen PDF</span> — <strong>Maksimal 20 MB</strong>.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="ti ti-device-floppy me-1"></i>Simpan Surat Kendaraan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetailSuratKendaraan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-info-circle text-info fs-5"></i> Detail Surat Kendaraan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <small class="text-muted d-block fw-semibold mb-1">JENIS SURAT</small>
                            <span id="detail_jenis_surat" class="fw-bold fs-4 text-primary"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <small class="text-muted d-block fw-semibold mb-1">STATUS SAAT INI</small>
                            <span id="detail_status" class="badge"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">Nama Kendaraan</small>
                        <span id="detail_nama_kendaraan" class="fw-semibold text-dark fs-3"></span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Nama Pemilik (STNK/BPKB)</small>
                        <span id="detail_nama_pemilik" class="fw-semibold text-dark"></span>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted d-block">Nomor Plat (No. Polisi)</small>
                        <span id="detail_no_plat" class="badge bg-dark text-white fw-bold px-2 py-1"></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Nomor Rangka (VIN)</small>
                        <code id="detail_no_rangka" class="fs-3"></code>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Nomor Mesin</small>
                        <code id="detail_no_mesin" class="fs-3"></code>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">Petugas Input</small>
                        <span id="detail_petugas" class="fw-semibold"></span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Tanggal Input</small>
                        <span id="detail_tgl_input" class="fw-semibold"></span>
                    </div>

                    <div class="col-12" id="detail_file_wrapper" style="display:none;">
                        <small class="text-muted d-block mb-1">Berkas Dokumen</small>
                        <a id="detail_file_link" href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-download me-1"></i> Buka / Unduh Berkas
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW DOKUMEN --}}
<div class="modal fade" id="modalPreviewDoc" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-2">
                <h6 class="modal-title fw-bold text-dark" id="previewDocTitle">Preview Dokumen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-dark" style="min-height: 500px;">
                <iframe id="previewDocFrame" src="" style="width: 100%; height: 600px; border: none;" class="d-none"></iframe>
                <img id="previewDocImg" src="" style="max-width: 100%; max-height: 600px; object-fit: contain;" class="d-none p-2">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Autofill Nama Kendaraan when selected from Data Aset dropdown
    const selectDataAset = document.getElementById("select_data_aset_id");
    const inputNama = document.getElementById("input_nama_kendaraan");
    const inputRangka = document.getElementById("input_no_rangka");

    if (selectDataAset) {
        selectDataAset.addEventListener("change", function() {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                const nama = opt.getAttribute("data-nama") || "";
                const merek = opt.getAttribute("data-merek") || "";
                const sn = opt.getAttribute("data-sn") || "";

                if (nama) {
                    inputNama.value = nama + (merek ? " (" + merek + ")" : "");
                }
                if (sn && inputRangka) {
                    inputRangka.value = sn;
                }
            }
        });
    }

    // Toggle custom keterangan
    const selectKet = document.getElementById("select_keterangan_tambah");
    const wrapCustom = document.getElementById("wrapper_keterangan_custom_tambah");
    if (selectKet && wrapCustom) {
        selectKet.addEventListener("change", function() {
            wrapCustom.style.display = this.value === "Isi Sendiri" ? "block" : "none";
        });
    }

    // Preview File Modal
    document.querySelectorAll(".btn-preview").forEach(btn => {
        btn.addEventListener("click", function() {
            const url = this.getAttribute("data-url");
            const title = this.getAttribute("data-title");
            document.getElementById("previewDocTitle").innerText = title;

            const ext = url.split(".").pop().toLowerCase();
            const isImg = ["jpg", "jpeg", "png", "webp", "gif"].includes(ext);

            const frame = document.getElementById("previewDocFrame");
            const img = document.getElementById("previewDocImg");

            if (isImg) {
                img.src = url;
                img.classList.remove("d-none");
                frame.classList.add("d-none");
                frame.src = "";
            } else {
                frame.src = url;
                frame.classList.remove("d-none");
                img.classList.add("d-none");
                img.src = "";
            }

            const modal = new bootstrap.Modal(document.getElementById("modalPreviewDoc"));
            modal.show();
        });
    });

    // Detail Modal
    document.querySelectorAll(".btn-detail").forEach(btn => {
        btn.addEventListener("click", function() {
            const item = JSON.parse(this.getAttribute("data-item"));
            const fileUrl = this.getAttribute("data-url");

            document.getElementById("detail_jenis_surat").innerText = item.jenis_surat || "-";
            document.getElementById("detail_nama_kendaraan").innerText = item.nama_kendaraan || "-";
            document.getElementById("detail_nama_pemilik").innerText = item.nama_pemilik || "-";
            document.getElementById("detail_no_plat").innerText = item.no_plat || "-";
            document.getElementById("detail_no_rangka").innerText = item.no_rangka || "-";
            document.getElementById("detail_no_mesin").innerText = item.no_mesin || "-";
            document.getElementById("detail_petugas").innerText = item.nama_petugas || (item.user ? item.user.name : "-");
            document.getElementById("detail_tgl_input").innerText = item.tgl_input ? new Date(item.tgl_input).toLocaleDateString("id-ID") : "-";

            const statusEl = document.getElementById("detail_status");
            if (item.warna_merah) {
                statusEl.className = "badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 fs-3";
                statusEl.innerText = item.keterangan || "Sedang Dipinjam / Diagunkan";
            } else {
                statusEl.className = "badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 fs-3";
                statusEl.innerText = item.keterangan || "Dokumen Asli Ada";
            }

            const fileWrapper = document.getElementById("detail_file_wrapper");
            const fileLink = document.getElementById("detail_file_link");
            if (fileUrl) {
                fileLink.href = fileUrl;
                fileWrapper.style.display = "block";
            } else {
                fileWrapper.style.display = "none";
            }

            const modal = new bootstrap.Modal(document.getElementById("modalDetailSuratKendaraan"));
            modal.show();
        });
    });

    // File size validation (20 MB) & Camera Capture Sync
    const mainFileInput = document.getElementById("input_file_dokumen");
    const cameraFileInput = document.getElementById("input_camera_dokumen");

    function checkFileSize(input) {
        if (input.files && input.files[0]) {
            const sizeMB = input.files[0].size / (1024 * 1024);
            if (sizeMB > 20) {
                alert("Ukuran berkas (" + sizeMB.toFixed(1) + " MB) melebihi batas maksimal 20 MB. Harap pilih berkas yang lebih kecil.");
                input.value = "";
                return false;
            }
        }
        return true;
    }

    if (mainFileInput) {
        mainFileInput.addEventListener("change", function() {
            checkFileSize(this);
        });
    }

    if (cameraFileInput && mainFileInput) {
        cameraFileInput.addEventListener("change", function() {
            if (checkFileSize(this) && this.files && this.files[0]) {
                const dt = new DataTransfer();
                dt.items.add(this.files[0]);
                mainFileInput.files = dt.files;
            }
        });
    }
});
</script>
@endsection
