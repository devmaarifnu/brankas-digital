@extends('template.general', [
    'title' => 'Brangkas Digital - Login',
])

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
.sip-left {
    display: flex;
    align-items: center;
    justify-content: center;
}
.brangkas-logo-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin-bottom: 22px;
}
.brangkas-logo-img {
    height: 78px;
    width: auto;
    object-fit: contain;
    filter: drop-shadow(0 2px 8px rgba(0,0,0,0.08));
}
</style>
@endsection

@section('container')
<div class="sip-shell">
    <div class="sip-left">
        <div class="sip-card" style="max-width: 420px; width: 100%;">
            <div class="brangkas-logo-wrap">
                <img src="{{ asset('assets/images/logos/fotobrankas-removebg.png') }}?v=4" alt="Brangkas Digital" class="brangkas-logo-img">
                <div class="d-flex flex-column text-start">
                    <span class="fw-bold text-dark lh-1" style="font-size: 17px; letter-spacing: 0.5px;">BRANGKAS DIGITAL</span>
                    <span class="fw-bold text-primary lh-1 mt-1" style="font-size: 11.5px; letter-spacing: 0.5px;">LP MA'ARIF NU PBNU</span>
                </div>
            </div>

            <div class="text-center mb-4">
                <h5 class="fw-bold text-dark mb-1">Masuk ke Sistem</h5>
                <small class="text-muted">Brangkas Digital &bull; LP Ma'arif NU PBNU</small>
            </div>

            @include('template.alert')

            <form action="{{ route('login.proses') }}" method="post">
                @csrf
                <div class="sip-field">
                    <label for="username">Username</label>
                    <div class="sip-input-wrap">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.6" stroke="currentColor" stroke-width="1.8"/><path d="M4.5 20c1.4-4 5-5.6 7.5-5.6s6.1 1.6 7.5 5.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" placeholder="Masukkan username" class="@error('username') is-invalid @enderror" required autofocus>
                    </div>
                    @error('username')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                </div>
                <div class="sip-field">
                    <label for="password">Password</label>
                    <div class="sip-input-wrap has-toggle">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="5" y="10.5" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.8"/></svg>
                        <input id="password" name="password" type="password" placeholder="Masukkan password" class="@error('password') is-invalid @enderror" required>
                        <button type="button" class="sip-toggle-eye" aria-label="Tampilkan password" onclick="sipToggleEye('password', this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                        </button>
                    </div>
                    @error('password')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                </div>

                <div class="sip-row-between">
                    <label class="sip-checkbox"><input type="checkbox" name="remember"> Ingat Saya</label>
                    <a href="{{ route('forgot') }}">Lupa Password?</a>
                </div>

                <button type="submit" class="sip-btn-submit" style="background: linear-gradient(135deg, #0f5132 0%, #198754 100%);">
                    Masuk
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </form>

            <div class="sip-foot-note mt-3">
                LP Ma'arif NU PBNU &bull; brankas.maarifnu.or.id
            </div>
        </div>
    </div>

    <div class="sip-right" style="background: linear-gradient(145deg, #072a1a 0%, #0f5132 100%);">
        <div class="sip-lattice"></div>
        <div class="sip-right-inner">
            <div class="sip-right-logo" style="justify-content:flex-start; flex:none; margin-bottom:16px;">
                <img src="{{ asset('assets/images/logos/brankas-logo-v3.png') }}?v=1" alt="Logo Brangkas" style="max-height:150px; width:auto; filter: drop-shadow(0 6px 20px rgba(0,0,0,0.5));">
            </div>

            <div class="sip-brand-text">
                <h2 style="color: #fff;">Brangkas Digital</h2>
                <span style="color: rgba(255,255,255,0.85);">LP Ma'arif NU PBNU &bull; brankas.maarifnu.or.id</span>
            </div>

            <ul class="sip-feature-list">
                <li><span class="sip-ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Pencatatan Dokumen Pindah Tangan (Record of Handover)</li>
                <li><span class="sip-ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Arsip Surat Tanah & Sertifikat Hak Milik</li>
                <li><span class="sip-ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Manajemen Dokumen Akta Notaris Resmi</li>
                <li><span class="sip-ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Pengelolaan Data Aset Lembaga Terintegrasi</li>
                <li><span class="sip-ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Tata Kelola Dokumen & Rekapitulasi Keuangan</li>
            </ul>

            <div class="sip-help-card" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                <h4 style="color: #fff;">Helpdesk Brangkas Digital</h4>
                <div class="sip-help-row" style="color: rgba(255,255,255,0.85);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3 6l9 7 9-7" stroke="currentColor" stroke-width="1.8"/></svg>
                    brankas.maarifnu@gmail.com
                </div>
                <div class="sip-address" style="color: rgba(255,255,255,0.75);">Gedung PBNU II, Lt. 2, Jl. Taman Amir Hamzah No. 5, Pegangsaan, Menteng, Jakarta Pusat 10320</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function sipToggleEye(id, btn) {
    var p = document.getElementById(id);
    var s = btn.querySelector('svg');
    var eyeOff = '<path d="M3 3l18 18M10.5 9a3 3 0 0 1 4 4M6.5 6.5c-2 2-4 5.5-4 5.5s3.6 7 10 7c2 0 3.8-.6 5.3-1.6M21.5 17.5l-3-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>';
    var eyeOn = '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>';
    if (p.type === 'password') { p.type = 'text'; s.innerHTML = eyeOff; }
    else { p.type = 'password'; s.innerHTML = eyeOn; }
}
</script>
@endsection