<nav class="pc-sidebar enterprise-sidebar shadow">
    <div class="navbar-wrapper">

        <!-- Brand -->
        <div class="m-header enterprise-header">
            <a href="{{ url('/') }}" class="text-center fs-3 fw-bold">
                e-System
            </a>
        </div>

        <div class="navbar-content">
            <ul class="pc-navbar">

                @if (auth()->user()->canAccess('dashboard'))
                    <!-- Dashboard -->
                    <li class="pc-item">
                        <a href="{{ route('dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>
                @endif

                @php
                    $canAccessMaster = auth()->user()->canAccess('master_user') ||
                                       auth()->user()->canAccess('master_karyawan') ||
                                       auth()->user()->canAccess('master_barang') ||
                                       auth()->user()->canAccess('master_pekerjaan') ||
                                       auth()->user()->canAccess('master_lokasi_subcon');
                @endphp

                @if ($canAccessMaster)
                    <!-- Master Data -->
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
                            @if (auth()->user()->canAccess('master_user'))
                                <li class="pc-item">
                                    <a class="pc-link" href="{{ url('user') }}">Master User</a>
                                </li>
                            @endif

                            @if (auth()->user()->canAccess('master_karyawan'))
                                <li class="pc-item">
                                    <a class="pc-link" href="{{ url('karyawan') }}">Master Karyawan</a>
                                </li>
                            @endif

                            @if (auth()->user()->canAccess('master_barang'))
                                <li class="pc-item">
                                    <a class="pc-link" href="{{ url('barang') }}">Master Barang</a>
                                </li>
                            @endif

                            @if (auth()->user()->canAccess('master_pekerjaan'))
                                <li class="pc-item">
                                    <a class="pc-link" href="{{ url('pekerjaan') }}">Master Pekerjaan</a>
                                </li>
                            @endif

                            @if (auth()->user()->canAccess('master_lokasi_subcon'))
                                <li class="pc-item">
                                    <a class="pc-link" href="{{ url('lokasi-subcon') }}">Master Lokasi Subcon</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (auth()->user()->canAccess('formulir_pengerjaan'))
                    <!-- Pengerjaan -->
                    <li class="pc-item pc-caption">
                        <label class="text-secondary">Pengerjaan</label>
                    </li>

                    <li class="pc-item">
                        <a href="{{ url('pengerjaan') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-edit"></i></span>
                            <span class="pc-mtext">Formulir Pengerjaan</span>
                        </a>
                    </li>
                @endif

                @php
                    $canAccessReport = auth()->user()->canAccess('laporan_subcon') || auth()->user()->canAccess('log_report');
                @endphp

                @if ($canAccessReport)
                    <!-- Report -->
                    <li class="pc-item pc-caption">
                        <label class="text-secondary">Report</label>
                    </li>

                    @if (auth()->user()->canAccess('laporan_subcon'))
                        <li class="pc-item">
                            <a href="{{ url('laporan-subcon') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-file-analytics"></i></span>
                                <span class="pc-mtext">Laporan Subcon</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->canAccess('log_report'))
                        <li class="pc-item">
                            <a href="{{ route('log-report.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-history"></i></span>
                                <span class="pc-mtext">Log Report</span>
                            </a>
                        </li>
                    @endif
                @endif

            </ul>
        </div>
    </div>
</nav>
