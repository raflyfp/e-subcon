@extends('layouts.mantis')
@section('title', 'Log Report')

@section('content')

    <div class="col-12">
        <div class="mb-3">
            <h3 class="fw-bold mb-1 text-dark fs-4">Log Report</h3>
            <p class="text-muted small mb-0">Rekam jejak seluruh aktivitas pengguna, perubahan data master, dan transaksi sistem</p>
        </div>

        <div class="row g-3 g-md-4">

            {{-- ========================================================================= --}}
            {{-- PANEL FILTER LOG REPORT                                                  --}}
            {{-- ========================================================================= --}}
            <div class="col-lg-4 col-xl-3">
                <div class="card border shadow-sm sticky-top" style="top: 90px; z-index: 10;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 text-dark fw-bold">
                            <i class="ti ti-filter text-primary me-2"></i>Filter Log
                        </h5>
                        <small class="text-muted">Saring riwayat aktivitas</small>
                    </div>
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('log-report.index') }}" id="filterLogForm">
                            <input type="hidden" name="filter" value="1">

                            {{-- Tanggal Mulai --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="filter_tanggal_mulai">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai" id="filter_tanggal_mulai"
                                    value="{{ $tanggalMulai }}">
                            </div>

                            {{-- Tanggal Akhir --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="filter_tanggal_akhir">Tanggal Akhir</label>
                                <input type="date" class="form-control" name="tanggal_akhir" id="filter_tanggal_akhir"
                                    value="{{ $tanggalAkhir }}">
                            </div>

                            <hr class="text-secondary opacity-25">

                            {{-- Filter Modul --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="filter_module">Modul / Menu</label>
                                <select class="form-select" name="module" id="filter_module">
                                    <option value="">-- Semua Modul --</option>
                                    @foreach ($moduleList as $mod)
                                        <option value="{{ $mod }}" {{ $selectedModule == $mod ? 'selected' : '' }}>
                                            {{ $mod }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Aksi --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="filter_action">Jenis Aksi</label>
                                <select class="form-select" name="action" id="filter_action">
                                    <option value="">-- Semua Aksi --</option>
                                    @foreach ($actionList as $act)
                                        <option value="{{ $act }}" {{ $selectedAction == $act ? 'selected' : '' }}>
                                            {{ $act }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter User --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="filter_user_id">Pengguna (User)</label>
                                <select class="form-select" name="user_id" id="filter_user_id">
                                    <option value="">-- Semua Pengguna --</option>
                                    @foreach ($userList as $u)
                                        <option value="{{ $u->id }}" {{ $selectedUser == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }} ({{ $u->username }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-grid gap-2 pt-2">
                                <button type="submit" class="btn btn-primary fw-semibold py-2">
                                    <i class="ti ti-search me-1"></i> Terapkan Filter
                                </button>
                                <a href="{{ route('log-report.index') }}" class="btn btn-outline-secondary btn-sm py-1">
                                    <i class="ti ti-rotate-2 me-1"></i> Reset Filter
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- TABEL DAFTAR LOG AKTIVITAS                                                --}}
            {{-- ========================================================================= --}}
            <div class="col-lg-8 col-xl-9">
                <div class="card border shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0 text-dark fw-bold">
                                <i class="ti ti-history text-primary me-2"></i>Riwayat Aktivitas Sistem
                            </h5>
                            @if ($isFiltered)
                                <small class="text-muted">Total <strong>{{ count($logs) }}</strong> catatan aktivitas ditemukan</small>
                            @else
                                <small class="text-muted">Silakan klik tombol <strong>Terapkan Filter</strong> untuk menampilkan data</small>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0" id="tableLogReport" style="font-size: 0.88rem;">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th style="width: 140px;">Waktu Kejadian</th>
                                        <th style="width: 130px;">Pengguna</th>
                                        <th style="width: 90px;">Aksi</th>
                                        <th style="width: 130px;">Modul</th>
                                        <th>Detail Deskripsi Perubahan</th>
                                        <th style="width: 100px;">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!$isFiltered)
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="ti ti-filter-check fs-1 d-block mb-2 text-primary opacity-50"></i>
                                                <h6 class="fw-bold text-dark">Data Belum Dimuat</h6>
                                                <p class="small text-muted mb-0">Silakan atur kriteria filter di sebelah kiri dan klik tombol <strong>Terapkan Filter</strong> untuk menampilkan riwayat log aktivitas.</p>
                                            </td>
                                        </tr>
                                    @else
                                        @forelse ($logs as $index => $log)
                                            <tr>
                                                <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                                <td class="text-center">
                                                    <span class="d-block fw-semibold text-dark">{{ $log->created_at->format('d/m/Y') }}</span>
                                                    <small class="text-muted font-monospace">{{ $log->created_at->format('H:i:s') }}</small>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-dark d-block">{{ $log->user_name }}</span>
                                                    <span class="badge bg-light text-secondary border px-1" style="font-size: 0.72rem;">{{ $log->role }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if ($log->action === 'CREATE')
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">CREATE</span>
                                                    @elseif ($log->action === 'UPDATE')
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">UPDATE</span>
                                                    @elseif ($log->action === 'DELETE')
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">DELETE</span>
                                                    @elseif ($log->action === 'TOGGLE_STATUS')
                                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">STATUS</span>
                                                    @elseif ($log->action === 'LOGIN')
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">LOGIN</span>
                                                    @elseif ($log->action === 'LOGOUT')
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">LOGOUT</span>
                                                    @elseif ($log->action === 'LOGIN_FAILED')
                                                        <span class="badge bg-danger text-white px-2 py-1"><i class="ti ti-alert-triangle me-1"></i>GAGAL LOGIN</span>
                                                    @elseif ($log->action === 'VALIDATION_FAILED')
                                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="ti ti-alert-circle me-1"></i>VALIDASI GAGAL</span>
                                                    @elseif ($log->action === 'ACCESS_DENIED')
                                                        <span class="badge bg-danger text-white px-2 py-1"><i class="ti ti-shield-x me-1"></i>AKSES DITOLAK</span>
                                                    @else
                                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $log->action }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-medium text-dark">{{ $log->module }}</span>
                                                </td>
                                                <td class="text-break">
                                                    {{ $log->description }}
                                                </td>
                                                <td class="text-center font-monospace text-muted" style="font-size: 0.8rem;">
                                                    {{ $log->ip_address ?: '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5 text-muted">
                                                    <i class="ti ti-file-search fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                                    <h6 class="fw-bold text-dark">Tidak Ada Data Ditemukan</h6>
                                                    <p class="small text-muted mb-0">Tidak ada rekaman aktivitas untuk rentang tanggal atau filter yang dipilih.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    @if ($isFiltered && count($logs) > 0)
        <script>
            $(document).ready(function() {
                $('#tableLogReport').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                    },
                    dom: "<'row mb-3'<'col-md-6 d-flex align-items-center'B><'col-md-6 d-flex justify-content-end'f>>" +
                         "<'row'<'col-sm-12'tr>>" +
                         "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="ti ti-file-spreadsheet me-1"></i> Export Excel',
                            className: 'btn btn-success btn-sm me-2',
                            title: 'Log_Report_' + '{{ $tanggalMulai }}_sampai_{{ $tanggalAkhir }}'
                        },
                        {
                            extend: 'print',
                            text: '<i class="ti ti-printer me-1"></i> Print Log',
                            className: 'btn btn-outline-dark btn-sm',
                            title: 'Log Report ({{ $tanggalMulai }} s/d {{ $tanggalAkhir }})'
                        }
                    ],
                    order: [[1, 'desc']],
                    pageLength: 25,
                    responsive: true
                });
            });
        </script>
    @endif
@endpush
