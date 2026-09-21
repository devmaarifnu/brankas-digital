@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Keuangan</a></li>
            <li><a href="javascript:void(0)">Rekap Tahunan</a></li>
        </ul>
    </nav>

    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-chart-bar fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Rekap Tahunan</h4>
                    <small class="text-muted">Rekapitulasi keuangan lembaga per tahun anggaran</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ti ti-table" style="color: #5D87FF;"></i>
                <span>Tabel Rekap Keuangan Tahunan</span>
            </h5>
            <span class="badge bg-light text-primary border">{{ $data->count() }} Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;">
                        <tr>
                            <th class="ps-3" width="40">#</th>
                            <th>Tahun</th>
                            <th>Total Pemasukan</th>
                            <th>Total Pengeluaran</th>
                            <th>Saldo Akhir</th>
                            <th>Keterangan</th>
                            <th class="pe-3 text-center" width="90">File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $item)
                        <tr>
                            <td class="ps-3">{{ $i+1 }}</td>
                            <td class="fw-bold fs-5 text-dark">{{ $item->tahun }}</td>
                            <td class="text-success fw-semibold">Rp {{ number_format($item->total_pemasukan,0,",",".") }}</td>
                            <td class="text-danger fw-semibold">Rp {{ number_format($item->total_pengeluaran,0,",",".") }}</td>
                            <td class="fw-bold {{ $item->saldo >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($item->saldo,0,",",".") }}</td>
                            <td><small class="text-muted">{{ $item->keterangan ?? "-" }}</small></td>
                            <td class="pe-3 text-center">
                                @if($item->file_rekap)
                                <a href="{{ asset($item->file_rekap) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat Rekap">
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
                                Belum ada data rekap tahunan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection