@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="{{ route('brangkas.surat-kendaraan') }}">Surat Kendaraan</a></li>
            <li><a href="javascript:void(0)">Edit Data</a></li>
        </ul>
    </nav>

    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-pencil fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Edit Surat Kendaraan</h4>
                    <small class="text-muted">Perbarui data Arsip Surat Kendaraan (BPKB/STNK) di brankas</small>
                </div>
            </div>
            <div>
                <a href="{{ route('brangkas.surat-kendaraan') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

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

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-car" style="color: #5D87FF;"></i>
                        <span>Formulir Perubahan Data Surat Kendaraan</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('brangkas.surat-kendaraan.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenis Surat <span class="text-danger">*</span></label>
                                <select name="jenis_surat" class="form-select" required>
                                    <option value="BPKB" {{ old('jenis_surat', $item->jenis_surat) === 'BPKB' ? 'selected' : '' }}>BPKB (Buku Pemilik Kendaraan Bermotor)</option>
                                    <option value="STNK" {{ old('jenis_surat', $item->jenis_surat) === 'STNK' ? 'selected' : '' }}>STNK (Surat Tanda Nomor Kendaraan)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Hubungan ke Data Aset Lembaga</label>
                                <select name="data_aset_id" id="select_data_aset_id" class="form-select">
                                    <option value="">-- Tidak Terhubung / Mandiri --</option>
                                    @foreach($kendaraanList as $k)
                                        <option value="{{ $k->id }}" {{ old('data_aset_id', $item->data_aset_id) == $k->id ? 'selected' : '' }} data-nama="{{ $k->nama_barang ?: $k->nama_aset }}" data-merek="{{ $k->merek }}" data-sn="{{ $k->nomor_seri_model }}">
                                            {{ $k->nama_barang ?: $k->nama_aset }} {{ $k->merek ? '('.$k->merek.')' : '' }} - [{{ $k->nomor_registrasi }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Kendaraan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_kendaraan" id="input_nama_kendaraan" class="form-control" value="{{ old('nama_kendaraan', $item->nama_kendaraan) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Pemilik (STNK/BPKB)</label>
                                <input type="text" name="nama_pemilik" class="form-control" value="{{ old('nama_pemilik', $item->nama_pemilik) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nomor Plat (No. Polisi)</label>
                                <input type="text" name="no_plat" class="form-control" value="{{ old('no_plat', $item->no_plat) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nomor Rangka (VIN)</label>
                                <input type="text" name="no_rangka" id="input_no_rangka" class="form-control" value="{{ old('no_rangka', $item->no_rangka) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nomor Mesin</label>
                                <input type="text" name="no_mesin" class="form-control" value="{{ old('no_mesin', $item->no_mesin) }}">
                            </div>

                            @php
                                $presetKet = ['Dokumen Asli Ada', 'Dipinjam', 'Diagunkan', 'Dihibahkan'];
                                $isCustomKet = !in_array($item->keterangan, $presetKet);
                            @endphp
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status / Keterangan</label>
                                <select name="keterangan" id="select_keterangan" class="form-select">
                                    <option value="Dokumen Asli Ada" {{ !$isCustomKet && $item->keterangan === 'Dokumen Asli Ada' ? 'selected' : '' }}>Tersedia (Dokumen Asli Ada)</option>
                                    <option value="Dipinjam" {{ !$isCustomKet && $item->keterangan === 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                    <option value="Diagunkan" {{ !$isCustomKet && $item->keterangan === 'Diagunkan' ? 'selected' : '' }}>Diagunkan</option>
                                    <option value="Dihibahkan" {{ !$isCustomKet && $item->keterangan === 'Dihibahkan' ? 'selected' : '' }}>Dihibahkan</option>
                                    <option value="Isi Sendiri" {{ $isCustomKet ? 'selected' : '' }}>-- Isi Sendiri --</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="wrapper_keterangan_custom" style="{{ $isCustomKet ? 'display:block;' : 'display:none;' }}">
                                <label class="form-label fw-semibold">Keterangan Custom</label>
                                <input type="text" name="keterangan_custom" class="form-control" value="{{ $isCustomKet ? $item->keterangan : '' }}" placeholder="Ketik keterangan khusus...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Input</label>
                                <input type="date" name="tgl_input" class="form-control" value="{{ old('tgl_input', $item->tgl_input ? $item->tgl_input->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Petugas Input</label>
                                <input type="text" name="nama_petugas" class="form-control bg-light text-dark fw-semibold" value="{{ old('nama_petugas', $item->nama_petugas ?? auth()->user()->name) }}" readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Upload Berkas / Foto Dokumen Baru</label>
                                @if($item->file_dokumen)
                                    @php
                                        $ext = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
                                        $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    @endphp
                                    <div class="mb-2">
                                        <a href="{{ asset($item->file_dokumen) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">
                                            <i class="ti ti-eye me-1"></i>Lihat {{ $isImg ? 'Foto' : 'PDF' }} Saat Ini
                                        </a>
                                    </div>
                                @endif
                                <div class="card border bg-light p-3 rounded-3 mb-1">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-7">
                                            <label class="form-label small fw-semibold text-dark mb-1"><i class="ti ti-file-upload me-1 text-primary"></i>Pilih File (Galeri Foto / PDF)</label>
                                            <input type="file" name="file_dokumen" id="input_file_dokumen_edit" class="form-control form-control-sm" accept="image/*,application/pdf,.pdf">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label small fw-semibold text-dark mb-1"><i class="ti ti-camera me-1 text-success"></i>Ambil Foto dari Kamera</label>
                                            <input type="file" id="input_camera_dokumen_edit" class="form-control form-control-sm" accept="image/*" capture="environment">
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti ti-info-circle me-1 text-info"></i>Pilihan file: <span class="fw-semibold text-dark">Kamera Langsung</span>, <span class="fw-semibold text-dark">Foto Galeri (JPG/PNG)</span>, atau <span class="fw-semibold text-dark">Dokumen PDF</span> — <strong>Maksimal 20 MB</strong>. Biarkan kosong jika tidak ingin mengganti file yang ada.
                                </small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('brangkas.surat-kendaraan') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary fw-semibold"><i class="ti ti-device-floppy me-1"></i>Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const selectKet = document.getElementById("select_keterangan");
    const wrapCustom = document.getElementById("wrapper_keterangan_custom");
    if (selectKet && wrapCustom) {
        selectKet.addEventListener("change", function() {
            wrapCustom.style.display = this.value === "Isi Sendiri" ? "block" : "none";
        });
    }

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
                if (sn && inputRangka && !inputRangka.value) {
                    inputRangka.value = sn;
                }
            }
        });
    }

    // File size validation (20 MB) & Camera Capture Sync
    const mainFileEdit = document.getElementById("input_file_dokumen_edit");
    const cameraFileEdit = document.getElementById("input_camera_dokumen_edit");

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

    if (mainFileEdit) {
        mainFileEdit.addEventListener("change", function() {
            checkFileSize(this);
        });
    }

    if (cameraFileEdit && mainFileEdit) {
        cameraFileEdit.addEventListener("change", function() {
            if (checkFileSize(this) && this.files && this.files[0]) {
                const dt = new DataTransfer();
                dt.items.add(this.files[0]);
                mainFileEdit.files = dt.files;
            }
        });
    }
});
</script>
@endsection
