@extends('template.general', [
    'title' => 'Sipinter - Ajukan NPSN Virtual',
])

@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/custom-selectpicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&amp;family=IBM+Plex+Mono:wght@500;600&amp;display=swap" rel="stylesheet">
@endsection

@section('container')
<div class="sip-shell">
    <div class="sip-left sip-left-wide">
        <div class="sip-card sip-card-wide">
            <a href="{{ route('home') }}" class="sip-back-home">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M11 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kembali ke Beranda
            </a>
            <a href="{{ route('home') }}" class="sip-brand"><img src="{{ asset('assets/images/logos/Logo_Sipinter_Panjang.png') }}" alt="SIPINTER"></a>
            
            <div class="text-center">
                <h6 class="mb-1">Sistem Administrasi Pendidikan Terpadu</h6>
                <h6 class="sip-sub mb-3">Lembaga Pendidikan Ma'arif NU PBNU</h6>
            </div>
            
            @include('template.alert')

            <form action="{{ route('npsnvirtual.request') }}" method="post">
                @csrf
                <div class="sip-field">
                    <label for="nama_sekolah">Nama Sekolah</label>
                    <div class="sip-input-wrap">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M3 10l9-5 9 5-9 5-9-5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M7 12v5c0 1.2 2.2 2.5 5 2.5s5-1.3 5-2.5v-5" stroke="currentColor" stroke-width="1.8"/></svg>
                        <input id="nama_sekolah" name="nama_sekolah" type="text" value="{{ old('nama_sekolah') }}" placeholder="Nama lengkap satuan pendidikan" class="@error('nama_sekolah') is-invalid @enderror">
                    </div>
                    @error('nama_sekolah')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                </div>

                <p style="font-size:11px;color:var(--sip-ink-soft);margin:-6px 0 10px;">*) pastikan email aktif &mdash; konfirmasi permohonan akan dikirim ke alamat ini.</p>

                <div class="sip-field-grid">
                    <div class="sip-field">
                        <label for="email">Email</label>
                        <div class="sip-input-wrap">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3 6l9 7 9-7" stroke="currentColor" stroke-width="1.8"/></svg>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="email@sekolah.sch.id" class="@error('email') is-invalid @enderror">
                        </div>
                        @error('email')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                    </div>
                    <div class="sip-field">
                        <label for="nik_kepsek">NIK Kepala Sekolah</label>
                        <div class="sip-input-wrap">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><circle cx="8.5" cy="11" r="2" stroke="currentColor" stroke-width="1.6"/><path d="M13 10h5M13 14h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            <input id="nik_kepsek" name="nik_kepsek" type="text" value="{{ old('nik_kepsek') }}" placeholder="16 digit NIK" inputmode="numeric" class="@error('nik_kepsek') is-invalid @enderror">
                        </div>
                        @error('nik_kepsek')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="sip-field-grid">
                    <div class="sip-field">
                        <label for="jenjang">Jenjang Pendidikan</label>
                        <select class="selectpicker @error('jenjang') is-invalid @enderror" data-show-subtext="false" data-live-search="true" name="jenjang" data-container="body">
                            @foreach($jenjang as $row)
                                <option value="{{ $row->id_jenjang }}">{{ $row->nm_jenjang }}</option>
                            @endforeach
                        </select>
                        @error('jenjang')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                    </div>
                    <div class="sip-field">
                        <label for="provinsi">Provinsi</label>
                        <select class="selectpicker @error('provinsi') is-invalid @enderror" data-show-subtext="false" data-live-search="true" name="provinsi" data-container="body">
                            @foreach($provinsi as $row)
                                <option value="{{ $row->id_prov }}">{{ $row->nm_prov }}</option>
                            @endforeach
                        </select>
                        @error('provinsi')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="sip-field-grid">
                    <div class="sip-field">
                        <label for="kabupaten">Kabupaten/Kota</label>
                        <select class="selectpicker @error('kabupaten') is-invalid @enderror" data-show-subtext="false" data-live-search="true" name="kabupaten" data-container="body">
                            @foreach($kabupaten as $row)
                                <option value="{{ $row->id_kab }}">{{ $row->nama_kab }}</option>
                            @endforeach
                        </select>
                        @error('kabupaten')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                    </div>
                    <div class="sip-field">
                        <label for="alamat">Alamat</label>
                        <div class="sip-input-wrap">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" style="top:12px;"><path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21Z" stroke="currentColor" stroke-width="1.8"/></svg>
                            <textarea id="alamat" name="alamat" rows="1" placeholder="Alamat lengkap satuan pendidikan" class="@error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                        </div>
                        @error('alamat')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                    </div>
                </div>

                <button type="submit" class="sip-btn-submit">
                    Ajukan NPSN Virtual
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </form>

            <div class="sip-foot-note">Sudah Punya NPSN? <a href="{{ route('ceknpsn') }}">Daftar Sekarang</a></div>
        </div>
    </div>

    <div class="sip-right">
        <div class="sip-lattice"></div>
        <div class="sip-right-inner">
            <div class="sip-right-logo" style="justify-content:flex-start; flex:none; margin-bottom:14px;">
                <img src="{{ asset('assets/images/logos/Logo_NU_Putih_PNG.png') }}" alt="Logo NU" style="max-width:140px;">
            </div>
            
            <div class="sip-brand-text">
                <h2>Sipinter LP Ma'arif NU PBNU</h2>
                <span>Pelayanan Terintegrasi dan Terpadu</span>
            </div>

            <ul class="sip-feature-list">
                <li><span class="sip-ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Menjadi Pusat Data Satuan Pendidikan Ma'arif NU</li>
                <li><span class="sip-ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Layanan Izin Sistem OSS/NIB</li>
                <li><span class="sip-ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Layanan Badan Hukum NU (BHPNU)</li>
                <li><span class="sip-ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Layanan Bantuan Pendidikan Ma'arif</li>
                <li><span class="sip-ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Layanan Beasiswa Pendidikan</li>
            </ul>
            <div class="sip-help-card">
                <h4>Helpdesk</h4>
                <div class="sip-help-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3 6l9 7 9-7" stroke="currentColor" stroke-width="1.8"/></svg>bhp.maarifnu@gmail.com</div>
                <div class="sip-help-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 4h4l2 5-2.5 1.5a12 12 0 0 0 6 6L15 14l5 2v4a2 2 0 0 1-2 2C9.6 22 2 14.4 2 6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6"/></svg>021-3904115 &nbsp;&bull;&nbsp; Fax 021-31906679</div>
                <div class="sip-wa-row"><a class="sip-wa-pill" href="https://wa.me/6285883858897">WA 1 &middot; 0858-8385-8897</a><a class="sip-wa-pill" href="https://wa.me/6281319868302">WA 2 &middot; 0813-1986-8302</a></div>
                <div class="sip-address">Gedung PBNU II, Lt. 2, Jl. Taman Amir Hamzah No. 5, Pegangsaan, Menteng, Jakarta Pusat 10320</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script>
        $('.selectpicker').selectpicker({container:'body'});
        $("select[name='provinsi']").on("change",function(){$.ajax({url:"{{ route('api.kabupatenbyprov',':param') }}".replace(':param',$(this).val()),type:"GET",dataType:'json',success:function(r){var o="";$.each(r,function(k,v){o+='<option value="'+v.id_kab+'">'+v.nama_kab+'</option>'});$("select[name='kabupaten']").html(o);$('.selectpicker').selectpicker('refresh')}})});
    </script>
@endsection
