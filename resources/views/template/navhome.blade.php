<nav class="navbar navbar-expand-lg navbar-dark bg-navbar-landing py-3 shadow">
    <div class="container container-navbar">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('assets/images/logos/Logo_Sipinter_Panjang.png') }}" alt="Logo Nu" width="140">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('informasi') }}">Informasi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('kontak') }}">Kontak</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('verify') }}">Verifikasi Dokumen</a>
                </li>
            </ul>
            @if(\Illuminate\Support\Facades\Auth::user() !== NULL)
            <ul class="navbar-nav flex-row align-items-center justify-content-end">
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <img src="../assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                        <div class="message-body">
                            <a href="{{ in_array(auth()->user()->role, ["operator"]) ? route('mysatpen') : route('a.dash') }}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-3">My Profile</p>
                            </a>
                            <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-key fs-6"></i>
                                <p class="mb-0 fs-3">Ganti Password</p>
                            </a>
                            <a href="{{ route('logout') }}" class="btn btn-outline-primary mx-3 mt-3 d-block">Logout</a>
                        </div>
                    </div>
                </li>
            </ul>
            @else
            <div class="d-flex">
                <a href="{{ route('ceknpsn') }}" class="btn btn-yellow mx-2" type="submit">Daftar</a>
                <a href="{{ route('login') }}" class="btn btn-yellow" type="submit">Masuk</a>
            </div>
            @endif
        </div>
    </div>
</nav>
