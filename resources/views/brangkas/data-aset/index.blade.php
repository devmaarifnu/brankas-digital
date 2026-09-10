@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Aset Lembaga</a></li>
            <li><a href="javascript:void(0)">Data Aset</a></li>
        </ul>
    </nav>

    {{-- Top Header Card --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-building-bank fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Data Aset Lembaga</h4>
                    <small class="text-muted">Rekapitulasi inventaris & aset lembaga LP Ma'arif NU</small>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAset">
                    <i class="ti ti-plus me-1"></i>Tambah Data Aset
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
                <span>Daftar Aset Lembaga</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-primary border">{{ $data->count() }} Aset</span>
                @if($data->where("warna_merah",true)->count() > 0)
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                    <i class="ti ti-alert-triangle me-1"></i>{{ $data->where("warna_merah",true)->count() }} status khusus / dipindah tangan
                </span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tblDataAset">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;">
                        <tr>
                            <th class="ps-3" width="40">#</th>
                            <th>Jenis Barang</th>
                            <th>Nama Barang</th>
                            <th>Merek</th>
                            <th>No. Seri/Model</th>
                            <th>No. Registrasi Aset</th>
                            <th>Sumber Perolehan</th>
                            <th>Tgl Perolehan</th>
                            <th>Usia Barang</th>
                            <th>Kondisi</th>
                            <th>Posisi Aset</th>
                            <th>Lokasi / Penerima</th>
                            <th>Nama Petugas</th>
                            <th>Tgl Input</th>
                            <th>Foto/PDF</th>
                            <th>Keterangan</th>
                            <th class="text-center pe-3" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $item)
                        <tr class="{{ $item->warna_merah ? 'table-danger' : '' }}">
                            <td class="ps-3">{{ $i+1 }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->jenis_barang ?? ($item->jenis_aset ?? '-') }}</span></td>
                            <td class="fw-semibold text-dark">{{ $item->nama_barang ?? $item->nama_aset }}</td>
                            <td>{{ $item->merek ?? '-' }}</td>
                            <td><code>{{ $item->nomor_seri_model ?? '-' }}</code></td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $item->nomor_registrasi ?? '-' }}</span></td>
                            <td><small>{{ $item->sumber_perolehan ?? '-' }}</small></td>
                            <td><small>{{ $item->tgl_perolehan ? $item->tgl_perolehan->format('d/m/Y') : '-' }}</small></td>
                            <td><small class="fw-semibold text-dark">{{ $item->usia_barang }}</small></td>
                            <td>
                                @if($item->kondisi_aset === 'Sangat Baik')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Sangat Baik</span>
                                @elseif($item->kondisi_aset === 'Rusak Ringan')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Rusak Ringan</span>
                                @elseif($item->kondisi_aset === 'Rusak Berat')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Rusak Berat</span>
                                @else
                                    <span class="badge bg-secondary">{{ $item->kondisi_aset ?? '-' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($item->posisi_aset === 'Kantor')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Kantor</span>
                                @elseif($item->posisi_aset === 'Dipinjam')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Dipinjam</span>
                                @elseif($item->posisi_aset === 'Dihibahkan')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Dihibahkan</span>
                                @else
                                    <span class="badge bg-secondary">{{ $item->posisi_aset ?? '-' }}</span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    @if($item->posisi_aset === 'Kantor')
                                        {{ $item->nama_ruangan ?? ($item->lokasi ?? 'Ruang Kantor') }}
                                     @else
                                        {{ $item->nama_penerima ?? ($item->lokasi ?? '-') }}
                                        @if($item->no_telp_penerima) <br><span class="text-muted">({{ $item->no_telp_penerima }})</span> @endif
                                    @endif
                                </small>
                            </td>
                            <td><small class="text-muted">{{ $item->nama_petugas ?? '-' }}</small></td>
                            <td><small class="text-muted">{{ $item->tgl_input ? $item->tgl_input->format('d/m/Y') : ($item->created_at ? $item->created_at->format('d/m/Y') : '-') }}</small></td>
                            <td class="text-center">
                                @if($item->file_dokumen)
                                    <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 shadow-sm btn-preview"
                                            data-title="{{ $item->nama_barang ?? $item->nama_aset }}"
                                            data-url="{{ asset($item->file_dokumen) }}"
                                            title="Lihat Foto/Dokumen PDF">
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
                                    <span class="badge bg-light text-dark border">{{ $item->keterangan ?? 'Operasional Kantor' }}</span>
                                @endif
                            </td>
                            <td class="text-center pe-3" style="width: 120px;">
                                <div class="d-flex flex-column gap-1 mx-auto" style="width: 85px;">
                                    {{-- Lihat Detail --}}
                                    <button type="button" class="btn btn-sm btn-outline-info w-100 py-1 px-2 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap shadow-sm btn-detail"
                                            data-item="{{ json_encode($item) }}"
                                            data-url="{{ $item->file_dokumen ? asset($item->file_dokumen) : '' }}"
                                            data-usia="{{ $item->usia_barang }}"
                                            title="Lihat Detail Lengkap">
                                        <i class="ti ti-eye"></i> Detail
                                    </button>

                                    {{-- Edit --}}
                                    <a href="{{ route('brangkas.data-aset.edit', $item->id) }}" class="btn btn-sm btn-outline-warning w-100 py-1 px-2 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap shadow-sm" title="Edit Aset">
                                        <i class="ti ti-pencil"></i> Edit
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('brangkas.data-aset.destroy', $item->id) }}" method="POST" class="w-100 m-0" onsubmit="return confirm('Yakin ingin menghapus aset ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-1 px-2 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap shadow-sm" title="Hapus Aset">
                                            <i class="ti ti-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="17" class="text-center text-muted py-5">
                                <i class="ti ti-inbox fs-2 d-block mb-2 text-muted"></i>
                                <span>Belum ada data aset lembaga.</span>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAset">
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
<div class="modal fade" id="modalTambahAset" tabindex="-1" aria-labelledby="modalTambahAsetLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3 shadow border-0">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalTambahAsetLabel">
                    <i class="ti ti-plus" style="color: #5D87FF;"></i>
                    <span>Input Data Aset Lembaga</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('brangkas.data-aset.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-primary bg-light-primary text-primary py-2 px-3 mb-3 d-flex align-items-center gap-2 border-0" style="font-size: 13px;">
                        <i class="ti ti-info-circle fs-5"></i>
                        <span>Isilah data inventaris aset lembaga secara lengkap.</span>
                    </div>

                    {{-- 1. Jenis Barang & 2. Nama Barang --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">1. Jenis Barang <span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_barang" required>
                                <option value="" selected disabled>-- Pilih Jenis Barang --</option>
                                <option value="Mobil">Mobil</option>
                                <option value="Sepeda Motor">Sepeda Motor</option>
                                <option value="Laptop">Laptop</option>
                                <option value="PC">PC / Komputer</option>
                                <option value="Printer">Printer / Scanner</option>
                                <option value="TV">TV / Proyektor</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">2. Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_barang" required placeholder="Contoh: Laptop Thinkpad T14 / Innova Reborn">
                        </div>
                    </div>

                    {{-- 3. Merek & 4. Nomor Seri/Model --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">3. Merek</label>
                            <input type="text" class="form-control" name="merek" placeholder="Contoh: Lenovo / Toyota / Epson">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">4. Nomor Seri / Model</label>
                            <input type="text" class="form-control" name="nomor_seri_model" placeholder="Contoh: SN: L3B0992 / Nopol: B 1926 LP">
                        </div>
                    </div>

                    {{-- 5. Nomor Registrasi Aset Ma'arif (Otomatis) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">5. Nomor Registrasi Aset Ma'arif (Otomatis)</label>
                        <input type="text" class="form-control bg-light fw-bold text-primary" name="nomor_registrasi" value="{{ $nextRegNo }}" readonly>
                        <div class="form-text">Dihasilkan otomatis oleh sistem.</div>
                    </div>

                    {{-- 6. Sumber Perolehan & 7. Tanggal Perolehan --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">6. Sumber Perolehan <span class="text-danger">*</span></label>
                            <select class="form-select" name="sumber_perolehan" required>
                                <option value="Beli" selected>Beli</option>
                                <option value="Hibah">Hibah</option>
                                <option value="Wakaf">Wakaf</option>
                                <option value="Bantuan Pemerintah">Bantuan Pemerintah</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">7. Tanggal Perolehan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_perolehan" id="ast_tgl_perolehan" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    {{-- 8. Usia Barang (Otomatis) & 9. Kondisi Aset --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">8. Usia Barang (Otomatis)</label>
                            <input type="text" class="form-control bg-light fw-semibold" id="ast_usia_barang" value="0 Bulan" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">9. Kondisi Aset <span class="text-danger">*</span></label>
                            <select class="form-select" name="kondisi_aset" required>
                                <option value="Sangat Baik" selected>Sangat Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    {{-- 10. Posisi Aset --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">10. Posisi Aset <span class="text-danger">*</span></label>
                        <select class="form-select" name="posisi_aset" id="ast_posisi_aset" required>
                            <option value="Kantor" selected>Kantor</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Dihibahkan">Dihibahkan</option>
                        </select>
                    </div>

                    {{-- 11. Kondisional Posisi Aset --}}
                    <div id="ast_section_kantor" class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-semibold text-primary"><i class="ti ti-map-pin me-1"></i>Posisi / Nama Ruangan Kantor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_ruangan" id="ast_nama_ruangan" placeholder="Contoh: Ruang Sekretariat / Ruang Rapat Lt. 2" value="Ruang Sekretariat">
                    </div>

                    <div id="ast_section_penerima" class="mb-3 p-3 bg-light rounded border" style="display:none;">
                        <h6 class="fw-bold text-warning mb-2"><i class="ti ti-user me-1"></i>Data Penerima / Pengguna Aset</h6>
                        <div class="mb-2">
                            <label class="form-label">Nama Penerima / Pengguna <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_penerima" id="ast_nama_penerima" placeholder="Nama lengkap penerima">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">No. Telepon Penerima</label>
                            <input type="text" class="form-control" name="no_telp_penerima" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    {{-- 12. Upload Foto/Dokumen Aset (Kunci PDF) --}}
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-semibold text-primary">
                            <i class="ti ti-file-upload me-1"></i>12. Upload Foto / Dokumen Aset (Wajib PDF)
                        </label>
                        <input type="file" class="form-control" name="file_dokumen" accept=".pdf,application/pdf">
                        <div class="form-text text-danger"><i class="ti ti-info-circle me-1"></i>Hanya format file <strong>PDF</strong> yang diizinkan. Maksimal 15MB.</div>
                    </div>

                    {{-- 13. Nama Petugas & 14. Tanggal Input --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">13. Nama Petugas</label>
                            <input type="text" class="form-control" name="nama_petugas" value="{{ auth()->user()->name ?: auth()->user()->username }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">14. Tanggal di Input</label>
                            <input type="date" class="form-control" name="tgl_input" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    {{-- 15. Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">15. Keterangan</label>
                        <select class="form-select" name="keterangan" id="ast_keterangan">
                            <option value="Dipakai kebutuhan operasional kantor" selected>Dipakai kebutuhan operasional kantor</option>
                            <option value="Dipinjam untuk kegiatan operasional">Dipinjam untuk kegiatan operasional</option>
                            <option value="Cadangan inventaris kantor">Cadangan inventaris kantor</option>
                            <option value="Isi Sendiri">Isi Sendiri...</option>
                        </select>
                        <input type="text" class="form-control mt-2" name="keterangan_custom" id="ast_keterangan_custom" placeholder="Tuliskan keterangan..." style="display:none;">
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3 bg-white">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-sm">
                        <i class="ti ti-device-floppy me-1"></i>Simpan Data Aset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL LENGKAP POPUP --}}
<div class="modal fade" id="modalDetailAset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3 shadow border-0">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-building-bank" style="color: #5D87FF;"></i>
                    <span>Detail Data Aset Lembaga</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr><th width="35%" class="bg-light">Nomor Registrasi</th><td id="dt_ast_reg" class="fw-bold text-primary">-</td></tr>
                        <tr><th class="bg-light">Jenis Barang</th><td id="dt_ast_jenis">-</td></tr>
                        <tr><th class="bg-light">Nama Barang</th><td id="dt_ast_nama" class="fw-bold">-</td></tr>
                        <tr><th class="bg-light">Merek & Seri/Model</th><td id="dt_ast_merek">-</td></tr>
                        <tr><th class="bg-light">Sumber Perolehan</th><td id="dt_ast_sumber">-</td></tr>
                        <tr><th class="bg-light">Tanggal Perolehan</th><td id="dt_ast_tglperolehan">-</td></tr>
                        <tr><th class="bg-light">Usia Barang</th><td id="dt_ast_usia" class="fw-bold">-</td></tr>
                        <tr><th class="bg-light">Kondisi Aset</th><td id="dt_ast_kondisi">-</td></tr>
                        <tr><th class="bg-light">Posisi Aset</th><td id="dt_ast_posisi">-</td></tr>
                        <tr><th class="bg-light">Ruangan / Penerima</th><td id="dt_ast_lokasi">-</td></tr>
                        <tr><th class="bg-light">Nama Petugas</th><td id="dt_ast_petugas">-</td></tr>
                        <tr><th class="bg-light">Tanggal Input</th><td id="dt_ast_tglinput">-</td></tr>
                        <tr><th class="bg-light">Keterangan</th><td id="dt_ast_keterangan">-</td></tr>
                        <tr><th class="bg-light">Dokumen/Foto Terlampir</th><td id="dt_ast_file">-</td></tr>
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
                    <span id="previewTitle">Pratinjau Foto/Dokumen Aset (PDF)</span>
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
    // Hitung usia barang otomatis
    function hitungUsia(tglString) {
        if (!tglString) return '0 Bulan';
        var tgl = new Date(tglString);
        var now = new Date();
        var years = now.getFullYear() - tgl.getFullYear();
        var months = now.getMonth() - tgl.getMonth();
        if (months < 0) {
            years--;
            months += 12;
        }
        var res = [];
        if (years > 0) res.push(years + ' Tahun');
        if (months > 0) res.push(months + ' Bulan');
        return res.length > 0 ? res.join(' ') : 'Kurang dari 1 Bulan';
    }

    $('#ast_tgl_perolehan').on('change', function() {
        $('#ast_usia_barang').val(hitungUsia($(this).val()));
    });
    $('#ast_usia_barang').val(hitungUsia($('#ast_tgl_perolehan').val()));

    // Kondisional posisi aset
    $('#ast_posisi_aset').on('change', function() {
        var pos = $(this).val();
        if (pos === 'Kantor') {
            $('#ast_section_kantor').slideDown();
            $('#ast_nama_ruangan').prop('required', true);
            $('#ast_section_penerima').slideUp();
            $('#ast_nama_penerima').prop('required', false);
        } else {
            $('#ast_section_kantor').slideUp();
            $('#ast_nama_ruangan').prop('required', false);
            $('#ast_section_penerima').slideDown();
            $('#ast_nama_penerima').prop('required', true);
        }
    });

    $('#ast_keterangan').on('change', function() {
        if ($(this).val() === 'Isi Sendiri') {
            $('#ast_keterangan_custom').slideDown().prop('required', true);
        } else {
            $('#ast_keterangan_custom').slideUp().prop('required', false);
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
        var usia = $(this).data('usia');

        $('#dt_ast_reg').text(item.nomor_registrasi || '-');
        $('#dt_ast_jenis').text(item.jenis_barang || item.jenis_aset || '-');
        $('#dt_ast_nama').text(item.nama_barang || item.nama_aset || '-');
        $('#dt_ast_merek').text((item.merek || '-') + ' / ' + (item.nomor_seri_model || '-'));
        $('#dt_ast_sumber').text(item.sumber_perolehan || '-');
        $('#dt_ast_tglperolehan').text(item.tgl_perolehan || '-');
        $('#dt_ast_usia').text(usia || '-');
        $('#dt_ast_kondisi').text(item.kondisi_aset || '-');
        $('#dt_ast_posisi').text(item.posisi_aset || '-');
        $('#dt_ast_lokasi').text(item.posisi_aset === 'Kantor' ? (item.nama_ruangan || item.lokasi || 'Kantor') : (item.nama_penerima || item.lokasi || '-'));
        $('#dt_ast_petugas').text(item.nama_petugas || '-');
        $('#dt_ast_tglinput').text(item.tgl_input || '-');
        $('#dt_ast_keterangan').html(item.warna_merah ? '<span class="badge bg-danger">' + (item.keterangan || 'Status Khusus') + '</span>' : '<span class="badge bg-success">' + (item.keterangan || 'Operasional Kantor') + '</span>');
        
        if (fileUrl) {
            $('#dt_ast_file').html('<a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-download me-1"></i>Unduh PDF</a>');
        } else {
            $('#dt_ast_file').text('Tidak ada dokumen PDF');
        }

        new bootstrap.Modal(document.getElementById('modalDetailAset')).show();
    });
});
</script>
@endsection