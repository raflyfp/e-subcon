@extends('layouts.mantis')
@section('title', 'Laporan Subcon')

@section('content')

    <div class="col-12">
        <div class="mb-3">
            <h3 class="fw-bold mb-1 text-dark fs-4">Laporan Subcon</h3>
            <p class="text-muted small mb-0">Laporan rekapitulasi data pengerjaan barang subcon</p>
        </div>

        <div class="row g-3 g-md-4">

        {{-- ========================================================================= --}}
        {{-- SEBELAH KIRI: PANEL FILTER LAPORAN                                        --}}
        {{-- ========================================================================= --}}
        <div class="col-lg-4 col-xl-3">
            <div class="card border shadow-sm sticky-top" style="top: 90px; z-index: 10;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 text-dark fw-bold">
                        <i class="ti ti-adjustments-horizontal text-primary me-2"></i>Filter Laporan
                    </h5>
                    <small class="text-muted">Pilih kriteria untuk menyaring data</small>
                </div>
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('laporan.index') }}" id="filterForm">
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

                        @if (!auth()->user()->is_admin)
                            <div class="mb-3 p-2 bg-light rounded border">
                                <small class="text-muted d-block">Subcon Pelaksana:</small>
                                <strong class="text-primary"><i class="ti ti-building me-1"></i>{{ $subcon->nama_lokasi ?? auth()->user()->name }}</strong>
                            </div>
                        @endif

                        {{-- Filter Karyawan --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="filter_karyawan_id">Karyawan Pelaksana</label>
                            <select class="form-select select2-filter" name="karyawan_id" id="filter_karyawan_id">
                                <option value="">-- Semua Karyawan --</option>
                                @foreach ($karyawanList as $k)
                                    <option value="{{ $k->id }}"
                                        {{ $selectedKaryawan == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_karyawan }} ({{ $k->no_karyawan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Barang --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="filter_barang_id">Barang</label>
                            <select class="form-select select2-filter" name="barang_id" id="filter_barang_id">
                                <option value="">-- Semua Barang --</option>
                                @foreach ($barangList as $b)
                                    <option value="{{ $b->id }}" {{ $selectedBarang == $b->id ? 'selected' : '' }}>
                                        [{{ $b->kode_barang }}] {{ $b->nama_barang }} ({{ $b->satuan ?? 'PCS' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Lokasi Subcon (Hanya Admin) --}}
                        @if (auth()->user()->is_admin)
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="filter_lokasi_subcon_id">Lokasi Subcon</label>
                                <select class="form-select select2-filter" name="lokasi_subcon_id" id="filter_lokasi_subcon_id">
                                    <option value="">-- Semua Lokasi --</option>
                                    @foreach ($lokasiList as $l)
                                        <option value="{{ $l->id }}" {{ $selectedLokasi == $l->id ? 'selected' : '' }}>
                                            {{ $l->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Submit Buttons --}}
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary fw-semibold py-2">
                                <i class="ti ti-filter me-1"></i> Terapkan Filter
                            </button>
                            <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm py-2">
                                <i class="ti ti-rotate-2 me-1"></i> Reset Filter
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- SEBELAH KANAN: HASIL LAPORAN (Report Sheet Document Style)                --}}
        {{-- ========================================================================= --}}
        <div class="col-lg-8 col-xl-9">

            @if (!$isFiltered)
                {{-- State Awal: Belum Filter (Kosong) --}}
                <div class="card border bg-white shadow-sm text-center py-5 px-4" style="min-height: 450px;">
                    <div class="my-auto py-4">
                        <div class="mb-3 text-primary opacity-50">
                            <i class="ti ti-file-search" style="font-size: 64px;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Laporan Belum Ditampilkan</h4>
                        <p class="text-muted fs-6 mb-4" style="max-width: 520px; margin: 0 auto;">
                            Silakan tentukan kriteria tanggal, barang, atau karyawan pada panel filter di sebelah kiri,
                            kemudian klik tombol <strong>"Terapkan Filter"</strong> untuk memuat data laporan.
                        </p>
                    </div>
                </div>
            @else
                {{-- Action Toolbar: Print, Export PDF, Export Excel --}}
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                        </div>

                        <div class="d-flex gap-2 align-items-center">
                            {{-- Tombol Print --}}
                            <button type="button" class="btn btn-outline-dark btn-sm fw-semibold"
                                onclick="printReportSheet()">
                                <i class="ti ti-printer me-1"></i> Print Laporan
                            </button>
                            {{-- Tombol PDF --}}
                            <button type="button" class="btn btn-danger btn-sm fw-semibold" onclick="exportReportPDF()">
                                <i class="ti ti-file-type-pdf me-1"></i> Export PDF
                            </button>
                            {{-- Tombol Excel --}}
                            <button type="button" class="btn btn-success btn-sm fw-semibold" onclick="exportReportExcel()">
                                <i class="ti ti-file-spreadsheet me-1"></i> Export Excel
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Frame Container Hasil Laporan dengan Scroll Internal --}}
                <div class="report-scroll-frame border rounded bg-white shadow-sm" id="reportViewerFrame"
                    style="max-height: 70vh; overflow-y: auto; overflow-x: auto; position: relative;">
                    <div class="p-3 p-md-4" id="printable-report-sheet" style="color: #000; min-width: 680px;">

                        {{-- Header Dokumen Laporan --}}
                        <div class="report-header mb-4 pb-2 border-bottom">
                            <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.02em;">PT. Sinaraya Nugraha</h4>
                            <h5 class="fw-bold mb-1 text-dark">Laporan Pengerjaan Barang Subcon</h5>
                            <div class="fw-bold text-dark fs-6 mb-2">
                                Periode :
                                {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->format('Y-m-d') : 'Awal' }}
                                s/d
                                {{ $tanggalAkhir ? \Carbon\Carbon::parse($tanggalAkhir)->format('Y-m-d') : 'Sekarang' }}
                            </div>
                            <div class="small fw-semibold text-secondary pt-1">
                                <span><strong>Barang :</strong>
                                    {{ $selectedBarangObj ? '[' . $selectedBarangObj->kode_barang . '] ' . $selectedBarangObj->nama_barang . ' (' . ($selectedBarangObj->satuan ?? 'PCS') . ')' : 'SEMUA BARANG' }}</span>
                                <span class="mx-2">|</span>
                                <span><strong>Lokasi :</strong>
                                    {{ $selectedLokasiObj ? $selectedLokasiObj->nama_lokasi : 'SEMUA LOKASI' }}</span>
                                @if (auth()->user()->is_admin)
                                    <span class="mx-2">|</span>
                                    <span><strong>Karyawan :</strong>
                                        {{ $selectedKaryawanObj ? $selectedKaryawanObj->nama_karyawan . ' (' . $selectedKaryawanObj->no_karyawan . ')' : 'SEMUA KARYAWAN' }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Tabel-Tabel Terpisah Per Kode Barang --}}
                        @php
                            $groupedPengerjaan = $pengerjaan->groupBy('kode_barang');
                        @endphp

                        @forelse ($groupedPengerjaan as $kodeBarang => $items)
                            @php
                                $firstItem = $items->first();
                                $satuan = $firstItem->satuan ?? 'PCS';
                                $subtotal = $items->sum('jumlah');
                            @endphp

                            <div class="barang-report-block mb-4" style="page-break-inside: avoid;">
                                {{-- Header Tabel Per Barang --}}
                                <div class="px-3 py-2 border rounded-top"
                                    style="background-color: #e0f2fe; color: #0369a1; font-weight: 700; border-color: #94a3b8 !important;">
                                    <i class="ti ti-package me-1"></i>
                                    <span class="fs-6">[{{ $kodeBarang }}] {{ $firstItem->nama_barang }}</span>
                                </div>

                                {{-- Tabel Rincian Data untuk Barang Ini --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle w-100 report-table mb-0"
                                        style="border-color: #94a3b8; font-size: 0.92rem; border-top: 0;">
                                        <thead>
                                            <tr class="text-center"
                                                style="background-color: #f8fafc; color: #0f172a; font-weight: 600; border-color: #94a3b8;">
                                                <th style="width: 45px; border: 1px solid #94a3b8;">No</th>
                                                <th style="width: 110px; border: 1px solid #94a3b8;">Tanggal</th>
                                                <th style="border: 1px solid #94a3b8;">Karyawan</th>
                                                <th style="border: 1px solid #94a3b8;">Lokasi Subcon</th>
                                                <th style="width: 130px; border: 1px solid #94a3b8;">Jenis Pekerjaan</th>
                                                <th style="width: 130px; border: 1px solid #94a3b8;">Jumlah Selesai</th>
                                                <th style="width: 80px; border: 1px solid #94a3b8;">Satuan</th>
                                                <th style="border: 1px solid #94a3b8;">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $item)
                                                <tr class="text-center" style="border: 1px solid #cbd5e1;">
                                                    <td style="border: 1px solid #cbd5e1;">{{ $loop->iteration }}</td>
                                                    <td style="border: 1px solid #cbd5e1;">
                                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                                    <td class="text-start" style="border: 1px solid #cbd5e1;">
                                                        {{ $item->nama_karyawan }} ({{ $item->no_karyawan }})</td>
                                                    <td style="border: 1px solid #cbd5e1;">{{ $item->nama_lokasi }}</td>
                                                    <td class="text-center" style="border: 1px solid #cbd5e1;">
                                                        {{ $item->jenis_pekerjaan ?: '-' }}
                                                    </td>
                                                    <td class="text-end fw-bold text-dark pe-3"
                                                        style="border: 1px solid #cbd5e1;">
                                                        {{ number_format($item->jumlah, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-center" style="border: 1px solid #cbd5e1;">
                                                        {{ $item->satuan ?? $satuan }}
                                                    </td>
                                                    <td class="text-start" style="border: 1px solid #cbd5e1;">
                                                        {{ $item->keterangan ?: '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="fw-bold"
                                                style="background-color: #f1f5f9; border: 1px solid #94a3b8;">
                                                <td colspan="5" class="text-end pe-3">
                                                    Total :</td>
                                                <td class="text-end fw-bold text-primary pe-3">
                                                    {{ number_format($subtotal, 0, ',', '.') }}</td>
                                                <td class="text-center fw-bold text-primary">
                                                    {{ $satuan }}</td>
                                                <td style="border: 1px solid #94a3b8;"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted border rounded p-4 bg-light">
                                <i class="ti ti-alert-circle fs-2 d-block mb-2 text-secondary"></i>
                                Tidak ada data pengerjaan barang pada periode / filter ini.
                            </div>
                        @endforelse

                        {{-- Footer Lembar Laporan --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 text-muted small"
                            style="border-top: 1px dashed #cbd5e1;">
                            <div>
                                Dicetak oleh: <strong>{{ auth()->user()->name }}</strong>
                            </div>
                            <div>
                                Waktu cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
                            </div>
                        </div>

                    </div>
                </div>
            @endif

        </div>

    </div>
    </div>

    {{-- Styling Scroll Frame & Print Media --}}
    <style>
        .report-scroll-frame::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .report-scroll-frame::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .report-scroll-frame::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .report-scroll-frame::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }

            .pc-sidebar,
            .pc-header,
            .top-navbar,
            .page-header,
            .sticky-top,
            .card-header,
            .btn,
            .d-flex.justify-content-between.align-items-center.mb-3 {
                display: none !important;
            }

            .col-lg-4,
            .col-xl-3 {
                display: none !important;
            }

            .col-lg-8,
            .col-xl-9 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }

            .card,
            .report-scroll-frame {
                border: none !important;
                box-shadow: none !important;
                max-height: none !important;
                overflow: visible !important;
                padding: 0 !important;
            }

            #printable-report-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                min-width: auto !important;
            }

            .barang-report-block {
                page-break-inside: avoid !important;
                margin-bottom: 20px !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th,
            td {
                border: 1px solid #000000 !important;
                padding: 6px 8px !important;
                color: #000000 !important;
            }
        }
    </style>

@endsection

@push('scripts')
    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- html2pdf.js for exact PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Select2 Filter
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });

        // Print Laporan Sheet
        function printReportSheet() {
            window.print();
        }

        // Export PDF Laporan (Exact Document PDF)
        function exportReportPDF() {
            const element = document.getElementById('printable-report-sheet');
            const tglMulai = document.getElementById('filter_tanggal_mulai').value || 'Awal';
            const tglAkhir = document.getElementById('filter_tanggal_akhir').value || 'Sekarang';
            const filename = 'Laporan_Subcon_' + tglMulai + '_sd_' + tglAkhir + '.pdf';

            const opt = {
                margin: [10, 10, 10, 10],
                filename: filename,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'landscape'
                }
            };

            Swal.fire({
                title: 'Sedang Membuat PDF...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            html2pdf().set(opt).from(element).save().then(() => {
                Swal.close();
            }).catch(err => {
                Swal.fire('Gagal', 'Terjadi kesalahan saat mengekspor PDF.', 'error');
            });
        }

        // Export Excel Laporan (Structured HTML Tables per Barang with Header & Subheaders)
        function exportReportExcel() {
            const tglMulai = document.getElementById('filter_tanggal_mulai').value || 'Awal';
            const tglAkhir = document.getElementById('filter_tanggal_akhir').value || 'Sekarang';
            const filename = 'Laporan_Subcon_' + tglMulai + '_sd_' + tglAkhir + '.xls';

            let reportHeaderHtml = '<table border="0">' +
                '<tr><td colspan="8" style="font-size:16px; font-weight:bold;">e-Subcon \u2014 PT. Sinaraya Nugraha<\/td><\/tr>' +
                '<tr><td colspan="8" style="font-size:14px; font-weight:bold;">Laporan Pengerjaan Barang Subcon<\/td><\/tr>' +
                '<tr><td colspan="8" style="font-size:12px; font-weight:bold;">Periode: ' + tglMulai + ' s/d ' + tglAkhir + '<\/td><\/tr>' +
                '<tr><td colspan="8"><\/td><\/tr>' +
                '<\/table>';

            let tablesHtml = '';
            const blocks = document.querySelectorAll('.barang-report-block');
            blocks.forEach(block => {
                const headerText = block.querySelector('.rounded-top')?.innerText.trim() || '';
                const table = block.querySelector('table');
                if (table) {
                    tablesHtml += '<table border="0">' +
                        '<tr><td colspan="8" style="background-color:#e0f2fe; font-size:13px; font-weight:bold; color:#0369a1; border:0.5pt solid #94a3b8;">' +
                        headerText + '<\/td><\/tr>' +
                        '<\/table>' +
                        table.outerHTML + '<br/>';
                }
            });

            const fullExcelHtml = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' +
                '<head>' +
                '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"/>' +
                '<style>' +
                'table { border-collapse: collapse; margin-bottom: 15px; }' +
                'th, td { border: 0.5pt solid #000000; padding: 5px; }' +
                'th { background-color: #f8fafc; font-weight: bold; }' +
                '<\/style>' +
                '<\/head>' +
                '<body>' +
                reportHeaderHtml +
                tablesHtml +
                '<\/body>' +
                '<\/html>';

            const blob = new Blob([fullExcelHtml], {
                type: 'application/vnd.ms-excel;charset=utf-8'
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endpush
