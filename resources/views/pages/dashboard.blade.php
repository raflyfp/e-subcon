@extends('layouts.mantis')

@section('title', 'Dashboard')

@section('content')

    <div class="col-12">
        {{-- Banner & Quick Action Direct to Form --}}
        <div class="card border mb-4 shadow-sm" style="border-left: 5px solid #2563eb !important;">
            <div class="card-body p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3 class="mb-1 fw-bold text-dark fs-4">
                        Halo, <span class="text-primary">{{ auth()->user()->name }}</span> 👋
                    </h3>
                    <p class="text-muted mb-0 small">
                        @if (auth()->user()->is_admin)
                            Panel Administrasi & Monitoring Rekapitulasi e-System
                        @else
                            Subcon: <strong><i class="ti ti-building me-1"></i>{{ $subcon->nama_lokasi ?? auth()->user()->name }}</strong>
                        @endif
                    </p>
                </div>
                <div>
                    <a href="{{ route('pengerjaan.index') }}" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                        <i class="ti ti-edit me-2"></i> Isi Formulir Pengerjaan
                    </a>
                </div>
            </div>
        </div>

        {{-- Monitoring Pengisian Karyawan Per Tanggal --}}
        <div class="card border shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="mb-1 text-dark fw-bold">
                            <i class="ti ti-calendar-check text-primary me-2"></i>Status Pengisian Form Karyawan
                        </h5>
                        <p class="text-muted small mb-0">Pantau kehadiran, urutkan data, dan cek jumlah submit form pengerjaan barang per tanggal</p>
                    </div>

                    {{-- Kontrol Navigasi Tanggal (Previous, Date Picker, Next, Hari Ini) --}}
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center flex-wrap gap-2" id="formTanggalMonitoring">
                            @if (auth()->user()->is_admin && request('lokasi_subcon_id'))
                                <input type="hidden" name="lokasi_subcon_id" value="{{ request('lokasi_subcon_id') }}">
                            @endif
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if (request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            @if (request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif

                            {{-- Tombol Sebelumnya --}}
                            <a href="{{ route('dashboard', array_merge(request()->query(), ['tanggal' => $prevDate, 'page' => 1])) }}"
                                class="btn btn-outline-secondary btn-sm" title="Hari Sebelumnya ({{ \Carbon\Carbon::parse($prevDate)->format('d/m/Y') }})">
                                <i class="ti ti-chevron-left me-1"></i> Sebelumnya
                            </a>

                            {{-- Input Tanggal --}}
                            <input type="date" name="tanggal" id="inputMonitoringTanggal" class="form-control form-control-sm text-center fw-bold"
                                style="width: 145px;" value="{{ $tanggal }}" onchange="document.getElementById('formTanggalMonitoring').submit();">

                            {{-- Tombol Selanjutnya --}}
                            <a href="{{ route('dashboard', array_merge(request()->query(), ['tanggal' => $nextDate, 'page' => 1])) }}"
                                class="btn btn-outline-secondary btn-sm" title="Hari Selanjutnya ({{ \Carbon\Carbon::parse($nextDate)->format('d/m/Y') }})">
                                Selanjutnya <i class="ti ti-chevron-right ms-1"></i>
                            </a>

                            {{-- Tombol Hari Ini --}}
                            @if ($tanggal != $today)
                                <a href="{{ route('dashboard', array_merge(request()->query(), ['tanggal' => $today, 'page' => 1])) }}"
                                    class="btn btn-primary btn-sm fw-semibold">
                                    Hari Ini
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Bar Indikator Filter Status, Sorting & Pencarian Karyawan --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3 pt-3 border-top">
                    {{-- Filter Cepat Berdasarkan Status --}}
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-semibold">
                            <i class="ti ti-calendar me-1 text-primary"></i>
                            {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                            @if ($tanggal == $today)
                                <span class="badge bg-primary ms-1">Hari Ini</span>
                            @endif
                        </span>

                        {{-- Tombol Filter Semua --}}
                        <a href="{{ route('dashboard', array_merge(request()->except(['status', 'page']))) }}"
                            class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }} fw-semibold">
                            Semua ({{ $sudahMengisiCount + $belumMengisiCount }})
                        </a>

                        {{-- Tombol Filter Sudah Mengisi --}}
                        <a href="{{ route('dashboard', array_merge(request()->query(), ['status' => 'sudah', 'page' => 1])) }}"
                            class="btn btn-sm {{ request('status') === 'sudah' ? 'btn-success text-white' : 'btn-outline-success' }} fw-semibold">
                            <i class="ti ti-check me-1"></i> Sudah Isi ({{ $sudahMengisiCount }})
                        </a>

                        {{-- Tombol Filter Belum Mengisi --}}
                        <a href="{{ route('dashboard', array_merge(request()->query(), ['status' => 'belum', 'page' => 1])) }}"
                            class="btn btn-sm {{ request('status') === 'belum' ? 'btn-secondary text-white' : 'btn-outline-secondary' }} fw-semibold">
                            <i class="ti ti-x me-1"></i> Belum Isi ({{ $belumMengisiCount }})
                        </a>
                    </div>

                    {{-- Sort Dropdown & Search Form --}}
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        {{-- Dropdown Sorting --}}
                        <form method="GET" action="{{ route('dashboard') }}" class="d-inline-block" id="formSorting">
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if (request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            @if (auth()->user()->is_admin && request('lokasi_subcon_id'))
                                <input type="hidden" name="lokasi_subcon_id" value="{{ request('lokasi_subcon_id') }}">
                            @endif

                            <div class="input-group input-group-sm" style="min-width: 195px;">
                                <span class="input-group-text bg-light text-muted"><i class="ti ti-arrows-sort"></i></span>
                                <select name="sort" class="form-select form-select-sm" onchange="document.getElementById('formSorting').submit();">
                                    <option value="nama_asc" {{ request('sort', 'nama_asc') === 'nama_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                                    <option value="nama_desc" {{ request('sort') === 'nama_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
                                    <option value="belum_dulu" {{ request('sort') === 'belum_dulu' ? 'selected' : '' }}>Belum Isi Dahulu</option>
                                    <option value="sudah_dulu" {{ request('sort') === 'sudah_dulu' ? 'selected' : '' }}>Sudah Isi Dahulu</option>
                                    <option value="submit_desc" {{ request('sort') === 'submit_desc' ? 'selected' : '' }}>Submit Terbanyak</option>
                                </select>
                            </div>
                        </form>

                        {{-- Filter Lokasi Subcon (Khusus Admin) --}}
                        @if (auth()->user()->is_admin)
                            <form method="GET" action="{{ route('dashboard') }}" class="d-inline-block">
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                @if (request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if (request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                                @if (request('sort'))
                                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                                @endif
                                <select name="lokasi_subcon_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 160px;">
                                    <option value="">-- Semua Lokasi --</option>
                                    @foreach ($lokasiList as $l)
                                        <option value="{{ $l->id }}" {{ request('lokasi_subcon_id') == $l->id ? 'selected' : '' }}>
                                            {{ $l->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif

                        {{-- Search Form Karyawan (Auto-Filter on Typing) --}}
                        <form method="GET" action="{{ route('dashboard') }}" id="searchFormKaryawan" class="d-flex align-items-center gap-1">
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            @if (request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            @if (request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                            @if (auth()->user()->is_admin && request('lokasi_subcon_id'))
                                <input type="hidden" name="lokasi_subcon_id" value="{{ request('lokasi_subcon_id') }}">
                            @endif

                            <div class="input-group input-group-sm" style="width: 220px;">
                                <input type="text" name="search" id="inputSearchKaryawan" class="form-control"
                                    placeholder="Cari nama / no karyawan..." value="{{ request('search') }}" autocomplete="off">
                                <button type="submit" class="btn btn-outline-secondary" title="Cari">
                                    <i class="ti ti-search"></i>
                                </button>
                                @if (request('search'))
                                    <a href="{{ route('dashboard', array_merge(request()->except(['search', 'page']))) }}"
                                        class="btn btn-outline-danger" title="Hapus Filter Pencarian">
                                        <i class="ti ti-x"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tabel Daftar Karyawan, Status, Jumlah Submit & Rincian --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="table-monitoring" style="border-color: #cbd5e1;">
                        <thead class="bg-light text-dark fw-bold text-center">
                            <tr>
                                <th style="width: 45px;">No</th>
                                <th class="text-start" style="width: 220px;">
                                    <a href="{{ route('dashboard', array_merge(request()->query(), ['sort' => request('sort') === 'nama_asc' ? 'nama_desc' : 'nama_asc', 'page' => 1])) }}"
                                        class="text-dark text-decoration-none d-flex align-items-center justify-content-between" title="Klik untuk mengurutkan nama">
                                        <span>Karyawan</span>
                                        <i class="ti {{ request('sort') === 'nama_desc' ? 'ti-sort-descending' : 'ti-sort-ascending' }} text-muted ms-1"></i>
                                    </a>
                                </th>
                                @if (auth()->user()->is_admin)
                                    <th style="width: 150px;">Lokasi Subcon</th>
                                @endif
                                <th style="width: 130px;">
                                    <a href="{{ route('dashboard', array_merge(request()->query(), ['sort' => request('sort') === 'belum_dulu' ? 'sudah_dulu' : 'belum_dulu', 'page' => 1])) }}"
                                        class="text-dark text-decoration-none d-flex align-items-center justify-content-center" title="Klik untuk mengurutkan status">
                                        <span>Status</span>
                                        <i class="ti ti-arrows-sort text-muted ms-1"></i>
                                    </a>
                                </th>
                                <th style="width: 130px;">
                                    <a href="{{ route('dashboard', array_merge(request()->query(), ['sort' => request('sort') === 'submit_desc' ? 'nama_asc' : 'submit_desc', 'page' => 1])) }}"
                                        class="text-dark text-decoration-none d-flex align-items-center justify-content-center" title="Klik untuk mengurutkan jumlah submit">
                                        <span>Jumlah Submit</span>
                                        <i class="ti ti-arrows-sort text-muted ms-1"></i>
                                    </a>
                                </th>
                                <th class="text-start">Rincian Pengerjaan Barang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($karyawanList as $karyawan)
                                @php
                                    $records = $pengerjaanTanggal->get($karyawan->id) ?? $pengerjaanTanggal->get((string) $karyawan->id) ?? collect([]);
                                    $submitCount = $records->count();
                                    $hasFilled = $submitCount > 0;
                                @endphp
                                <tr style="background-color: {{ $hasFilled ? '#ffffff' : '#fcfcfc' }};">
                                    <td class="text-center">{{ $karyawanList->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong class="text-dark">{{ $karyawan->nama_karyawan }}</strong>
                                        <div class="small text-muted">No: {{ $karyawan->no_karyawan }}</div>
                                    </td>
                                    @if (auth()->user()->is_admin)
                                        <td class="text-center">
                                            <span class="text-secondary small fw-semibold">
                                                {{ $karyawan->lokasiSubcon->nama_lokasi ?? '-' }}
                                            </span>
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        @if ($hasFilled)
                                            <span class="badge bg-success px-2 py-1 fw-semibold">
                                                <i class="ti ti-circle-check me-1"></i> Sudah Isi
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1 fw-semibold">
                                                <i class="ti ti-clock me-1"></i> Belum Isi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($hasFilled)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 fw-bold fs-7">
                                                {{ $submitCount }}x Submit
                                            </span>
                                        @else
                                            <span class="text-muted small">0x</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($hasFilled)
                                            <div class="d-flex flex-column gap-1">
                                                @foreach ($records as $rec)
                                                    <div class="p-2 bg-light rounded border small d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                        <div>
                                                            <strong class="text-primary">[{{ $rec->barang->kode_barang ?? '-' }}]</strong>
                                                            {{ $rec->barang->nama_barang ?? '-' }}
                                                            @if ($rec->jenis_pekerjaan)
                                                                <span class="text-muted ms-1">• {{ $rec->jenis_pekerjaan }}</span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <strong class="text-dark">{{ number_format($rec->jumlah, 0, ',', '.') }}</strong>
                                                            <span class="text-muted">{{ $rec->barang->satuan ?? 'PCS' }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small italic">- Belum ada data pengerjaan -</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->is_admin ? 6 : 5 }}" class="text-center py-4 text-muted">
                                        <i class="ti ti-info-circle fs-3 d-block mb-1"></i>
                                        @if (request('search'))
                                            Tidak ada karyawan yang sesuai dengan kata kunci "<strong>{{ request('search') }}</strong>".
                                        @elseif (request('status') === 'belum')
                                            Semua karyawan sudah mengisi form pengerjaan pada tanggal ini! 🎉
                                        @elseif (request('status') === 'sudah')
                                            Belum ada karyawan yang mengisi form pengerjaan pada tanggal ini.
                                        @else
                                            Tidak ada data karyawan yang terdaftar.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer Pagination --}}
            @if ($karyawanList->hasPages() || $karyawanList->total() > 0)
                <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan <strong>{{ $karyawanList->firstItem() ?? 0 }}</strong> - <strong>{{ $karyawanList->lastItem() ?? 0 }}</strong> dari total <strong>{{ $karyawanList->total() }}</strong> karyawan
                    </small>
                    <div class="pagination-container">
                        {{ $karyawanList->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-filter otomatis saat mengetik di kotak pencarian
            let searchTimeout = null;
            $('#inputSearchKaryawan').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    $('#searchFormKaryawan').submit();
                }, 400); // Tunggu 400ms setelah user selesai mengetik
            });

            // Fokus otomatis kembali ke input pencarian setelah reload
            let searchInput = document.getElementById('inputSearchKaryawan');
            if (searchInput && searchInput.value) {
                searchInput.focus();
                let len = searchInput.value.length;
                searchInput.setSelectionRange(len, len);
            }
        });
    </script>

    @if (session('unauthorized'))
        <script>
            Swal.fire({
                icon: "{{ session('unauthorized.type') }}",
                title: "{{ session('unauthorized.title') }}",
                text: "{{ session('unauthorized.text') }}",
                confirmButtonText: 'Kembali',
            });
        </script>
    @endif
@endpush
