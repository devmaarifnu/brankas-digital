@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="{{ route('brangkas.akta-notaris') }}">Akta Notaris</a></li>
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
                    <h4 class="mb-0 fw-bold text-dark">Edit Akta Notaris</h4>
                    <small class="text-muted">Perbarui formulir kuesioner akta notaris / SK di brankas</small>
                </div>
            </div>
            <div>
                <a href="{{ route('brangkas.akta-notaris') }}" class="btn btn-outline-secondary">
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
                        <span>Formulir Perubahan Data Akta Notaris</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('brangkas.akta-notaris.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- 1. Jenis Dokumen --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">1. Jenis Dokumen <span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_dokumen" required>
                                @foreach(['Akta Notaris', 'SK Menkumham', 'Akta Perjanjian', 'Akta Hibah', 'Lainnya'] as $jns)
                                    <option value="{{ $jns }}" {{ old('jenis_dokumen', $item->jenis_dokumen ?? $item->jenis_sertifikat) == $jns ? 'selected' : '' }}>{{ $jns }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Nomor Dokumen & 3. Nama Dokumen --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">2. Nomor Dokumen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nomor_dokumen" value="{{ old('nomor_dokumen', $item->nomor_dokumen ?? $item->nomor_akta) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">3. Nama Dokumen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_dokumen" value="{{ old('nama_dokumen', $item->nama_dokumen ?? $item->nama_sertifikat) }}" required>
                            </div>
                        </div>

                        {{-- 4. Tanggal Dokumen & 5. Nama Notaris --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">4. Tanggal Dokumen</label>
                                <input type="date" class="form-control" name="tgl_dokumen" value="{{ old('tgl_dokumen', $item->tgl_dokumen ? $item->tgl_dokumen->format('Y-m-d') : ($item->tanggal_akta ? $item->tanggal_akta->format('Y-m-d') : '')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">5. Nama Notaris</label>
                                <input type="text" class="form-control" name="nama_notaris" value="{{ old('nama_notaris', $item->nama_notaris) }}">
                            </div>
                        </div>

                        {{-- 6. Alamat Notaris --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">6. Alamat Notaris</label>
                            <textarea class="form-control" name="alamat_notaris" rows="2">{{ old('alamat_notaris', $item->alamat_notaris ?? $item->alamat) }}</textarea>
                        </div>

                        {{-- 7. Telp. Notaris --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">7. Telp. Notaris</label>
                            <input type="text" class="form-control" name="telp_notaris" value="{{ old('telp_notaris', $item->telp_notaris) }}">
                        </div>

                        {{-- 8. Nama Petugas & 9. Tanggal Input --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">8. Nama Petugas</label>
                                <input type="text" class="form-control" name="nama_petugas" value="{{ old('nama_petugas', $item->nama_petugas) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">9. Tanggal Input</label>
                                <input type="date" class="form-control" name="tgl_input" value="{{ old('tgl_input', $item->tgl_input ? $item->tgl_input->format('Y-m-d') : date('Y-m-d')) }}">
                            </div>
                        </div>

                        {{-- 10. Upload Dokumen --}}
                        <div class="mb-3 p-3 bg-light rounded border">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-file-upload me-1"></i>10. Upload Dokumen (Wajib PDF)
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

                        {{-- 11. Keterangan --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">11. Keterangan <span class="text-danger">*</span></label>
                            <select class="form-select" name="keterangan" required>
                                @foreach(['Dokumen Asli Ada', 'Hanya Fotocopy', 'Diagunkan', 'Dipinjam'] as $ket)
                                    <option value="{{ $ket }}" {{ old('keterangan', $item->keterangan) == $ket ? 'selected' : '' }}>{{ $ket }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('brangkas.akta-notaris') }}" class="btn btn-light px-4">Batal</a>
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