@extends('template.general', [
    'title' => "Reset Password - Sistem Administrasi Pendidikan Terpadu LP Ma'arif NU PBNU",
])

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
.sip-right-logo{display:flex; align-items:center; justify-content:center; flex:1;}
.sip-right-logo img{max-width:200px; opacity:0.9;}
</style>
@endsection

@section('container')
<div class="sip-shell">
    <div class="sip-left">
        <div class="sip-card">
            <a href="{{ route('home') }}" class="sip-back-home">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M11 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kembali ke Beranda
            </a>
            <a href="{{ route('home') }}" class="sip-brand">
                <img src="{{ asset('assets/images/logos/Logo_Sipinter_Panjang.png') }}" alt="SIPINTER">
            </a>

            <h1>Reset Password</h1>
            <p class="sip-sub">Masukkan email dan password baru Anda untuk mereset akun.</p>

            @include('template.alert')

            <form action="{{ route('reset.send') }}" method="post">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="sip-field">
                    <label for="email">Email</label>
                    <div class="sip-input-wrap">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3 6l9 7 9-7" stroke="currentColor" stroke-width="1.8"/></svg>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Masukkan alamat email anda" class="@error('email') is-invalid @enderror">
                    </div>
                    @error('email')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                </div>
                <div class="sip-field">
                    <label for="new_password">Password Baru</label>
                    <div class="sip-input-wrap has-toggle">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="5" y="10.5" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.8"/></svg>
                        <input id="new_password" name="new_password" type="password" placeholder="Masukkan password baru" class="@error('new_password') is-invalid @enderror">
                        <button type="button" class="sip-toggle-eye" onclick="sipToggleEye('new_password', this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                        </button>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                        </button>
                    </div>
                    @error('new_password')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                </div>
                <div class="sip-field">
                    <label for="password_confirm">Konfirmasi Password</label>
                    <div class="sip-input-wrap has-toggle">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="5" y="10.5" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.8"/></svg>
                        <input id="password_confirm" name="password_confirm" type="password" placeholder="Konfirmasi password anda" class="@error('password_confirm') is-invalid @enderror">
                        <button type="button" class="sip-toggle-eye" onclick="sipToggleEye('password_confirm', this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                        </button>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                        </button>
                    </div>
                    <small id="password-match-message" style="color:#c0392b;"></small>
                    @error('password_confirm')<small style="color:#c0392b;">{{ $message }}</small>@enderror
                </div>

                <button type="submit" class="sip-btn-submit">
                    Reset Password
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </form>
        </div>
    </div>

    <div class="sip-right">
        <div class="sip-lattice"></div>
        <div class="sip-right-inner">
            <div class="sip-right-logo">
                <img src="{{ asset('assets/images/logos/Logo_NU_Putih_PNG.png') }}" alt="Logo NU">
            </div>
            <div class="sip-help-card">
                <h4>Helpdesk</h4>
                <div class="sip-help-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3 6l9 7 9-7" stroke="currentColor" stroke-width="1.8"/></svg>
                    bhp.maarifnu@gmail.com
                </div>
                <div class="sip-help-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 4h4l2 5-2.5 1.5a12 12 0 0 0 6 6L15 14l5 2v4a2 2 0 0 1-2 2C9.6 22 2 14.4 2 6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6"/></svg>
                    021-3904115 &nbsp;&bull;&nbsp; Fax 021-31906679
                </div>
                <div class="sip-wa-row">
                    <a class="sip-wa-pill" href="https://wa.me/6285883858897">WA 1 &middot; 0858-8385-8897</a>
                    <a class="sip-wa-pill" href="https://wa.me/6281319868302">WA 2 &middot; 0813-1986-8302</a>
                </div>
                <div class="sip-address">Gedung PBNU II, Lt. 2, Jl. Taman Amir Hamzah No. 5, Pegangsaan, Menteng, Jakarta Pusat 10320</div>
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
$('#password_confirm').on('keyup', function() {
    var p = $('#new_password').val();
    var c = $(this).val();
    var m = $('#password-match-message');
    if (c.length > 0) {
        if (p !== c) { m.text('Password tidak cocok!').show(); }
        else { m.text('').hide(); }
    } else { m.text('').hide(); }
});
</script>
@endsection
