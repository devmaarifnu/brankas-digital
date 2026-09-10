<nav class="sidebar-nav scroll-sidebar" data-simplebar="">
    <ul id="sidebarnav">

        {{-- RECORD OF HANDOVER --}}
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">UTAMA</span>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link {{ request()->routeIs('handover*') ? 'active' : '' }}"
               href="{{ route('handover.index') }}" aria-expanded="false">
                <span><i class="ti ti-file-import"></i></span>
                <span class="hide-menu">Record of Handover</span>
            </a>
        </li>

        {{-- SURAT SURAT BERHARGA --}}
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">SURAT SURAT BERHARGA</span>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link has-arrow {{ request()->routeIs('brangkas.surat*') || request()->routeIs('brangkas.akta*') ? 'active' : '' }}"
               href="javascript:void(0)" aria-expanded="false">
                <span><i class="ti ti-folder-open"></i></span>
                <span class="hide-menu">Surat Surat Berharga</span>
            </a>
            <ul aria-expanded="false" class="collapse first-level {{ request()->routeIs('brangkas.surat*') || request()->routeIs('brangkas.akta*') ? 'in' : '' }}">
                <li class="sidebar-item">
                    <a href="{{ route('brangkas.surat-tanah') }}"
                       class="sidebar-link {{ request()->routeIs('brangkas.surat-tanah') ? 'active' : '' }}">
                        <span><i class="ti ti-file-certificate"></i></span>
                        <span class="hide-menu">Arsip Surat Tanah</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('brangkas.akta-notaris') }}"
                       class="sidebar-link {{ request()->routeIs('brangkas.akta-notaris') ? 'active' : '' }}">
                        <span><i class="ti ti-certificate"></i></span>
                        <span class="hide-menu">Akta Notaris</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- DATA ASET LEMBAGA --}}
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">ASET</span>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link {{ request()->routeIs('brangkas.data-aset') ? 'active' : '' }}"
               href="{{ route('brangkas.data-aset') }}" aria-expanded="false">
                <span><i class="ti ti-building-bank"></i></span>
                <span class="hide-menu">Data Aset Lembaga</span>
            </a>
        </li>

        {{-- KEUANGAN --}}
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">KEUANGAN</span>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link has-arrow {{ request()->routeIs('keuangan*') ? 'active' : '' }}"
               href="javascript:void(0)" aria-expanded="false">
                <span><i class="ti ti-cash"></i></span>
                <span class="hide-menu">Keuangan</span>
            </a>
            <ul aria-expanded="false" class="collapse first-level {{ request()->routeIs('keuangan*') ? 'in' : '' }}">
                <li class="sidebar-item">
                    <a href="{{ route('keuangan.pengajuan') }}"
                       class="sidebar-link {{ request()->routeIs('keuangan.pengajuan') ? 'active' : '' }}">
                        <span><i class="ti ti-clipboard-list"></i></span>
                        <span class="hide-menu">Pengajuan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('keuangan.rekening-koran') }}"
                       class="sidebar-link {{ request()->routeIs('keuangan.rekening-koran') ? 'active' : '' }}">
                        <span><i class="ti ti-file-text"></i></span>
                        <span class="hide-menu">Rekening Koran</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('keuangan.buku-bank') }}"
                       class="sidebar-link {{ request()->routeIs('keuangan.buku-bank') ? 'active' : '' }}">
                        <span><i class="ti ti-book"></i></span>
                        <span class="hide-menu">Buku Bank</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('keuangan.buku-kas-tunai') }}"
                       class="sidebar-link {{ request()->routeIs('keuangan.buku-kas-tunai') ? 'active' : '' }}">
                        <span><i class="ti ti-wallet"></i></span>
                        <span class="hide-menu">Buku Kas Tunai</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('keuangan.buku-kas-umum') }}"
                       class="sidebar-link {{ request()->routeIs('keuangan.buku-kas-umum') ? 'active' : '' }}">
                        <span><i class="ti ti-report-money"></i></span>
                        <span class="hide-menu">Buku Kas Umum</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('keuangan.rekap-bulanan') }}"
                       class="sidebar-link {{ request()->routeIs('keuangan.rekap-bulanan') ? 'active' : '' }}">
                        <span><i class="ti ti-calendar-stats"></i></span>
                        <span class="hide-menu">Rekap Bulanan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('keuangan.rekap-tahunan') }}"
                       class="sidebar-link {{ request()->routeIs('keuangan.rekap-tahunan') ? 'active' : '' }}">
                        <span><i class="ti ti-chart-bar"></i></span>
                        <span class="hide-menu">Rekap Tahunan</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- SETTING --}}
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">PENGATURAN</span>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link has-arrow {{ request()->routeIs('setting*') ? 'active' : '' }}"
               href="javascript:void(0)" aria-expanded="false">
                <span><i class="ti ti-settings"></i></span>
                <span class="hide-menu">Setting</span>
            </a>
            <ul aria-expanded="false" class="collapse first-level {{ request()->routeIs('setting*') ? 'in' : '' }}">
                <li class="sidebar-item">
                    <a href="{{ route('setting.users') }}"
                       class="sidebar-link {{ request()->routeIs('setting.users') ? 'active' : '' }}">
                        <span><i class="ti ti-users"></i></span>
                        <span class="hide-menu">Users</span>
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</nav>
