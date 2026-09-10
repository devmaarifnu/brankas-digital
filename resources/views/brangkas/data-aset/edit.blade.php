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

                        {{-- 12. Upload Dokumen PDF --}}
                        <div class="mb-3 p-3 bg-light rounded border">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-file-upload me-1"></i>12. Upload Foto / Dokumen Aset (Wajib PDF)
                            </label>
                            @if($item->file_dokumen)
                                <div class="mb-2">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 me-2"><i class="ti ti-file-check me-1"></i>Dokumen PDF sudah ada</span>
                                    <a href="{{ asset($item->file_dokumen) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">Lihat File PDF Saat Ini</a>
                                </div>
                            @endif
                            <input type="file" class="form-control" name="file_dokumen" accept=".pdf,application/pdf">
                            <div class="form-text text-danger"><i class="ti ti-info-circle me-1"></i>Biarkan kosong jika tidak mengganti file. Format wajib PDF.</div>
                        </div>

                        {{-- 13. Petugas & 14. Tgl Input --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">13. Nama Petugas</label>
                                <input type="text" class="form-control" name="nama_petugas" value="{{ old('nama_petugas', $item->nama_petugas) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">14. Tanggal Input</label>
                                <input type="date" class="form-control" name="tgl_input" value="{{ old('tgl_input', $item->tgl_input ? $item->tgl_input->format('Y-m-d') : date('Y-m-d')) }}">
                            </div>
                        </div>

                        {{-- 15. Keterangan --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">15. Keterangan</label>
                            <input type="text" class="form-control" name="keterangan" value="{{ old('keterangan', $item->keterangan) }}">
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
});
</script>
@endsection