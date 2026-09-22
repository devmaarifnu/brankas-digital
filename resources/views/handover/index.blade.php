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

    {{-- Top Header Card (Sama Persis Menu Lain) --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-arrows-left-right fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Record of Transfer</h4>
                    <small class="text-muted">Input dan rekapitulasi data dokumen atau aset yang dipindahtangankan</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('handover.export', request()->query()) }}" class="btn btn-success fw-semibold shadow-sm d-flex align-items-center gap-1" title="Export Rekap ke Excel">
                    <i class="ti ti-file-spreadsheet fs-5"></i>
                    <span>Export Excel</span>
                </a>
                @if(auth()->user()->canManageData())
                <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahHandover">
                    <i class="ti ti-plus me-1"></i>Tambah Record of Transfer
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
    <div class="card shadow-sm border-0 rounded-3 mb-3" style="border: 1px solid #ebf1f6;">
        <div class="card-body p-3">
            <form action="{{ route('handover.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari nama dokumen, peminjam, penerima, catatan...">
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

    {{-- Full Width Table Card --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-3" width="50">No</th>
                            <th>Kategori</th>
                            <th>Nama Dokumen / Aset</th>
                            <th>Status</th>
                            <th>Pihak Terkait</th>
                            <th>Petugas Input</th>
                            <th>Tgl Serah Terima</th>
                            <th>Bukti File</th>
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
                                    <a href="{{ asset($rec->file_bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat Bukti Dokumen">
                                        <i class="ti ti-eye me-1"></i>Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="ti ti-inbox fs-2 d-block mb-2 text-muted"></i>
                                Belum ada data perpindahan dokumen / aset yang sesuai pencarian.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($records->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $records->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL TAMBAH RECORD OF TRANSFER --}}
@if(auth()->user()->canManageData())
<div class="modal fade" id="modalTambahHandover" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-arrows-left-right text-primary fs-5"></i> Form Serah Terima Dokumen (Record of Transfer)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('handover.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Nama Petugas --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Nama Petugas Input</label>
                            <input type="text" class="form-control bg-light text-dark fw-semibold" value="{{ auth()->user()->name ?: auth()->user()->username }}" readonly>
                        </div>

                        {{-- Kategori --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="kategori" id="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Arsip Surat Tanah">Arsip Surat Tanah</option>
                                <option value="Akta Notaris">Akta Notaris</option>
                                <option value="Data Aset Lembaga">Data Aset Lembaga</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Nama Barang / Dokumen <span class="text-danger">*</span></label>
                            <select class="form-select" name="ref_id" id="ref_id" required disabled>
                                <option value="">-- Pilih Kategori & Status terlebih dahulu --</option>
                            </select>
                            <input type="hidden" name="nama_dokumen" id="nama_dokumen_hidden">
                            <div class="form-text mt-1" id="doc_hint" style="display:none;"></div>
                        </div>

                        {{-- Bagian Dipinjam --}}
                        <div id="section-dipinjam" style="display:none;" class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="text-primary fw-bold mb-3"><i class="ti ti-user me-1"></i>Data Peminjam</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nama Peminjam <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nama_peminjam" id="nama_peminjam" placeholder="Nama lengkap peminjam">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nomor Telepon Peminjam <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="no_telp_peminjam" id="no_telp_peminjam" placeholder="08xxxxxxxxxx">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bagian Diagunkan --}}
                        <div id="section-diagunkan" style="display:none;" class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="text-warning fw-bold mb-3"><i class="ti ti-building-bank me-1"></i>Data Agunan Bank</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nama Bank / Lembaga <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nama_bank" id="nama_bank" placeholder="Contoh: Bank Mandiri / BSI">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Jangka Waktu Agunan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="jangka_agunan" id="jangka_agunan" placeholder="Contoh: 1 Tahun / s.d. Dec 2026">
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label class="form-label fw-semibold">Penanggung Jawab <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="penanggung_agunan" id="penanggung_agunan" placeholder="Nama penanggung jawab">
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label class="form-label fw-semibold">No. Telp Penanggung Jawab</label>
                                        <input type="text" class="form-control" name="no_telp_penanggung" placeholder="08xxxxxxxxxx">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bagian Dihibahkan --}}
                        <div id="section-dihibahkan" style="display:none;" class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="text-info fw-bold mb-3"><i class="ti ti-gift me-1"></i>Data Penerima Hibah</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nama Penerima Hibah <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nama_penerima" id="nama_penerima" placeholder="Nama lembaga / perorangan penerima">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nomor Telepon Penerima</label>
                                        <input type="text" class="form-control" name="no_telp_penerima" placeholder="08xxxxxxxxxx">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bagian Dikembalikan --}}
                        <div id="section-dikembalikan" style="display:none;" class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="text-success fw-bold mb-3"><i class="ti ti-check me-1"></i>Data Pengembali Dokumen</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nama Pengembali <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nama_peminjam_kembali" id="nama_peminjam_kembali" placeholder="Nama yang mengembalikan ke brankas">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nomor Telepon Pengembali</label>
                                        <input type="text" class="form-control" name="no_telp_peminjam_kembali" id="no_telp_peminjam_kembali" placeholder="08xxxxxxxxxx">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tanggal Serah Terima --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Tanggal Serah Terima <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_serahterima" value="{{ date('Y-m-d') }}" required>
                        </div>

                        {{-- Upload Bukti File --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Bukti Berkas / Foto Serah Terima</label>
                            <input type="file" class="form-control" name="file_bukti" accept="image/*,application/pdf">
                        </div>

                        {{-- Catatan --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">Catatan Tambahan</label>
                            <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan opsional..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="ti ti-device-floppy me-1"></i>Simpan Record of Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section("scripts")
<script>
$(document).ready(function() {
    function loadDocuments() {
        var kategori = $('#kategori').val();
        var status = $('#status').val();
        var $refSelect = $('#ref_id');
        var $hint = $('#doc_hint');

        if (!kategori || !status) {
            $refSelect.html('<option value="">-- Pilih Kategori & Status terlebih dahulu --</option>').prop('disabled', true);
            $hint.hide();
            return;
        }

        $refSelect.html('<option value="">Sedang memuat data...</option>').prop('disabled', true);

        $.ajax({
            url: "{{ route('handover.get-items') }}",
            type: "GET",
            data: { kategori: kategori, status: status },
            success: function(res) {
                $refSelect.empty();
                if (res.length === 0) {
                    if (status === 'Dikembalikan') {
                        $refSelect.append('<option value="">-- Tidak ada dokumen/aset yang sedang dipinjam --</option>');
                    } else {
                        $refSelect.append('<option value="">-- Semua dokumen/aset dalam kategori ini sedang dipinjam/diagunkan --</option>');
                    }
                    $refSelect.prop('disabled', true);
                    $hint.removeClass().addClass('form-text text-danger').text('Tidak ada dokumen yang memenuhi kriteria status.').show();
                } else {
                    $refSelect.append('<option value="">-- Pilih Dokumen / Aset --</option>');
                    $.each(res, function(i, item) {
                        $refSelect.append('<option value="' + item.id + '" data-nama="' + item.nama_dokumen + '">' + item.nama_dokumen + '</option>');
                    });
                    $refSelect.prop('disabled', false);

                    if (status === 'Dikembalikan') {
                        $hint.removeClass().addClass('form-text text-success').text('Menampilkan dokumen/aset yang SAAT INI SEDANG DIPINJAM atau DIAGUNKAN.').show();
                    } else {
                        $hint.removeClass().addClass('form-text text-muted').text('Menampilkan dokumen/aset yang SAAT INI TERSEDIA di brankas.').show();
                    }
                }
            },
            error: function() {
                $refSelect.html('<option value="">Gagal memuat data</option>').prop('disabled', true);
            }
        });
    }

    $('#kategori, #status').on('change', function() {
        loadDocuments();
    });

    $('#ref_id').on('change', function() {
        var selectedName = $(this).find('option:selected').data('nama');
        $('#nama_dokumen_hidden').val(selectedName || '');
    });

    $('#status').on('change', function() {
        var st = $(this).val();
        $('#section-dipinjam, #section-diagunkan, #section-dihibahkan, #section-dikembalikan').hide();

        if (st === 'Dipinjam') {
            $('#section-dipinjam').show();
            $('#nama_peminjam, #no_telp_peminjam').prop('required', true);
            $('#nama_bank, #jangka_agunan, #penanggung_agunan, #nama_penerima, #nama_peminjam_kembali').prop('required', false);
        } else if (st === 'Diagunkan') {
            $('#section-diagunkan').show();
            $('#nama_bank, #jangka_agunan, #penanggung_agunan').prop('required', true);
            $('#nama_peminjam, #no_telp_peminjam, #nama_penerima, #nama_peminjam_kembali').prop('required', false);
        } else if (st === 'Dihibahkan') {
            $('#section-dihibahkan').show();
            $('#nama_penerima').prop('required', true);
            $('#nama_peminjam, #no_telp_peminjam, #nama_bank, #jangka_agunan, #penanggung_agunan, #nama_peminjam_kembali').prop('required', false);
        } else if (st === 'Dikembalikan') {
            $('#section-dikembalikan').show();
            $('#nama_peminjam_kembali').prop('required', true);
            $('#nama_peminjam, #no_telp_peminjam, #nama_bank, #jangka_agunan, #penanggung_agunan, #nama_penerima').prop('required', false);
        }
    });

    $('form').on('submit', function() {
        var st = $('#status').val();
        if (st === 'Dikembalikan') {
            $('#nama_peminjam').val($('#nama_peminjam_kembali').val());
            $('#no_telp_peminjam').val($('#no_telp_peminjam_kembali').val());
        }
    });
});
</script>
@endsection