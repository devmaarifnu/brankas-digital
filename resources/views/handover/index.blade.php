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

                        {{-- Nama Dokumen - Auto dari AJAX --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Nama Barang / Dokumen <span class="text-danger">*</span></label>
                            <select class="form-select" name="ref_id" id="ref_id" required disabled>
                                <option value="">-- Pilih Kategori terlebih dahulu --</option>
                            </select>
                            <input type="hidden" name="nama_dokumen" id="nama_dokumen_hidden">
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" id="status" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Dipinjam">Dipinjam</option>
                                <option value="Diagunkan">Diagunkan</option>
                                <option value="Dihibahkan">Dihibahkan</option>
                            </select>
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
                            <h6 class="text-success fw-bold mb-2"><i class="ti ti-gift me-1"></i>Data Penerima Hibah</h6>
                            <div class="mb-2">
                                <label class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_penerima" id="nama_penerima" placeholder="Nama lengkap penerima hibah">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Nomor Telepon Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="no_telp_penerima" id="no_telp_penerima" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        {{-- Tanggal Serah Terima --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Tanggal Serah Terima <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_serahterima" required value="{{ date('Y-m-d') }}">
                        </div>

                        {{-- Upload Bukti / Foto Serah Terima --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Upload Bukti / Foto Serah Terima</label>
                            <input type="file" class="form-control" name="file_bukti" accept="image/*,application/pdf">
                            <div class="form-text">JPG, PNG, atau PDF. Foto proses serah terima atau surat bukti.</div>
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Catatan Tambahan</label>
                            <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan lain jika ada..."></textarea>
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
            <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-list" style="color: #5D87FF;"></i>
                        <span>Log Record of Transfer</span>
                    </h5>
                    <span class="badge bg-light text-primary border">{{ $records->count() }} Data</span>
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
                                    <th>Tgl Serah Terima</th>
                                    <th class="pe-3">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $i => $rec)
                                <tr>
                                    <td class="ps-3">{{ $i+1 }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $rec->kategori }}</span></td>
                                    <td class="fw-semibold text-dark">{{ $rec->nama_dokumen }}</td>
                                    <td>
                                        @if($rec->status === 'Dipinjam')
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Dipinjam</span>
                                        @elseif($rec->status === 'Diagunkan')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Diagunkan</span>
                                        @elseif($rec->status === 'Dihibahkan')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Dihibahkan</span>
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
                                        @endif
                                    </td>
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
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="ti ti-inbox fs-2 d-block mb-2 text-muted"></i>
                                        Belum ada data perpindahan dokumen / aset.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section("scripts")
<script>
$(document).ready(function() {
    $('#kategori').on('change', function() {
        var kategori = $(this).val();
        var $refSelect = $('#ref_id');
        $refSelect.html('<option value="">Memuat dokumen...</option>').prop('disabled', true);

        if (!kategori) {
            $refSelect.html('<option value="">-- Pilih Kategori terlebih dahulu --</option>');
            return;
        }

        $.ajax({
            url: "{{ route('handover.items') }}",
            type: "GET",
            data: { kategori: kategori },
            success: function(data) {
                $refSelect.empty().append('<option value="">-- Pilih Dokumen / Aset --</option>');
                $.each(data, function(index, item) {
                    $refSelect.append('<option value="' + item.id + '" data-nama="' + item.nama_dokumen + '">' + item.nama_dokumen + '</option>');
                });
                $refSelect.prop('disabled', false);
            },
            error: function() {
                $refSelect.html('<option value="">Gagal memuat data</option>');
            }
        });
    });

    $('#ref_id').on('change', function() {
        var selectedText = $(this).find('option:selected').data('nama');
        $('#nama_dokumen_hidden').val(selectedText || '');
    });

    $('#status').on('change', function() {
        var status = $(this).val();
        $('#section-dipinjam').hide().find('input').prop('required', false);
        $('#section-diagunkan').hide().find('input').prop('required', false);
        $('#section-dihibahkan').hide().find('input').prop('required', false);

        if (status === 'Dipinjam') {
            $('#section-dipinjam').slideDown().find('input').prop('required', true);
        } else if (status === 'Diagunkan') {
            $('#section-diagunkan').slideDown().find('input').prop('required', true);
        } else if (status === 'Dihibahkan') {
            $('#section-dihibahkan').slideDown().find('input').prop('required', true);
        }
    });
});
</script>
@endsection