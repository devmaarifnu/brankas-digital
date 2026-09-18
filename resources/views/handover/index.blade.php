@extends("template.layout")

@section("navbar")
    @include("template.nav")
@endsection

@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Utama</a></li>
            <li><a href="javascript:void(0)">Record of Transfer</a></li>
        </ul>
    </nav>

    {{-- Top Header Card --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                <i class="ti ti-arrows-left-right fs-5" style="color: #5D87FF;"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Record of Transfer</h4>
                <small class="text-muted">Input dan rekapitulasi data dokumen atau aset yang dipindahtangankan</small>
            </div>
        </div>
    </div>

    @if(session("success"))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="ti ti-check me-2 text-success fs-5"></i>{{ session("success") }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        {{-- Form Input (hidden for viewer / aproval) --}}
        @if(auth()->user()->canManageData())
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="card-title text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-pencil" style="color: #5D87FF;"></i>
                        <span>Form Serah Terima Dokumen</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route("handover.store") }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Nama Petugas (Readonly & Bound to Auth User) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Nama Petugas</label>
                            <input type="text" class="form-control bg-light text-dark fw-semibold" value="{{ auth()->user()->name ?: auth()->user()->username }}" readonly>
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="kategori" id="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Arsip Surat Tanah">Arsip Surat Tanah</option>
                                <option value="Akta Notaris">Akta Notaris</option>
                                <option value="Data Aset Lembaga">Data Aset Lembaga</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Status Serah Terima <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" id="status" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Dipinjam">Dipinjam (Peminjaman Dokumen)</option>
                                <option value="Diagunkan">Diagunkan (Agunan Bank)</option>
                                <option value="Dihibahkan">Dihibahkan (Pemberian Hibah)</option>
                                <option value="Dikembalikan">Dikembalikan (Pengembalian ke Brankas)</option>
                            </select>
                        </div>

                        {{-- Nama Dokumen - Auto dari AJAX --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Nama Barang / Dokumen <span class="text-danger">*</span></label>
                            <select class="form-select" name="ref_id" id="ref_id" required disabled>
                                <option value="">-- Pilih Kategori & Status terlebih dahulu --</option>
                            </select>
                            <input type="hidden" name="nama_dokumen" id="nama_dokumen_hidden">
                            <div class="form-text" id="doc_hint" style="display:none;"></div>
                        </div>

                        {{-- Bagian Dipinjam --}}
                        <div id="section-dipinjam" style="display:none;" class="p-3 bg-light rounded-3 mb-3 border">
                            <h6 class="text-primary fw-bold mb-2"><i class="ti ti-user me-1"></i>Data Peminjam</h6>
                            <div class="mb-2">
                                <label class="form-label">Nama Peminjam <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_peminjam" id="nama_peminjam" placeholder="Nama lengkap peminjam">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Nomor Telepon Peminjam <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="no_telp_peminjam" id="no_telp_peminjam" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        {{-- Bagian Diagunkan --}}
                        <div id="section-diagunkan" style="display:none;" class="p-3 bg-light rounded-3 mb-3 border">
                            <h6 class="text-warning fw-bold mb-2"><i class="ti ti-building-bank me-1"></i>Data Agunan Bank</h6>
                            <div class="mb-2">
                                <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_bank" id="nama_bank" placeholder="Contoh: Bank Mandiri">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Jangka Waktu Agunan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="jangka_agunan" id="jangka_agunan" placeholder="Contoh: 5 Tahun / s.d. 2029">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Penanggung Jawab Agunan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="penanggung_agunan" id="penanggung_agunan" placeholder="Nama lengkap penanggung jawab">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">No. Telepon Penanggung Jawab <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="no_telp_penanggung" id="no_telp_penanggung" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        {{-- Bagian Dihibahkan --}}
                        <div id="section-dihibahkan" style="display:none;" class="p-3 bg-light rounded-3 mb-3 border">
                            <h6 class="text-info fw-bold mb-2"><i class="ti ti-gift me-1"></i>Data Penerima Hibah</h6>
                            <div class="mb-2">
                                <label class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_penerima" id="nama_penerima" placeholder="Nama lengkap penerima hibah">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Nomor Telepon Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="no_telp_penerima" id="no_telp_penerima" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        {{-- Bagian Dikembalikan --}}
                        <div id="section-dikembalikan" style="display:none;" class="p-3 bg-light rounded-3 mb-3 border border-success border-opacity-25">
                            <h6 class="text-success fw-bold mb-2"><i class="ti ti-rotate-clockwise me-1"></i>Data Pengembalian Dokumen / Aset</h6>
                            <div class="mb-2">
                                <label class="form-label">Nama Yang Mengembalikan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_peminjam_kembali" id="nama_peminjam_kembali" placeholder="Nama lengkap pihak yang menyerahkan kembali">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Nomor Telepon Pengembali</label>
                                <input type="text" class="form-control" name="no_telp_peminjam_kembali" id="no_telp_peminjam_kembali" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        {{-- Tanggal Serah Terima --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Tanggal Serah Terima <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_serahterima" required value="{{ date('Y-m-d') }}">
                        </div>

                        {{-- Upload Bukti / Foto Serah Terima --}}
                        <div class="mb-3 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-semibold text-dark d-flex align-items-center justify-content-between flex-wrap gap-1 mb-2">
                                <span><i class="ti ti-camera me-1"></i>Upload Bukti / Foto Serah Terima</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fs-1">Kamera / Galeri / PDF</span>
                            </label>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 btn-trigger-camera">
                                    <i class="ti ti-camera fs-4"></i> Buka Kamera HP
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 btn-trigger-gallery">
                                    <i class="ti ti-photo fs-4"></i> Pilih Foto / Galeri
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 btn-trigger-pdf">
                                    <i class="ti ti-file-type-pdf fs-4"></i> Pilih PDF
                                </button>
                            </div>
                            <input type="file" class="d-none input-camera" accept="image/*" capture="environment">
                            <input type="file" class="d-none input-gallery" accept="image/*">
                            <input type="file" class="d-none input-pdf" accept=".pdf,application/pdf">
                            <input type="file" class="form-control main-upload-input" name="file_bukti" accept="image/*,application/pdf">
                            <div class="form-text text-muted small mt-1">JPG, PNG, atau PDF (Maksimal 20MB). Foto serah terima atau scan berita acara.</div>
                            <div class="preview-selected-file mt-2" style="display: none;"></div>
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Catatan Tambahan</label>
                            <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan kondisi dokumen, keterangan serah terima, dll..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary fw-semibold w-100 py-2 shadow-sm">
                            <i class="ti ti-device-floppy me-1"></i>Simpan Record Transfer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Tabel Rekap Handover --}}
        <div class="{{ auth()->user()->canManageData() ? 'col-lg-7' : 'col-12' }}">
            {{-- Filter & Search Card --}}
            <div class="card shadow-sm border-0 rounded-3 mb-3" style="border: 1px solid #ebf1f6;">
                <div class="card-body p-3">
                    <form action="{{ route('handover.index') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari nama, pihak terkait, catatan...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" name="kategori">
                                <option value="">-- Semua Kategori --</option>
                                <option value="Arsip Surat Tanah" {{ request('kategori') === 'Arsip Surat Tanah' ? 'selected' : '' }}>Arsip Surat Tanah</option>
                                <option value="Akta Notaris" {{ request('kategori') === 'Akta Notaris' ? 'selected' : '' }}>Akta Notaris</option>
                                <option value="Data Aset Lembaga" {{ request('kategori') === 'Data Aset Lembaga' ? 'selected' : '' }}>Data Aset Lembaga</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" name="status">
                                <option value="">-- Semua Status --</option>
                                <option value="Dipinjam" {{ request('status') === 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                <option value="Diagunkan" {{ request('status') === 'Diagunkan' ? 'selected' : '' }}>Diagunkan</option>
                                <option value="Dihibahkan" {{ request('status') === 'Dihibahkan' ? 'selected' : '' }}>Dihibahkan</option>
                                <option value="Dikembalikan" {{ request('status') === 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-primary flex-fill" title="Terapkan Filter">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('handover.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-list" style="color: #5D87FF;"></i>
                        <span>Log Record of Transfer</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('handover.export', request()->query()) }}" class="btn btn-sm btn-success fw-semibold shadow-sm d-flex align-items-center gap-1" title="Export Rekap ke Excel">
                            <i class="ti ti-file-spreadsheet fs-5"></i>
                            <span>Export Excel</span>
                        </a>
                        <span class="badge bg-light text-primary border">{{ $records->total() }} Data</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;">
                                <tr>
                                    <th class="ps-3" width="40">#</th>
                                    <th>Kategori</th>
                                    <th>Nama Dokumen / Aset</th>
                                    <th>Status</th>
                                    <th>Pihak Terkait</th>
                                    <th>Petugas</th>
                                    <th>Tgl Serah Terima</th>
                                    <th class="pe-3">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $i => $rec)
                                <tr>
                                    <td class="ps-3">{{ $records->firstItem() + $i }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $rec->kategori }}</span></td>
                                    <td class="fw-semibold text-dark">{{ $rec->nama_dokumen }}</td>
                                    <td>
                                        @if($rec->status === 'Dipinjam')
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"><i class="ti ti-hand-stop me-1"></i>Dipinjam</span>
                                        @elseif($rec->status === 'Diagunkan')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25"><i class="ti ti-building-bank me-1"></i>Diagunkan</span>
                                        @elseif($rec->status === 'Dihibahkan')
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"><i class="ti ti-gift me-1"></i>Dihibahkan</span>
                                        @elseif($rec->status === 'Dikembalikan')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="ti ti-check me-1"></i>Dikembalikan</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $rec->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rec->status === 'Dipinjam')
                                            <small class="d-block fw-semibold text-dark">{{ $rec->nama_peminjam }}</small>
                                            <small class="text-muted">{{ $rec->no_telp_peminjam }}</small>
                                        @elseif($rec->status === 'Diagunkan')
                                            <small class="d-block fw-semibold text-dark">{{ $rec->nama_bank }}</small>
                                            <small class="text-muted">PJ: {{ $rec->penanggung_agunan }} ({{ $rec->jangka_agunan }})</small>
                                        @elseif($rec->status === 'Dihibahkan')
                                            <small class="d-block fw-semibold text-dark">{{ $rec->nama_penerima }}</small>
                                            <small class="text-muted">{{ $rec->no_telp_penerima }}</small>
                                        @elseif($rec->status === 'Dikembalikan')
                                            <small class="d-block fw-semibold text-dark">Dari: {{ $rec->nama_peminjam ?: ($rec->nama_penerima ?: '-') }}</small>
                                            <small class="text-muted">{{ $rec->no_telp_peminjam ?: $rec->no_telp_penerima }}</small>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $rec->user->name ?? ($rec->nama_petugas ?? '-') }}</small></td>
                                    <td><small class="text-muted">{{ $rec->tgl_serahterima ? $rec->tgl_serahterima->format('d/m/Y') : '-' }}</small></td>
                                    <td class="pe-3">
                                        @if($rec->file_bukti)
                                            <a href="{{ asset($rec->file_bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat Bukti">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="ti ti-inbox fs-2 d-block mb-2 text-muted"></i>
                                        Belum ada data perpindahan dokumen / aset yang sesuai pencarian / filter.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi --}}
                    @if($records->hasPages())
                    <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $records->firstItem() ?? 0 }} - {{ $records->lastItem() ?? 0 }} dari total {{ $records->total() }} data
                        </small>
                        <div>
                            {{ $records->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section("scripts")
<script>
$(document).ready(function() {
    function loadDocuments() {
        var kategori = $('#kategori').val();
        var status = $('#status').val();
        var $refSelect = $('#ref_id');
        var $hint = $('#doc_hint');

        if (!kategori) {
            if ($refSelect.hasClass('select2-hidden-accessible')) {
                $refSelect.select2('destroy');
            }
            $refSelect.html('<option value="">-- Pilih Kategori terlebih dahulu --</option>').prop('disabled', true);
            $hint.hide();
            return;
        }

        if ($refSelect.hasClass('select2-hidden-accessible')) {
            $refSelect.select2('destroy');
        }
        $refSelect.html('<option value="">Memuat data dokumen...</option>').prop('disabled', true);

        $.ajax({
            url: "{{ route('handover.items') }}",
            type: "GET",
            data: { kategori: kategori, status: status },
            success: function(data) {
                $refSelect.empty();
                if (data.length === 0) {
                    if (status === 'Dikembalikan') {
                        $refSelect.append('<option value="">-- Tidak ada dokumen yang sedang dipinjam/diagunkan --</option>');
                    } else {
                        $refSelect.append('<option value="">-- Tidak ada dokumen yang tersedia --</option>');
                    }
                } else {
                    $refSelect.append('<option value="">-- Ketik / Cari Dokumen atau Aset (' + data.length + ' item) --</option>');
                    $.each(data, function(index, item) {
                        $refSelect.append('<option value="' + item.id + '" data-nama="' + item.nama_dokumen + '">' + item.nama_dokumen + '</option>');
                    });
                }
                $refSelect.prop('disabled', false);

                // Aktifkan Fitur Pencarian Select2
                $refSelect.select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Ketik / Cari Dokumen atau Aset --',
                    allowClear: true,
                    width: '100%'
                });

                if (status === 'Dikembalikan') {
                    $hint.html('<span class="text-success"><i class="ti ti-info-circle me-1"></i>Hanya menampilkan dokumen yang sedang dipinjam / diagunkan untuk proses pengembalian.</span>').show();
                } else if (status) {
                    $hint.html('<span class="text-muted"><i class="ti ti-info-circle me-1"></i>Hanya menampilkan dokumen yang tersedia di brankas.</span>').show();
                } else {
                    $hint.hide();
                }
            },
            error: function() {
                $refSelect.html('<option value="">Gagal memuat data</option>');
                $hint.hide();
            }
        });
    }

    $('#kategori, #status').on('change', function() {
        var status = $('#status').val();

        // Sembunyikan semua sub-form
        $('#section-dipinjam').hide().find('input').prop('required', false);
        $('#section-diagunkan').hide().find('input').prop('required', false);
        $('#section-dihibahkan').hide().find('input').prop('required', false);
        $('#section-dikembalikan').hide().find('input').prop('required', false);

        if (status === 'Dipinjam') {
            $('#section-dipinjam').slideDown().find('input').prop('required', true);
        } else if (status === 'Diagunkan') {
            $('#section-diagunkan').slideDown().find('input').prop('required', true);
        } else if (status === 'Dihibahkan') {
            $('#section-dihibahkan').slideDown().find('input').prop('required', true);
        } else if (status === 'Dikembalikan') {
            $('#section-dikembalikan').slideDown();
            $('#nama_peminjam_kembali').prop('required', true);
        }

        loadDocuments();
    });

    $(document).on('change select2:select', '#ref_id', function() {
        var selectedText = $(this).find('option:selected').data('nama');
        // Bersihkan tag status di nama dokumen sebelum simpan
        if (selectedText) {
            selectedText = selectedText.replace(/\s*\[Sedang.*?\]\s*$/, '');
        }
        $('#nama_dokumen_hidden').val(selectedText || '');
    });

    // Handle form submit untuk input nama pengembali jika status Dikembalikan
    $('form').on('submit', function() {
        var status = $('#status').val();
        if (status === 'Dikembalikan') {
            var namaKembali = $('#nama_peminjam_kembali').val();
            var telpKembali = $('#no_telp_peminjam_kembali').val();
            $('#nama_peminjam').val(namaKembali);
            $('#no_telp_peminjam').val(telpKembali);
        }
    });

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