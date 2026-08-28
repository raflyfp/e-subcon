<nav class="pc-sidebar enterprise-sidebar shadow">
    <div class="navbar-wrapper">

        <!-- Brand -->
        <div class="m-header enterprise-header">
            <a href="{{ url('/') }}" class="text-center fs-3 fw-bold">
                e-Subcon
            </a>
        </div>

        <div class="navbar-content">
            <ul class="pc-navbar">

                <!-- Dashboard (semua role) -->
                <li class="pc-item">
                    <a href="{{ url('/') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                @if (auth()->user()->is_admin)
                    <!-- Master Data (admin only) -->
                    <li class="pc-item pc-caption">
                        <label>Master Data</label>
                    </li>

                    <li class="pc-item pc-hasmenu">
                        <a class="pc-link">
                            <span class="pc-micon"><i class="ti ti-database"></i></span>
                            <span class="pc-mtext">Master Data</span>
                            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                        </a>

                        <ul class="pc-submenu">
                            <li class="pc-item">
                                <a class="pc-link" href="{{ url('user') }}">Daftar User</a>
                            </li>
                            <li class="pc-item">
                                <a class="pc-link" href="{{ url('karyawan') }}">Daftar Karyawan</a>
                            </li>
                            <li class="pc-item">
                                <a class="pc-link" href="{{ url('barang') }}">Daftar Barang</a>
                            </li>
                            <li class="pc-item">
                                <a class="pc-link" href="{{ url('lokasi-subcon') }}">Lokasi Subcon</a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- Pengerjaan (semua role) -->
                <li class="pc-item pc-caption">
                    <label class="text-secondary">Pengerjaan</label>
                </li>

                <li class="pc-item">
                    <a href="{{ url('pengerjaan') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-edit"></i></span>
                        <span class="pc-mtext">Formulir Pengerjaan</span>
                    </a>
                </li>

                <li class="pc-item">
                    <a href="{{ url('laporan-subcon') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-file-analytics"></i></span>
                        <span class="pc-mtext">Laporan Subcon</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>