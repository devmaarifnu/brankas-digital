@php
/**
 * Partial for Filter & Search Card across Brangkas modules (Surat Tanah, Akta Notaris, Data Aset Lembaga).
 * Designed to look identical to Record of Transfer search & filter card.
 */
$currentUrl = url()->current();
$fieldName = $filterField ?? 'kategori';
$searchPlaceholder = $placeholder ?? 'Cari nama, pihak terkait, catatan...';
@endphp

{{-- Filter & Search Card --}}
<div class="card shadow-sm border-0 rounded-3 mb-3" style="border: 1px solid #ebf1f6;">
    <div class="card-body p-3">
        <form action="{{ $currentUrl }}" method="GET" class="row g-2 align-items-center">
            {{-- Kolom Pencarian dengan Icon Kaca Pembesar --}}
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="ti ti-search"></i></span>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ $searchPlaceholder }}">
                </div>
            </div>

            {{-- Dropdown Kategori --}}
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="{{ $fieldName }}">
                    <option value="">-- Semua Kategori --</option>
                    @if(isset($filterOptions) && count($filterOptions) > 0)
                        @foreach($filterOptions as $opt)
                            @if(!empty($opt))
                                <option value="{{ $opt }}" {{ request($fieldName) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Dropdown Status Handover --}}
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="status_handover">
                    <option value="">-- Semua Status --</option>
                    @if(isset($statusList) && count($statusList) > 0)
                        @foreach($statusList as $st)
                            @if(!empty($st))
                                <option value="{{ $st }}" {{ request('status_handover') == $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endif
                        @endforeach
                    @else
                        <option value="Tersedia" {{ request('status_handover') === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Dipinjam" {{ request('status_handover') === 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="Diagunkan" {{ request('status_handover') === 'Diagunkan' ? 'selected' : '' }}>Diagunkan</option>
                        <option value="Dihibahkan" {{ request('status_handover') === 'Dihibahkan' ? 'selected' : '' }}>Dihibahkan</option>
                        <option value="Dikembalikan" {{ request('status_handover') === 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    @endif
                </select>
            </div>

            {{-- Tombol Filter dan Reset --}}
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-fill" title="Terapkan Filter">
                    <i class="ti ti-filter me-1"></i>Filter
                </button>
                <a href="{{ $currentUrl }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="ti ti-refresh"></i>
                </a>
            </div>
        </form>
    </div>
</div>
