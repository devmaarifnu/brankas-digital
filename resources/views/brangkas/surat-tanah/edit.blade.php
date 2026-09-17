@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="{{ route('brangkas.surat-tanah') }}">Arsip Surat Tanah</a></li>
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
                    <h4 class="mb-0 fw-bold text-dark">Edit Arsip Surat Tanah</h4>
                    <small class="text-muted">Perbarui formulir kuesioner sertifikat tanah di brankas</small>
                </div>
            </div>
            <div>
                <a href="{{ route('brangkas.surat-tanah') }}" class="btn btn-outline-secondary">
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
                        <i class="ti ti-pencil" style="color: #5D87FF;"></i>
                        <span>Formulir Perubahan Data Sertifikat</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('brangkas.surat-tanah.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- 1. Jenis Sertifikat --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">1. Jenis Sertifikat <span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_sertifikat" required>
                                @foreach(['SHM', 'Wakaf', 'Hibah', 'SHGB', 'SHGU', 'Hak Guna Pakai'] as $jns)
                                    <option value="{{ $jns }}" {{ old('jenis_sertifikat', $item->jenis_sertifikat) == $jns ? 'selected' : '' }}>{{ $jns }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Nomor Sertifikat & 3. Luas --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">2. Nomor Sertifikat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nomor_sertifikat" value="{{ old('nomor_sertifikat', $item->nomor_sertifikat) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">3. Luas Sertifikat (M2) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="luas" value="{{ old('luas', $item->luas) }}" required>
                            </div>
                        </div>

                        {{-- 4. Nama Sertifikat --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">4. Nama Sertifikat (Atas Nama) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_sertifikat" value="{{ old('nama_sertifikat', $item->nama_sertifikat ?? $item->nama_dokumen) }}" required>
                        </div>

                        {{-- 5. Desa & 6. Kecamatan --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">5. Desa/Kelurahan</label>
                                <input type="text" class="form-control" name="desa_kelurahan" value="{{ old('desa_kelurahan', $item->desa_kelurahan) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">6. Kecamatan</label>
                                <input type="text" class="form-control" name="kecamatan" value="{{ old('kecamatan', $item->kecamatan) }}">
                            </div>
                        </div>

                        {{-- 7. Kab/Kota & 8. Provinsi --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">7. Kabupaten/Kota</label>
                                <input type="text" class="form-control" name="kabupaten_kota" value="{{ old('kabupaten_kota', $item->kabupaten_kota) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">8. Provinsi</label>
                                <input type="text" class="form-control" name="provinsi" value="{{ old('provinsi', $item->provinsi) }}">
                            </div>
                        </div>

                        {{-- 9. Upload Dokumen --}}
                        {{-- 9. Upload Dokumen / Foto (Kamera, Galeri, PDF) --}}
                        <div class="mb-3 p-3 bg-light rounded border">
                            <label class="form-label fw-semibold text-primary d-flex align-items-center justify-content-between flex-wrap gap-1 mb-2">
                                <span><i class="ti ti-camera me-1"></i>9. Upload Dokumen / Foto Fisik</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fs-1">PDF / Foto / Kamera HP</span>
                            </label>
                            @if($item->file_dokumen)
                                @php
                                    $ext = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
                                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                @endphp
                                <div class="mb-2 p-2 bg-white rounded border d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="ti {{ $isImg ? 'ti-photo' : 'ti-file-check' }} me-1"></i>Berkas saat ini ada</span>
                                    <a href="{{ asset($item->file_dokumen) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">Lihat {{ $isImg ? 'Foto' : 'PDF' }} Saat Ini</a>
                                </div>
                            @endif

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
                                <i class="ti ti-info-circle me-1"></i>Biarkan kosong jika tidak mengganti file. Mendukung <strong>PDF, Foto Kamera, atau Galeri HP</strong> (Maksimal 20MB).
                            </div>
                            <div class="preview-selected-file mt-2" style="display: none;"></div>
                        </div>

                        {{-- 10. Nama Petugas & 11. Tanggal Input --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">10. Nama Petugas</label>
                                <input type="text" class="form-control bg-light" value="{{ $item->user->name ?? ($item->nama_petugas ?? auth()->user()->name) }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">11. Tanggal Input</label>
                                <input type="date" class="form-control" name="tgl_input" value="{{ old('tgl_input', $item->tgl_input ? $item->tgl_input->format('Y-m-d') : date('Y-m-d')) }}">
                            </div>
                        </div>

                        {{-- 12. Status --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">12. Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="keterangan" required>
                                @foreach(['Tersedia', 'Dipinjam', 'Diagunkan', 'Dihibahkan', 'Dikembalikan', 'Hanya Fotocopy'] as $ket)
                                    <option value="{{ $ket }}" {{ old('keterangan', $item->status_handover ?: $item->keterangan) == $ket ? 'selected' : '' }}>{{ $ket }}</option>
                                @endforeach
                                @if(!in_array(old('keterangan', $item->status_handover ?: $item->keterangan), ['Tersedia', 'Dipinjam', 'Diagunkan', 'Dihibahkan', 'Dikembalikan', 'Hanya Fotocopy']) && !empty($item->keterangan))
                                    <option value="{{ $item->keterangan }}" selected>{{ $item->keterangan }}</option>
                                @endif
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('brangkas.surat-tanah') }}" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-sm">
                                <i class="ti ti-device-floppy me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
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
});
</script>
@endsection