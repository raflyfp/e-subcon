@extends('layouts.mantis')
@section('title', 'Laporan Subcon')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="content mb-1">Laporan Subcon</h1>
            <p class="text-muted mb-0">Rekapitulasi data pengerjaan barang subcon berdasarkan periode dan filter</p>
        </div>
        <div>
            <a href="{{ url('pengerjaan') }}" class="btn btn-outline-primary">
                <i class="ti ti-edit me-1"></i> Buka Formulir Pengerjaan
            </a>
        </div>
    </div>

    {{-- Filter Card (Flat & Simple) --}}
    <div class="card mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark fw-bold">
                <i class="ti ti-filter text-primary me-2"></i>Filter Laporan
            </h5>
            <div class="d-flex gap-1 flex-wrap">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setFilterPeriod('today')">Hari Ini</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setFilterPeriod('this_month')">Bulan Ini</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setFilterPeriod('last_month')">Bulan Lalu</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setFilterPeriod('all')">Semua Waktu</button>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('laporan.index') }}" id="filterForm">
                <div class="row g-3">
                    {{-- Tanggal Mulai --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" id="filter_tanggal_mulai"
                            value="{{ $tanggalMulai }}">
                    </div>

                    {{-- Tanggal Akhir --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="tanggal_akhir" id="filter_tanggal_akhir"
                            value="{{ $tanggalAkhir }}">
                    </div>

                    {{-- Admin: Filter Karyawan --}}
                    @if (auth()->user()->is_admin)
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Karyawan</label>
                            <select class="form-select select2-filter" name="karyawan_id" id="filter_karyawan_id">
                                <option value="">-- Semua Karyawan --</option>
                                @foreach ($karyawanList as $k)
                                    <option value="{{ $k->id }}" {{ $selectedKaryawan == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_karyawan }} ({{ $k->no_karyawan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Filter Barang --}}
                    <div class="col-md-{{ auth()->user()->is_admin ? '3' : '3' }}">
                        <label class="form-label fw-semibold">Barang</label>
                        <select class="form-select select2-filter" name="barang_id" id="filter_barang_id">
                            <option value="">-- Semua Barang --</option>
                            @foreach ($barangList as $b)
                                <option value="{{ $b->id }}" {{ $selectedBarang == $b->id ? 'selected' : '' }}>
                                    [{{ $b->kode_barang }}] {{ $b->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Lokasi Subcon --}}
                    <div class="col-md-{{ auth()->user()->is_admin ? '3' : '3' }}">
                        <label class="form-label fw-semibold">Lokasi Subcon</label>
                        <select class="form-select select2-filter" name="lokasi_subcon_id" id="filter_lokasi_subcon_id">
                            <option value="">-- Semua Lokasi --</option>
                            @foreach ($lokasiList as $l)
                                <option value="{{ $l->id }}" {{ $selectedLokasi == $l->id ? 'selected' : '' }}>
                                    {{ $l->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-rotate-2 me-1"></i> Reset Filter
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-search me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan Statistik Laporan Sesuai Filter --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card border p-3 bg-white mb-0">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg bg-light-primary text-primary me-3">
                        <i class="ti ti-checkup-list fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-semibold">Total Unit Selesai</h6>
                        <h3 class="mb-0 fw-bold text-primary">{{ number_format($pengerjaan->sum('jumlah'), 0, ',', '.') }} <small class="fs-6 text-muted">Unit</small></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card border p-3 bg-white mb-0">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg bg-light-success text-success me-3">
                        <i class="ti ti-clipboard-list fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-semibold">Total Transaksi Pengerjaan</h6>
                        <h3 class="mb-0 fw-bold text-success">{{ number_format($pengerjaan->count(), 0, ',', '.') }} <small class="fs-6 text-muted">Data</small></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-lg-4">
            <div class="card border p-3 bg-white mb-0">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg bg-light-info text-info me-3">
                        <i class="ti ti-package fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-semibold">Jumlah Jenis Barang</h6>
                        <h3 class="mb-0 fw-bold text-info">{{ $pengerjaan->pluck('kode_barang')->unique()->count() }} <small class="fs-6 text-muted">Item</small></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Laporan & Toolbar Ekspor (Excel, PDF, Print) --}}
    <div class="card">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h5 class="mb-1 text-dark fw-bold">
                    <i class="ti ti-table me-2 text-primary"></i>Tabel Rekapitulasi Laporan
                </h5>
                <small class="text-muted">
                    Periode: <strong>{{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') : 'Awal' }}</strong> s/d <strong>{{ $tanggalAkhir ? \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') : 'Sekarang' }}</strong>
                </small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="dt-responsive table-responsive">
                <table id="laporan-table" class="table table-striped table-bordered align-middle nowrap w-100">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            @if (auth()->user()->is_admin)
                                <th>Karyawan</th>
                            @endif
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Lokasi Subcon</th>
                            <th>Jumlah Selesai</th>
                            <th>Keterangan</th>
                            <th class="no-export" style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengerjaan as $item)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                @if (auth()->user()->is_admin)
                                    <td class="text-start">{{ $item->nama_karyawan }} ({{ $item->no_karyawan }})</td>
                                @endif
                                <td><span class="badge bg-secondary">{{ $item->kode_barang }}</span></td>
                                <td class="text-start">{{ $item->nama_barang }}</td>
                                <td>{{ $item->nama_lokasi }}</td>
                                <td><strong>{{ number_format($item->jumlah, 0, ',', '.') }}</strong> Unit</td>
                                <td class="text-start">{{ $item->keterangan ?: '-' }}</td>
                                <td class="no-export">
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus-pengerjaan"
                                        data-id="{{ $item->id }}"
                                        title="Hapus Data">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold text-center">
                            <td colspan="{{ auth()->user()->is_admin ? 6 : 5 }}" class="text-end">Total Kuantitas:</td>
                            <td>{{ number_format($pengerjaan->sum('jumlah'), 0, ',', '.') }} Unit</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Select2 Filter
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Inisialisasi DataTables Laporan dengan Export Excel, PDF, Print
            if (!$.fn.DataTable.isDataTable('#laporan-table')) {
                $('#laporan-table').DataTable({
                    pageLength: 25,
                    info: true,
                    dom: '<"d-flex flex-wrap justify-content-between align-items-center mb-3"Bf>rt<"d-flex flex-wrap justify-content-between align-items-center mt-3"ip>',
                    order: [[1, 'desc']],
                    language: {
                        emptyTable: "Tidak ada data pengerjaan pada periode/filter ini",
                        zeroRecords: "Tidak ditemukan data yang sesuai",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pengerjaan",
                        infoEmpty: "Menampilkan 0 data",
                        search: "Cari di tabel:",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    },
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="ti ti-file-spreadsheet me-1"></i> Excel',
                            className: 'btn btn-success btn-sm',
                            title: 'Laporan_Pengerjaan_Barang_Subcon',
                            filename: function() {
                                let tglMulai = $('#filter_tanggal_mulai').val() || 'Awal';
                                let tglAkhir = $('#filter_tanggal_akhir').val() || 'Sekarang';
                                return 'Laporan_Subcon_' + tglMulai + '_sd_' + tglAkhir;
                            },
                            exportOptions: { columns: ':not(.no-export)' },
                            footer: true
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="ti ti-file-type-pdf me-1"></i> PDF',
                            className: 'btn btn-danger btn-sm',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            title: 'Laporan Pengerjaan Barang Subcon',
                            messageTop: function() {
                                let tglMulai = $('#filter_tanggal_mulai').val() || 'Awal';
                                let tglAkhir = $('#filter_tanggal_akhir').val() || 'Sekarang';
                                return 'Periode: ' + tglMulai + ' s/d ' + tglAkhir;
                            },
                            exportOptions: { columns: ':not(.no-export)' },
                            footer: true
                        },
                        {
                            extend: 'print',
                            text: '<i class="ti ti-printer me-1"></i> Print',
                            className: 'btn btn-secondary btn-sm',
                            title: 'Laporan Pengerjaan Barang Subcon',
                            messageTop: function() {
                                let tglMulai = $('#filter_tanggal_mulai').val() || 'Awal';
                                let tglAkhir = $('#filter_tanggal_akhir').val() || 'Sekarang';
                                return '<p style="margin-bottom:15px; font-size:14px;">Periode: <strong>' + tglMulai + '</strong> s/d <strong>' + tglAkhir + '</strong></p>';
                            },
                            exportOptions: { columns: ':not(.no-export)' },
                            footer: true
                        },
                        {
                            extend: 'colvis',
                            text: '<i class="ti ti-columns me-1"></i> Kolom',
                            className: 'btn btn-outline-secondary btn-sm',
                            columns: ':not(.d-none)'
                        }
                    ]
                });
            }
        });

        // Quick Period Setter
        function setFilterPeriod(type) {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;

            let startStr = todayStr;
            let endStr = todayStr;

            if (type === 'today') {
                startStr = todayStr;
                endStr = todayStr;
            } else if (type === 'this_month') {
                startStr = `${yyyy}-${mm}-01`;
                endStr = todayStr;
            } else if (type === 'last_month') {
                const lastMonthDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                const lmY = lastMonthDate.getFullYear();
                const lmM = String(lastMonthDate.getMonth() + 1).padStart(2, '0');
                const lmLastDay = String(lastMonthEnd.getDate()).padStart(2, '0');
                startStr = `${lmY}-${lmM}-01`;
                endStr = `${lmY}-${lmM}-${lmLastDay}`;
            } else if (type === 'all') {
                startStr = '';
                endStr = '';
            }

            document.getElementById('filter_tanggal_mulai').value = startStr;
            document.getElementById('filter_tanggal_akhir').value = endStr;
            document.getElementById('filterForm').submit();
        }

        // Hapus pengerjaan
        $(document).on('click', '.btn-hapus-pengerjaan', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data pengerjaan barang akan dihapus permanen',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('pengerjaan') }}/" + id,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                            }
                        },
                        error: function(err) {
                            let msg = 'Terjadi kesalahan koneksi.';
                            if (err.status === 403) {
                                msg = 'Anda tidak memiliki akses untuk menghapus data ini.';
                            }
                            Swal.fire('Gagal', msg, 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
