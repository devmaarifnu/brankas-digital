@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="{{ route('brangkas.data-aset') }}">Data Aset</a></li>
            <li><a href="javascript:void(0)">Edit Aset</a></li>
        </ul>
    </nav>

    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-pencil fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Edit Data Aset Lembaga</h4>
                    <small class="text-muted">Perbarui data kuesioner inventaris aset milik lembaga</small>
                </div>
            </div>
            <div>
                <a href="{{ route('brangkas.data-aset') }}" class="btn btn-outline-secondary">
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
                        <span>Formulir Perubahan Data Aset: {{ $item->nama_barang ?? $item->nama_aset }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('brangkas.data-aset.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- 1. Jenis Barang & 2. Nama Barang --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">1. Jenis Barang <span class="text-danger">*</span></label>
                                <select class="form-select" name="jenis_barang" required>
                                    @foreach(['Mobil', 'Sepeda Motor', 'Laptop', 'PC', 'Printer', 'TV', 'Lainnya'] as $jns)
                                        <option value="{{ $jns }}" {{ old('jenis_barang', $item->jenis_barang ?? $item->jenis_aset) == $jns ? 'selected' : '' }}>{{ $jns }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">2. Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_barang" value="{{ old('nama_barang', $item->nama_barang ?? $item->nama_aset) }}" required>
                            </div>
                        </div>

                        {{-- 3. Merek & 4. Seri/Model --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">3. Merek</label>
                                <input type="text" class="form-control" name="merek" value="{{ old('merek', $item->merek) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">4. Nomor Seri / Model</label>
                                <input type="text" class="form-control" name="nomor_seri_model" value="{{ old('nomor_seri_model', $item->nomor_seri_model) }}">
                            </div>
                        </div>

                        {{-- 5. Nomor Registrasi --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">5. Nomor Registrasi Aset Ma'arif</label>
                            <input type="text" class="form-control bg-light fw-bold text-primary" name="nomor_registrasi" value="{{ old('nomor_registrasi', $item->nomor_registrasi) }}" readonly>
                        </div>

                        {{-- 6. Sumber & 7. Tanggal Perolehan --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">6. Sumber Perolehan <span class="text-danger">*</span></label>
                                <select class="form-select" name="sumber_perolehan" required>
                                    @foreach(['Beli', 'Hibah', 'Wakaf', 'Bantuan Pemerintah', 'Lainnya'] as $sbr)
                                        <option value="{{ $sbr }}" {{ old('sumber_perolehan', $item->sumber_perolehan) == $sbr ? 'selected' : '' }}>{{ $sbr }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">7. Tanggal Perolehan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tgl_perolehan" value="{{ old('tgl_perolehan', $item->tgl_perolehan ? $item->tgl_perolehan->format('Y-m-d') : date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        {{-- 9. Kondisi & 10. Posisi Aset --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">9. Kondisi Aset <span class="text-danger">*</span></label>
                                <select class="form-select" name="kondisi_aset" required>
                                    @foreach(['Sangat Baik', 'Rusak Ringan', 'Rusak Berat'] as $knd)
                                        <option value="{{ $knd }}" {{ old('kondisi_aset', $item->kondisi_aset) == $knd ? 'selected' : '' }}>{{ $knd }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">10. Posisi Aset <span class="text-danger">*</span></label>
                                <select class="form-select" name="posisi_aset" id="edit_posisi_aset" required>
                                    @foreach(['Kantor', 'Dipinjam', 'Dihibahkan'] as $pos)
                                        <option value="{{ $pos }}" {{ old('posisi_aset', $item->posisi_aset) == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 11. Ruangan / Penerima --}}
                        <div id="edit_section_kantor" class="mb-3 p-3 bg-light rounded border">
                            <label class="form-label fw-semibold text-primary"><i class="ti ti-map-pin me-1"></i>Nama Ruangan Kantor</label>
                            <input type="text" class="form-control" name="nama_ruangan" value="{{ old('nama_ruangan', $item->nama_ruangan ?? $item->lokasi) }}">
                        </div>

                        <div id="edit_section_penerima" class="mb-3 p-3 bg-light rounded border" style="display:none;">
                            <h6 class="fw-bold text-warning mb-2"><i class="ti ti-user me-1"></i>Data Penerima / Pengguna</h6>
                            <div class="mb-2">
                                <label class="form-label">Nama Penerima / Pengguna</label>
                                <input type="text" class="form-control" name="nama_penerima" value="{{ old('nama_penerima', $item->nama_penerima ?? $item->lokasi) }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">No. Telepon Penerima</label>
                                <input type="text" class="form-control" name="no_telp_penerima" value="{{ old('no_telp_penerima', $item->no_telp_penerima) }}">
                            </div>
                        </div>

                        {{-- 12. Upload Dokumen / Foto (Kamera, Galeri, PDF) --}}
                        <div class="mb-3 p-3 bg-light rounded border">
                            <label class="form-label fw-semibold text-primary d-flex align-items-center justify-content-between flex-wrap gap-1 mb-2">
                                <span><i class="ti ti-camera me-1"></i>12. Upload Foto / Dokumen Aset Fisik</span>
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

                        {{-- 13. Petugas & 14. Tgl Input --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">13. Nama Petugas</label>
                                <input type="text" class="form-control bg-light" value="{{ $item->user->name ?? ($item->nama_petugas ?? auth()->user()->name) }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">14. Tanggal Input</label>
                                <input type="date" class="form-control" name="tgl_input" value="{{ old('tgl_input', $item->tgl_input ? $item->tgl_input->format('Y-m-d') : date('Y-m-d')) }}">
                            </div>
                        </div>

                        {{-- 15. Status --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">15. Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="keterangan" id="ast_keterangan_edit" required>
                                @foreach(['Tersedia', 'Dipinjam', 'Diagunkan', 'Dihibahkan', 'Dikembalikan', 'Rusak / Perbaikan'] as $st)
                                    <option value="{{ $st }}" {{ old('keterangan', $item->status_handover ?: $item->keterangan) == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                                @if(!in_array(old('keterangan', $item->status_handover ?: $item->keterangan), ['Tersedia', 'Dipinjam', 'Diagunkan', 'Dihibahkan', 'Dikembalikan', 'Rusak / Perbaikan']) && !empty($item->keterangan))
                                    <option value="{{ $item->keterangan }}" selected>{{ $item->keterangan }}</option>
                                @endif
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('brangkas.data-aset') }}" class="btn btn-light px-4">Batal</a>
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

@section("scripts")
<script>
$(document).ready(function() {
    function togglePosisi(pos) {
        if (pos === 'Kantor') {
            $('#edit_section_kantor').show();
            $('#edit_section_penerima').hide();
        } else {
            $('#edit_section_kantor').hide();
            $('#edit_section_penerima').show();
        }
    }
    $('#edit_posisi_aset').on('change', function() {
        togglePosisi($(this).val());
    });
    togglePosisi($('#edit_posisi_aset').val());

    // Upload Trigger Handlers
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