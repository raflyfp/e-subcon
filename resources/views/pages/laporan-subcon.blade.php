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
                                <label class="form-label fw-semibold" for="filter_tanggal_mulai" style="font-size: 0.95rem;">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai" id="filter_tanggal_mulai"
                                    style="font-size: 0.95rem; min-height: 42px;" value="{{ $tanggalMulai }}">
                            </div>

                            {{-- Tanggal Akhir --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="filter_tanggal_akhir" style="font-size: 0.95rem;">Tanggal Akhir</label>
                                <input type="date" class="form-control" name="tanggal_akhir" id="filter_tanggal_akhir"
                                    style="font-size: 0.95rem; min-height: 42px;" value="{{ $tanggalAkhir }}">
                            </div>

                            <hr class="text-secondary opacity-25">

                            @if (!auth()->user()->is_admin)
                                <div class="mb-3 p-2 bg-light rounded border">
                                    <small class="text-muted d-block" style="font-size: 0.88rem;">Subcon Pelaksana:</small>
                                    <strong class="text-primary" style="font-size: 1rem;"><i
                                            class="ti ti-building me-1"></i>{{ $subcon->nama_lokasi ?? auth()->user()->name }}</strong>
                                </div>
                            @endif

                            {{-- Filter Lokasi Subcon (Hanya Admin - Compact Multi-Select) --}}
                            @if (auth()->user()->is_admin)
                                <div class="custom-multiselect-wrapper mb-3" id="ms_lokasi">
                                    <label class="form-label fw-semibold mb-1" style="font-size: 0.95rem;">
                                        <i class="ti ti-building me-1 text-primary"></i>Lokasi Subcon
                                    </label>
                                    <div class="dropdown custom-multiselect">
                                        <button class="form-select text-start d-flex justify-content-between align-items-center multiselect-trigger bg-white shadow-none" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                            <span class="multiselect-label text-truncate me-2 text-muted">-- Semua Lokasi (Multi-Select) --</span>
                                            <span class="badge bg-primary rounded-pill multiselect-count d-none" style="font-size: 0.85rem;">0</span>
                                        </button>
                                        <div class="dropdown-menu p-2 shadow-sm w-100 custom-multiselect-dropdown" style="min-width: 300px; max-width: 380px; z-index: 1050;">
                                            <div class="mb-2 d-flex gap-1 align-items-center">
                                                <div class="position-relative flex-grow-1">
                                                    <input type="text" class="form-control multiselect-search ps-4" style="font-size: 0.9rem;" placeholder="Cari lokasi subcon...">
                                                    <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size: 15px;"></i>
                                                </div>
                                                <button type="button" class="btn btn-outline-secondary btn-sm multiselect-undo-btn px-2 d-flex align-items-center justify-content-center" title="Reset / Batalkan Pilihan Lokasi" style="height: 38px; min-width: 38px; border-color: #cbd5e1;">
                                                    <i class="ti ti-rotate-2 text-danger" style="font-size: 16px;"></i>
                                                </button>
                                            </div>
                                            <div class="multiselect-options-list overflow-auto" style="max-height: 230px;">
                                                @foreach ($lokasiList as $l)
                                                    <label class="dropdown-item d-flex align-items-center justify-content-between py-2 px-2 rounded multiselect-option-label" style="cursor: pointer;">
                                                        <div class="d-flex align-items-start gap-2 w-100">
                                                            <input class="form-check-input mt-1 multiselect-checkbox" type="checkbox" name="lokasi_subcon_id[]" value="{{ $l->id }}"
                                                                {{ in_array($l->id, (array) $selectedLokasi) ? 'checked' : '' }}
                                                                data-label="{{ $l->nama_lokasi }}">
                                                            <span style="font-size: 0.88rem; line-height: 1.35; white-space: normal; word-break: break-word;">{{ $l->nama_lokasi }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <div class="pt-2 mt-2 border-top d-flex justify-content-end">
                                                <button type="button" class="btn btn-primary btn-sm px-3 py-1 fw-semibold multiselect-ok-btn" style="font-size: 0.9rem;">
                                                    <i class="ti ti-check me-1"></i>OK
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Filter Karyawan (Compact Multi-Select Menyesuaikan Lokasi Subcon) --}}
                            <div class="custom-multiselect-wrapper mb-3" id="ms_karyawan">
                                <label class="form-label fw-semibold mb-1" style="font-size: 0.95rem;">
                                    <i class="ti ti-users me-1 text-primary"></i>Karyawan Pelaksana
                                </label>
                                <div class="dropdown custom-multiselect">
                                    <button class="form-select text-start d-flex justify-content-between align-items-center multiselect-trigger bg-white shadow-none" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <span class="multiselect-label text-truncate me-2 text-muted">-- Semua Karyawan (Multi-Select) --</span>
                                        <span class="badge bg-primary rounded-pill multiselect-count d-none" style="font-size: 0.82rem;">0</span>
                                    </button>
                                    <div class="dropdown-menu p-2 shadow-sm w-100 custom-multiselect-dropdown" style="min-width: 320px; max-width: 400px; z-index: 1050;">
                                        <div class="mb-2 d-flex gap-1 align-items-center">
                                            <div class="position-relative flex-grow-1">
                                                <input type="text" class="form-control multiselect-search ps-4" style="font-size: 0.9rem;" placeholder="Cari nama / no karyawan...">
                                                <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size: 15px;"></i>
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary btn-sm multiselect-undo-btn px-2 d-flex align-items-center justify-content-center" title="Reset / Batalkan Pilihan Karyawan" style="height: 38px; min-width: 38px; border-color: #cbd5e1;">
                                                <i class="ti ti-rotate-2 text-danger" style="font-size: 16px;"></i>
                                            </button>
                                        </div>
                                        <div class="multiselect-options-list overflow-auto" style="max-height: 230px;">
                                            @foreach ($karyawanList as $k)
                                                <label class="dropdown-item d-flex align-items-center justify-content-between py-2 px-2 rounded multiselect-option-label" data-lokasi="{{ $k->lokasi_subcon_id }}" style="cursor: pointer;">
                                                    <div class="d-flex align-items-start gap-2 w-100">
                                                        <input class="form-check-input mt-1 multiselect-checkbox" type="checkbox" name="karyawan_id[]" value="{{ $k->id }}"
                                                            {{ in_array($k->id, (array) $selectedKaryawan) ? 'checked' : '' }}
                                                            data-label="{{ $k->nama_karyawan }} ({{ $k->no_karyawan }})">
                                                        <span style="font-size: 0.88rem; line-height: 1.35; white-space: normal; word-break: break-word;">{{ $k->nama_karyawan }} <span class="text-muted">({{ $k->no_karyawan }})</span></span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="pt-2 mt-2 border-top d-flex justify-content-end">
                                            <button type="button" class="btn btn-primary btn-sm px-3 py-1 fw-semibold multiselect-ok-btn" style="font-size: 0.9rem;">
                                                <i class="ti ti-check me-1"></i>OK
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Filter Barang (Compact Multi-Select Menyesuaikan Lokasi Subcon) --}}
                            <div class="custom-multiselect-wrapper mb-3" id="ms_barang">
                                <label class="form-label fw-semibold mb-1" style="font-size: 0.95rem;">
                                    <i class="ti ti-package me-1 text-primary"></i>Barang
                                </label>
                                <div class="dropdown custom-multiselect">
                                    <button class="form-select text-start d-flex justify-content-between align-items-center multiselect-trigger bg-white shadow-none" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <span class="multiselect-label text-truncate me-2 text-muted">-- Semua Barang (Multi-Select) --</span>
                                        <span class="badge bg-primary rounded-pill multiselect-count d-none" style="font-size: 0.82rem;">0</span>
                                    </button>
                                    <div class="dropdown-menu p-2 shadow-sm w-100 custom-multiselect-dropdown" style="min-width: 340px; max-width: 440px; z-index: 1050;">
                                        <div class="mb-2 d-flex gap-1 align-items-center">
                                            <div class="position-relative flex-grow-1">
                                                <input type="text" class="form-control multiselect-search ps-4" style="font-size: 0.9rem;" placeholder="Cari kode / nama barang...">
                                                <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size: 15px;"></i>
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary btn-sm multiselect-undo-btn px-2 d-flex align-items-center justify-content-center" title="Reset / Batalkan Pilihan Barang" style="height: 38px; min-width: 38px; border-color: #cbd5e1;">
                                                <i class="ti ti-rotate-2 text-danger" style="font-size: 16px;"></i>
                                            </button>
                                        </div>
                                        <div class="multiselect-options-list overflow-auto" style="max-height: 240px;">
                                            @foreach ($barangList as $b)
                                                <label class="dropdown-item d-flex align-items-center justify-content-between py-2 px-2 rounded multiselect-option-label" data-lokasi="{{ $b->lokasi_subcon_id }}" style="cursor: pointer;">
                                                    <div class="d-flex align-items-start gap-2 w-100">
                                                        <input class="form-check-input mt-1 multiselect-checkbox" type="checkbox" name="barang_id[]" value="{{ $b->id }}"
                                                            {{ in_array($b->id, (array) $selectedBarang) ? 'checked' : '' }}
                                                            data-label="[{{ $b->kode_barang }}] {{ $b->nama_barang }}">
                                                        <span style="font-size: 0.875rem; line-height: 1.35; white-space: normal; word-break: break-word;"><strong class="text-primary">[{{ $b->kode_barang }}]</strong> {{ $b->nama_barang }} <span class="text-muted">({{ $b->satuan ?? 'PCS' }})</span></span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="pt-2 mt-2 border-top d-flex justify-content-end">
                                            <button type="button" class="btn btn-primary btn-sm px-3 py-1 fw-semibold multiselect-ok-btn" style="font-size: 0.9rem;">
                                                <i class="ti ti-check me-1"></i>OK
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sort By Laporan --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="filter_group_by" style="font-size: 0.95rem;">
                                    <i class="ti ti-sort-ascending me-1 text-primary"></i>Sort By
                                </label>
                                <select class="form-select" name="group_by" id="filter_group_by" style="font-size: 0.95rem; min-height: 42px;">
                                    <option value="barang" {{ ($groupBy ?? 'barang') === 'barang' ? 'selected' : '' }}>
                                        Per Kode / Nama Barang
                                    </option>
                                    <option value="karyawan" {{ ($groupBy ?? '') === 'karyawan' ? 'selected' : '' }}>
                                        Per Karyawan Pelaksana
                                    </option>
                                    @if (auth()->user()->is_admin)
                                        <option value="subcon" {{ ($groupBy ?? '') === 'subcon' ? 'selected' : '' }}>
                                            Per Lokasi Subcon
                                        </option>
                                    @endif
                                </select>
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary fw-semibold py-2 fs-6" style="font-size: 1rem !important;">
                                    <i class="ti ti-filter me-1"></i> Terapkan Filter
                                </button>
                                <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm py-2 fs-6" style="font-size: 0.95rem !important;">
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
                                <span class="badge bg-primary-subtle text-primary border px-3 py-2 fs-6">
                                    <i class="ti ti-sort-ascending me-1"></i> Sort By:
                                    <strong>
                                        @if (($groupBy ?? 'barang') === 'karyawan')
                                            Per Karyawan
                                        @elseif (($groupBy ?? 'barang') === 'subcon')
                                            Per Lokasi Subcon
                                        @else
                                            Per Kode Barang
                                        @endif
                                    </strong>
                                </span>
                            </div>

                            <div class="d-flex gap-2 align-items-center">
                                @if (auth()->user()->canAccess('laporan_subcon'))
                                    {{-- Tombol Print --}}
                                    <button type="button" class="btn btn-outline-dark btn-sm fw-semibold"
                                        onclick="printReportSheet()">
                                        <i class="ti ti-printer me-1"></i> Print Laporan
                                    </button>
                                    {{-- Tombol PDF --}}
                                    <button type="button" class="btn btn-danger btn-sm fw-semibold"
                                        onclick="exportReportPDF()">
                                        <i class="ti ti-file-type-pdf me-1"></i> Export PDF
                                    </button>
                                    {{-- Tombol Excel --}}
                                    <button type="button" class="btn btn-success btn-sm fw-semibold"
                                        onclick="exportReportExcel()">
                                        <i class="ti ti-file-spreadsheet me-1"></i> Export Excel
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Frame Container Hasil Laporan dengan Scroll Internal --}}
                    <div class="report-scroll-frame border rounded bg-white shadow-sm" id="reportViewerFrame"
                        style="max-height: 70vh; overflow-y: auto; overflow-x: auto; position: relative;">
                        <div class="p-3 p-md-4" id="printable-report-sheet" style="color: #000; min-width: 680px;">

                            {{-- Header Dokumen Laporan --}}
                            <div class="report-header mb-4 pb-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.02em;">PT. SNA MEDIKA
                                        </h4>
                                        <h5 class="fw-bold mb-1 text-dark">Laporan Pengerjaan Barang Subcon</h5>
                                        <div class="fw-bold text-dark fs-6 mb-2">
                                            Periode :
                                            {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->format('Y-m-d') : 'Awal' }}
                                            s/d
                                            {{ $tanggalAkhir ? \Carbon\Carbon::parse($tanggalAkhir)->format('Y-m-d') : 'Sekarang' }}
                                        </div>
                                        <div class="small fw-semibold text-secondary pt-1">
                                            <span>
                                                <strong>Barang :</strong>
                                                @if (isset($selectedBarangObjs) && $selectedBarangObjs->count() === 1)
                                                    [{{ $selectedBarangObjs[0]->kode_barang }}]
                                                    {{ $selectedBarangObjs[0]->nama_barang }}
                                                    ({{ $selectedBarangObjs[0]->satuan ?? 'PCS' }})
                                                @elseif (isset($selectedBarangObjs) && $selectedBarangObjs->count() > 1)
                                                    {{ $selectedBarangObjs->count() }} Barang Dipilih
                                                    ({{ $selectedBarangObjs->pluck('nama_barang')->take(3)->implode(', ') }}{{ $selectedBarangObjs->count() > 3 ? '...' : '' }})
                                                @else
                                                    SEMUA BARANG
                                                @endif
                                            </span>
                                            <span class="mx-2">|</span>
                                            <span>
                                                <strong>Lokasi :</strong>
                                                @if (isset($selectedLokasiObjs) && $selectedLokasiObjs->count() === 1)
                                                    {{ $selectedLokasiObjs[0]->nama_lokasi }}
                                                @elseif (isset($selectedLokasiObjs) && $selectedLokasiObjs->count() > 1)
                                                    {{ $selectedLokasiObjs->count() }} Lokasi Dipilih
                                                    ({{ $selectedLokasiObjs->pluck('nama_lokasi')->take(3)->implode(', ') }}{{ $selectedLokasiObjs->count() > 3 ? '...' : '' }})
                                                @else
                                                    {{ !auth()->user()->is_admin && $subcon ? $subcon->nama_lokasi : 'SEMUA LOKASI' }}
                                                @endif
                                            </span>
                                            @if (auth()->user()->is_admin)
                                                <span class="mx-2">|</span>
                                                <span>
                                                    <strong>Karyawan :</strong>
                                                    @if (isset($selectedKaryawanObjs) && $selectedKaryawanObjs->count() === 1)
                                                        {{ $selectedKaryawanObjs[0]->nama_karyawan }}
                                                        ({{ $selectedKaryawanObjs[0]->no_karyawan }})
                                                    @elseif (isset($selectedKaryawanObjs) && $selectedKaryawanObjs->count() > 1)
                                                        {{ $selectedKaryawanObjs->count() }} Karyawan Dipilih
                                                        ({{ $selectedKaryawanObjs->pluck('nama_karyawan')->take(3)->implode(', ') }}{{ $selectedKaryawanObjs->count() > 3 ? '...' : '' }})
                                                    @else
                                                        SEMUA KARYAWAN
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <img src="{{ asset('logo.png') }}" alt="Logo SNA"
                                            style="max-height: 65px; max-width: 180px; object-fit: contain;">
                                    </div>
                                </div>
                            </div>

                            {{-- Tabel-Tabel Pengelompokan Data --}}
                            @php
                                $currentGroupBy = $groupBy ?? 'barang';
                                if ($currentGroupBy === 'karyawan') {
                                    $groupedPengerjaan = $pengerjaan->groupBy(function ($item) {
                                        return $item->nama_karyawan . ' (' . $item->no_karyawan . ')';
                                    });
                                } elseif ($currentGroupBy === 'subcon') {
                                    $groupedPengerjaan = $pengerjaan->groupBy('nama_lokasi');
                                } else {
                                    $groupedPengerjaan = $pengerjaan->groupBy('kode_barang');
                                }
                            @endphp

                            @forelse ($groupedPengerjaan as $groupKey => $items)
                                @php
                                    $firstItem = $items->first();
                                    $subtotal = $items->sum('jumlah');
                                    $totalDurasiMenit = $items->sum('durasi_menit');

                                    $totJam = intdiv($totalDurasiMenit, 60);
                                    $totMnt = $totalDurasiMenit % 60;
                                    if ($totJam > 0 && $totMnt > 0) {
                                        $durasiTotalText = "{$totJam} Jam {$totMnt} Mnt";
                                    } elseif ($totJam > 0) {
                                        $durasiTotalText = "{$totJam} Jam";
                                    } elseif ($totMnt > 0) {
                                        $durasiTotalText = "{$totMnt} Menit";
                                    } else {
                                        $durasiTotalText = '-';
                                    }
                                @endphp

                                <div class="barang-report-block mb-4" style="page-break-inside: avoid;">
                                    {{-- Header Tabel Per Kelompok --}}
                                    <div class="px-3 py-2 border rounded-top d-flex justify-content-between align-items-center"
                                        style="background-color: #e0f2fe; color: #0369a1; font-weight: 700; border-color: #94a3b8 !important;">
                                        <div>
                                            @if ($currentGroupBy === 'karyawan')
                                                <i class="ti ti-user me-1"></i>
                                                <span class="fs-6">{{ $groupKey }}</span>
                                                <span class="text-muted small fw-normal ms-2">(Lokasi:
                                                    {{ $firstItem->nama_lokasi }})</span>
                                            @elseif ($currentGroupBy === 'subcon')
                                                <i class="ti ti-building me-1"></i>
                                                <span class="fs-6">Lokasi Subcon: {{ $groupKey }}</span>
                                            @else
                                                <i class="ti ti-package me-1"></i>
                                                <span class="fs-6">[{{ $groupKey }}]
                                                    {{ $firstItem->nama_barang }}</span>
                                            @endif
                                        </div>
                                        {{-- <div>
                                        <small class="text-primary fw-semibold">{{ count($items) }} Catatan Pengerjaan</small>
                                    </div> --}}
                                    </div>

                                    {{-- Tabel Rincian Data --}}
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle w-100 report-table mb-0"
                                            style="border-color: #94a3b8; font-size: 0.92rem; border-top: 0;">
                                            <thead>
                                                <tr class="text-center"
                                                    style="background-color: #f8fafc; color: #0f172a; font-weight: 600; border-color: #94a3b8;">
                                                    <th style="width: 35px; border: 1px solid #94a3b8;">No</th>
                                                    <th style="width: 90px; border: 1px solid #94a3b8;">Tanggal</th>
                                                    <th style="width: 125px; border: 1px solid #94a3b8;">Jam Kerja</th>
                                                    <th style="width: 105px; border: 1px solid #94a3b8;">Durasi</th>
                                                    @if ($currentGroupBy !== 'karyawan')
                                                        <th style="border: 1px solid #94a3b8;">Karyawan</th>
                                                    @endif
                                                    @if ($currentGroupBy === 'karyawan' || $currentGroupBy === 'subcon')
                                                        <th style="border: 1px solid #94a3b8;">Barang yang Dikerjakan</th>
                                                    @endif
                                                    @if ($currentGroupBy !== 'subcon')
                                                        <th style="border: 1px solid #94a3b8;">Lokasi Subcon</th>
                                                    @endif
                                                    <th style="width: 110px; border: 1px solid #94a3b8;">Jenis Pekerjaan
                                                    </th>
                                                    <th style="width: 105px; border: 1px solid #94a3b8;">Jumlah Selesai
                                                    </th>
                                                    <th style="width: 65px; border: 1px solid #94a3b8;">Satuan</th>
                                                    <th style="border: 1px solid #94a3b8;">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($items as $item)
                                                    <tr class="text-center" style="border: 1px solid #cbd5e1;">
                                                        <td style="border: 1px solid #cbd5e1;">{{ $loop->iteration }}</td>
                                                        <td style="border: 1px solid #cbd5e1;">
                                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                                        </td>
                                                        <td class="text-center"
                                                            style="border: 1px solid #cbd5e1; font-size: 0.88rem;">
                                                            @if ($item->jam_mulai && $item->jam_selesai)
                                                                <span
                                                                    class="fw-semibold text-dark">{{ substr($item->jam_mulai, 0, 5) }}
                                                                    - {{ substr($item->jam_selesai, 0, 5) }}</span>
                                                                <span class="text-muted small">WIB</span>
                                                            @elseif ($item->jam_mulai)
                                                                <span
                                                                    class="fw-semibold text-dark">{{ substr($item->jam_mulai, 0, 5) }}</span>
                                                                <span class="text-muted small">WIB</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center"
                                                            style="border: 1px solid #cbd5e1; font-size: 0.88rem;">
                                                            @if (!empty($item->durasi_menit))
                                                                @php
                                                                    $jam = intdiv($item->durasi_menit, 60);
                                                                    $mnt = $item->durasi_menit % 60;
                                                                    if ($jam > 0 && $mnt > 0) {
                                                                        $durText = "{$jam} Jam {$mnt} Mnt";
                                                                    } elseif ($jam > 0) {
                                                                        $durText = "{$jam} Jam";
                                                                    } else {
                                                                        $durText = "{$mnt} Menit";
                                                                    }
                                                                @endphp
                                                                <span
                                                                    class="fw-semibold text-success">{{ $durText }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        @if ($currentGroupBy !== 'karyawan')
                                                            <td class="text-start" style="border: 1px solid #cbd5e1;">
                                                                {{ $item->nama_karyawan }} ({{ $item->no_karyawan }})</td>
                                                        @endif
                                                        @if ($currentGroupBy === 'karyawan' || $currentGroupBy === 'subcon')
                                                            <td class="text-start" style="border: 1px solid #cbd5e1;">
                                                                <strong>[{{ $item->kode_barang }}]</strong>
                                                                {{ $item->nama_barang }}
                                                            </td>
                                                        @endif
                                                        @if ($currentGroupBy !== 'subcon')
                                                            <td style="border: 1px solid #cbd5e1;">
                                                                {{ $item->nama_lokasi }}</td>
                                                        @endif
                                                        <td class="text-center" style="border: 1px solid #cbd5e1;">
                                                            {{ $item->jenis_pekerjaan ?: '-' }}
                                                        </td>
                                                        <td class="text-end fw-bold text-dark pe-3 col-jumlah"
                                                            style="border: 1px solid #cbd5e1;"
                                                            data-raw-value="{{ $item->jumlah }}">
                                                            {{ number_format($item->jumlah, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-center" style="border: 1px solid #cbd5e1;">
                                                            {{ $item->satuan ?? 'PCS' }}
                                                        </td>
                                                        <td class="text-start" style="border: 1px solid #cbd5e1;">
                                                            {{ $item->keterangan ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="fw-bold"
                                                    style="background-color: #f1f5f9; border: 1px solid #94a3b8;">
                                                    <td colspan="3" class="text-start ps-3"
                                                        style="border: 1px solid #94a3b8;">
                                                        Total :
                                                    </td>
                                                    <td class="text-center fw-bold text-success"
                                                        style="border: 1px solid #94a3b8; font-size: 0.88rem;">
                                                        {{ $durasiTotalText }}
                                                    </td>
                                                    <td colspan="3" style="border: 1px solid #94a3b8;"></td>
                                                    <td class="text-end fw-bold text-primary pe-3 col-total"
                                                        style="border: 1px solid #94a3b8;"
                                                        data-raw-value="{{ $subtotal }}">
                                                        {{ number_format($subtotal, 0, ',', '.') }}
                                                    </td>
                                                    <td colspan="2" style="border: 1px solid #94a3b8;"></td>
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

        .barang-report-block {
            page-break-inside: avoid !important;
            margin-bottom: 20px !important;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

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
                margin-bottom: 16px !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 8.5pt !important;
            }

            th,
            td {
                border: 1px solid #000000 !important;
                padding: 4px 6px !important;
                color: #000000 !important;
            }
        }

        /* Custom Compact Checkbox Multi-Select Styling */
        .custom-multiselect-dropdown {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }
        .multiselect-options-list::-webkit-scrollbar {
            width: 6px;
        }
        .multiselect-options-list::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .multiselect-options-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .multiselect-option-label {
            transition: all 0.12s ease;
            user-select: none;
            border-radius: 6px;
            font-size: 0.95rem;
            padding: 6px 8px;
        }
        .multiselect-option-label:hover {
            background-color: #f1f5f9 !important;
        }
        .multiselect-option-label.is-checked {
            background-color: #e0f2fe !important;
            font-weight: 600;
        }
        .multiselect-option-label .multiselect-checkbox {
            width: 1.15em;
            height: 1.15em;
            cursor: pointer;
            flex-shrink: 0;
        }
        .multiselect-trigger {
            cursor: pointer;
            min-height: 42px;
            font-size: 0.95rem;
            border-color: #dee2e6;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            padding: 8px 12px;
        }
        .multiselect-trigger:focus,
        .multiselect-trigger:active {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
        }
        .report-table th, 
        .report-table td {
            font-size: 0.95rem;
        }
    </style>

@endsection

@push('scripts')
    <!-- html2pdf.js for exact PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Helper fungsi untuk inisialisasi Custom Multi-Select Dropdown
            function initCustomMultiselect(wrapperId, defaultEmptyText, singleNoun, onSelectionChange) {
                const $wrapper = $('#' + wrapperId);
                if (!$wrapper.length) return;

                const $dropdownEl = $wrapper.find('.dropdown');
                const $trigger = $wrapper.find('.multiselect-trigger');
                const $label = $wrapper.find('.multiselect-label');
                const $badge = $wrapper.find('.multiselect-count');
                const $search = $wrapper.find('.multiselect-search');
                const $optionsList = $wrapper.find('.multiselect-options-list');
                const $options = $wrapper.find('.multiselect-option-label');
                const $checkboxes = $wrapper.find('.multiselect-checkbox');
                const $undoBtn = $wrapper.find('.multiselect-undo-btn');
                const $okBtn = $wrapper.find('.multiselect-ok-btn');

                // Fungsi memindahkan opsi yang SUDAH DIPILIH / CHECKED ke posisi teratas
                function sortSelectedOptionsToTop() {
                    const checkedLabels = [];
                    const uncheckedLabels = [];

                    $optionsList.children('.multiselect-option-label').each(function() {
                        if ($(this).find('.multiselect-checkbox').is(':checked')) {
                            checkedLabels.push(this);
                        } else {
                            uncheckedLabels.push(this);
                        }
                    });

                    $optionsList.append(checkedLabels);
                    $optionsList.append(uncheckedLabels);
                }

                function updateDisplay() {
                    const checked = $checkboxes.filter(':checked');
                    const count = checked.length;

                    $options.each(function() {
                        const cb = $(this).find('.multiselect-checkbox');
                        if (cb.is(':checked')) {
                            $(this).addClass('is-checked');
                        } else {
                            $(this).removeClass('is-checked');
                        }
                    });

                    if (count === 0) {
                        $label.text(defaultEmptyText).addClass('text-muted').removeClass('text-dark fw-semibold');
                        $badge.addClass('d-none').text('0');
                    } else if (count === 1) {
                        const labelText = checked.first().data('label') || checked.first().parent().text().trim();
                        $label.text(labelText).removeClass('text-muted').addClass('text-dark fw-semibold');
                        $badge.removeClass('d-none').text('1');
                    } else {
                        $label.text(count + ' ' + singleNoun + ' Dipilih').removeClass('text-muted').addClass('text-dark fw-semibold');
                        $badge.removeClass('d-none').text(count);
                    }

                    if (typeof onSelectionChange === 'function') {
                        onSelectionChange();
                    }
                }

                // Checkbox toggle
                $checkboxes.on('change', function() {
                    updateDisplay();
                });

                // Live search dalam dropdown
                $search.on('input', function() {
                    const q = $(this).val().toLowerCase().trim();
                    $options.each(function() {
                        if ($(this).hasClass('d-none-subcon')) return;
                        const text = $(this).text().toLowerCase();
                        if (!q || text.includes(q)) {
                            $(this).removeClass('d-none');
                        } else {
                            $(this).addClass('d-none');
                        }
                    });
                });

                // Tombol Undo / Reset Pilihan di sebelah kolom cari
                $undoBtn.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $checkboxes.prop('checked', false);
                    $search.val('').trigger('input');
                    updateDisplay();
                    sortSelectedOptionsToTop();
                });

                // Ketika filter dibuka: Taruh pilihan terpilih di atas dan LANGSUNG FOKUS ke kolom cari (user bisa langsung ketik)
                $dropdownEl.on('shown.bs.dropdown', function() {
                    sortSelectedOptionsToTop();
                    setTimeout(function() {
                        $search.focus().select();
                    }, 50);
                });

                // Tombol OK untuk menutup dropdown setelah selesai memilih
                $okBtn.on('click', function(e) {
                    e.preventDefault();
                    sortSelectedOptionsToTop();
                    const dropdown = bootstrap.Dropdown.getInstance($trigger[0]) || new bootstrap.Dropdown($trigger[0]);
                    dropdown.hide();
                });

                // Initial run
                updateDisplay();
                sortSelectedOptionsToTop();
            }

            // Fungsi filter dinamis: Karyawan dan Barang menyesuaikan Lokasi Subcon yang dipilih
            function syncLokasiToKaryawanAndBarang() {
                if (!$('#ms_lokasi').length) return;

                const selectedLokasi = $('#ms_lokasi .multiselect-checkbox:checked').map(function() {
                    return String($(this).val());
                }).get();

                // 1. Filter Karyawan
                $('#ms_karyawan .multiselect-option-label').each(function() {
                    const optLokasi = String($(this).data('lokasi') || '');
                    const match = selectedLokasi.length === 0 || selectedLokasi.includes(optLokasi);
                    if (match) {
                        $(this).removeClass('d-none-subcon d-none');
                    } else {
                        $(this).addClass('d-none-subcon d-none');
                        $(this).find('.multiselect-checkbox').prop('checked', false);
                    }
                });
                updateSingleDisplay('ms_karyawan', '-- Semua Karyawan (Multi-Select) --', 'Karyawan');

                // 2. Filter Barang
                $('#ms_barang .multiselect-option-label').each(function() {
                    const optLokasi = String($(this).data('lokasi') || '');
                    const match = selectedLokasi.length === 0 || !optLokasi || selectedLokasi.includes(optLokasi);
                    if (match) {
                        $(this).removeClass('d-none-subcon d-none');
                    } else {
                        $(this).addClass('d-none-subcon d-none');
                        $(this).find('.multiselect-checkbox').prop('checked', false);
                    }
                });
                updateSingleDisplay('ms_barang', '-- Semua Barang (Multi-Select) --', 'Barang');
            }

            function updateSingleDisplay(wrapperId, defaultEmptyText, singleNoun) {
                const $wrapper = $('#' + wrapperId);
                const $label = $wrapper.find('.multiselect-label');
                const $badge = $wrapper.find('.multiselect-count');
                const checked = $wrapper.find('.multiselect-checkbox:checked');
                const count = checked.length;

                $wrapper.find('.multiselect-option-label').each(function() {
                    const cb = $(this).find('.multiselect-checkbox');
                    if (cb.is(':checked')) {
                        $(this).addClass('is-checked');
                    } else {
                        $(this).removeClass('is-checked');
                    }
                });

                if (count === 0) {
                    $label.text(defaultEmptyText).addClass('text-muted').removeClass('text-dark fw-semibold');
                    $badge.addClass('d-none').text('0');
                } else if (count === 1) {
                    const labelText = checked.first().data('label') || checked.first().parent().text().trim();
                    $label.text(labelText).removeClass('text-muted').addClass('text-dark fw-semibold');
                    $badge.removeClass('d-none').text('1');
                } else {
                    $label.text(count + ' ' + singleNoun + ' Dipilih').removeClass('text-muted').addClass('text-dark fw-semibold');
                    $badge.removeClass('d-none').text(count);
                }
            }

            // Inisialisasi masing-masing filter
            initCustomMultiselect('ms_lokasi', '-- Semua Lokasi (Multi-Select) --', 'Lokasi', syncLokasiToKaryawanAndBarang);
            initCustomMultiselect('ms_karyawan', '-- Semua Karyawan (Multi-Select) --', 'Karyawan');
            initCustomMultiselect('ms_barang', '-- Semua Barang (Multi-Select) --', 'Barang');

            // Jalankan sinkronisasi awal
            syncLokasiToKaryawanAndBarang();
        });

        // Print Laporan Sheet (Menggunakan Dialog Cetak Browser)
        function printReportSheet() {
            window.print();
        }

        // Export PDF Laporan (A4 Portrait Native Server-Side DomPDF)
        function exportReportPDF() {
            // Gunakan parameter filter yang persis sedang aktif di halaman saat ini
            const currentSearch = window.location.search;
            const downloadUrl = "{{ route('laporan.export-pdf') }}" + (currentSearch ? currentSearch : '');

            Swal.fire({
                title: 'Sedang Mengunduh PDF...',
                text: 'Membuat dokumen PDF A4 Portrait resmi...',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            window.location.href = downloadUrl;
        }

        // Export Excel Laporan (Structured HTML Tables per Barang with Header & Subheaders)
        function exportReportExcel() {
            const tglMulai = document.getElementById('filter_tanggal_mulai').value || 'Awal';
            const tglAkhir = document.getElementById('filter_tanggal_akhir').value || 'Sekarang';
            const filename = 'Laporan_Subcon_' + tglMulai + '_sd_' + tglAkhir + '.xls';

            let reportHeaderHtml = '<table border="0">' +
                '<tr><td colspan="10" style="font-size:16px; font-weight:bold; font-family: Calibri, sans-serif;">e-System \u2014 PT. SNA MEDIKA<\/td><\/tr>' +
                '<tr><td colspan="10" style="font-size:14px; font-weight:bold; font-family: Calibri, sans-serif;">Laporan Pengerjaan Barang Subcon<\/td><\/tr>' +
                '<tr><td colspan="10" style="font-size:12px; font-weight:bold; font-family: Calibri, sans-serif;">Periode: ' +
                tglMulai + ' s/d ' + tglAkhir +
                '<\/td><\/tr>' +
                '<tr><td colspan="10"><\/td><\/tr>' +
                '<\/table>';

            let tablesHtml = '';
            const blocks = document.querySelectorAll('.barang-report-block');
            blocks.forEach(block => {
                const headerText = block.querySelector('.rounded-top')?.innerText.trim() || '';
                const table = block.querySelector('table');
                if (table) {
                    const clone = table.cloneNode(true);

                    // Format angka kuantitas & total sebagai Number murni (bukan text)
                    // Menggunakan mso-number-format:"\#\,\#\#0" agar Excel mengenali sebagai Angka (Number)
                    // dengan pemisah ribuan otomatis, tanpa warning tanda seru hijau dan tidak terkonversi 1000 jadi 1
                    clone.querySelectorAll('.col-jumlah, .col-total, [data-raw-value]').forEach(td => {
                        const rawVal = td.getAttribute('data-raw-value');
                        if (rawVal !== null && rawVal !== '') {
                            td.textContent = rawVal; // Masukkan angka murni (contoh: 1000, 500)
                        }
                        td.setAttribute('style', (td.getAttribute('style') || '') +
                            '; mso-number-format:"\\#\\,\\#\\#0"; text-align:right;');
                    });

                    // Untuk kolom teks lainnya, pastikan format teks dipertahankan
                    clone.querySelectorAll('td').forEach(td => {
                        if (!td.classList.contains('col-jumlah') && !td.classList.contains('col-total') && !
                            td.hasAttribute('data-raw-value')) {
                            const currentStyle = td.getAttribute('style') || '';
                            if (!currentStyle.includes('mso-number-format')) {
                                td.setAttribute('style', currentStyle + '; mso-number-format:"\\@";');
                            }
                        }
                    });

                    tablesHtml += '<table border="0">' +
                        '<tr><td colspan="10" style="background-color:#e0f2fe; font-size:13px; font-weight:bold; color:#0369a1; border:0.5pt solid #94a3b8; font-family: Calibri, sans-serif;">' +
                        headerText + '<\/td><\/tr>' +
                        '<\/table>' +
                        clone.outerHTML + '<br/>';
                }
            });

            const fullExcelHtml =
                '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' +
                '<head>' +
                '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"/>' +
                '<!--[if gte mso 9]><xml>' +
                '<' + 'x:ExcelWorkbook><' + 'x:ExcelWorksheets><' + 'x:ExcelWorksheet>' +
                '<' + 'x:Name>Laporan Subcon</' + 'x:Name>' +
                '<' + 'x:WorksheetOptions><' + 'x:DisplayGridlines/></' + 'x:WorksheetOptions>' +
                '</' + 'x:ExcelWorksheet></' + 'x:ExcelWorksheets></' + 'x:ExcelWorkbook>' +
                '</xml><![endif]-->' +
                '<style>' +
                'table { border-collapse: collapse; margin-bottom: 15px; font-family: Calibri, Arial, sans-serif; font-size: 11pt; }' +
                'th, td { border: 0.5pt solid #94a3b8; padding: 6px; }' +
                'th { background-color: #f8fafc; font-weight: bold; text-align: center; }' +
                '.text-start { text-align: left; }' +
                '.text-center { text-align: center; }' +
                '.text-end { text-align: right; }' +
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
