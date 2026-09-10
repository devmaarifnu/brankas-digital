@extends('template.general', [
    'title' => 'Sipinter - Registrasi Berhasil',
])

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&amp;family=IBM+Plex+Mono:wght@500;600&amp;display=swap" rel="stylesheet">
@endsection
@endsection

@section('container')
<div class="sip-shell">
    <div class="sip-card">
        <a href="{{ route('home') }}" class="sip-brand"><img src="{{ asset('assets/images/logos/Logo_Sipinter_Panjang.png') }}" alt="SIPINTER"></a>

        <div class="sip-success-icon">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="#0B6B3A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h1>Registrasi Berhasil</h1>
        <p>Berikut nomor registrasi satuan pendidikan Anda. Simpan nomor ini dengan baik.</p>

        <div class="sip-reg-number">{{ Session::get('regNumber') }}</div>

        <p style="font-size:12px;">Untuk masuk ke portal, gunakan nomor registrasi di atas sebagai username. Nomor registrasi juga dikirimkan ke email Anda.</p>

        <a href="{{ route('login') }}" class="sip-btn">
            Halaman Login
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>
</div>
@endsection
