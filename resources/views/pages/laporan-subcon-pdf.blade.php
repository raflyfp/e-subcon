<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Pengerjaan Barang Subcon</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 8mm 8mm 12mm 8mm;
        }

        * {
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }

        /* Header Document */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 6px;
        }

        .header-table td {
            vertical-align: top;
            padding: 0 0 6px 0;
            border: none;
        }

        .company-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2px 0;
            letter-spacing: -0.3px;
        }

        .report-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 4px 0;
        }

        .periode-text {
            font-size: 8.5pt;
            font-weight: bold;
            color: #334155;
            margin-bottom: 3px;
        }

        .filter-meta {
            font-size: 7.5pt;
            color: #64748b;
        }

        .filter-meta strong {
            color: #334155;
        }

        .logo-img {
            max-height: 48px;
            max-width: 150px;
        }

        /* Group Block */
        .group-block {
            margin-bottom: 12px;
            page-break-inside: auto;
        }

        .group-header {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 4px 8px;
            border: 1px solid #94a3b8;
            border-bottom: none;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        /* Table Styling */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            page-break-inside: auto;
        }

        table.data-table thead {
            display: table-header-group;
        }

        table.data-table tfoot {
            display: table-footer-group;
        }

        table.data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        table.data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            font-size: 7.5pt;
            text-align: center;
            padding: 4px 3px;
            border: 0.8px solid #94a3b8;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        table.data-table td {
            font-size: 7.5pt;
            padding: 3.5px 3px;
            border: 0.8px solid #cbd5e1;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        table.data-table tfoot td {
            background-color: #f8fafc;
            border: 0.8px solid #94a3b8;
            font-weight: bold;
            padding: 4px 3px;
        }

        /* Text Utilities */
        .text-center { text-align: center; }
        .text-left   { text-align: left; }
        .text-right  { text-align: right; }
        .fw-bold     { font-weight: bold; }
        .text-muted  { color: #64748b; }
        .text-success{ color: #16a34a; }
        .text-primary{ color: #0284c7; }

        /* Footer info */
        .doc-footer {
            margin-top: 10px;
            padding-top: 5px;
            border-top: 0.8px dashed #cbd5e1;
            font-size: 7pt;
            color: #64748b;
            page-break-inside: avoid;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            border: none;
            padding: 0;
            font-size: 7pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    {{-- HEADER DOKUMEN --}}
    <table class="header-table">
        <tr>
            <td style="width: 72%;">
                <div class="company-title">PT. SNA MEDIKA</div>
                <div class="report-title">Laporan Pengerjaan Barang Subcon</div>
                <div class="periode-text">
                    Periode :
                    {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->format('Y-m-d') : 'Awal' }}
                    s/d
                    {{ $tanggalAkhir ? \Carbon\Carbon::parse($tanggalAkhir)->format('Y-m-d') : 'Sekarang' }}
                </div>
                <div class="filter-meta">
                    <span><strong>Barang :</strong> {{ $selectedBarangObj ? '[' . $selectedBarangObj->kode_barang . '] ' . $selectedBarangObj->nama_barang . ' (' . ($selectedBarangObj->satuan ?? 'PCS') . ')' : 'SEMUA BARANG' }}</span>
                    &nbsp;|&nbsp;
                    <span><strong>Lokasi :</strong> {{ $selectedLokasiObj ? $selectedLokasiObj->nama_lokasi : 'SEMUA LOKASI' }}</span>
                    @if (auth()->user()->is_admin)
                        &nbsp;|&nbsp;
                        <span><strong>Karyawan :</strong> {{ $selectedKaryawanObj ? $selectedKaryawanObj->nama_karyawan . ' (' . $selectedKaryawanObj->no_karyawan . ')' : 'SEMUA KARYAWAN' }}</span>
                    @endif
                </div>
            </td>
            <td style="width: 28%; text-align: right;">
                @if (!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo SNA">
                @endif
            </td>
        </tr>
    </table>

    {{-- TABEL PENGELOMPOKAN DATA --}}
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

        <div class="group-block">
            <div class="group-header">
                @if ($currentGroupBy === 'karyawan')
                    {{ $groupKey }} <span style="font-weight: normal; color: #475569; font-size: 7.5pt;">(Lokasi: {{ $firstItem->nama_lokasi }})</span>
                @elseif ($currentGroupBy === 'subcon')
                    Lokasi Subcon: {{ $groupKey }}
                @else
                    [{{ $groupKey }}] {{ $firstItem->nama_barang }}
                @endif
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="width: 13%;">Jam Kerja</th>
                        <th style="width: 8%;">Durasi</th>

                        @if ($currentGroupBy !== 'karyawan')
                            <th style="width: 15%;">Karyawan</th>
                        @endif

                        @if ($currentGroupBy === 'karyawan' || $currentGroupBy === 'subcon')
                            <th style="width: 20%;">Barang yang Dikerjakan</th>
                        @endif

                        @if ($currentGroupBy !== 'subcon')
                            <th style="width: 10%;">Lokasi Subcon</th>
                        @endif

                        <th style="width: 10%;">Jenis Pekerjaan</th>
                        <th style="width: 10%;">Jumlah Selesai</th>
                        <th style="width: 6%;">Satuan</th>
                        <th style="width: {{ $currentGroupBy === 'barang' ? '14%' : '9%' }};">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if ($item->jam_mulai && $item->jam_selesai)
                                    <span class="fw-bold">{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</span>
                                    <span class="text-muted" style="font-size: 6.5pt;">WIB</span>
                                @elseif ($item->jam_mulai)
                                    <span class="fw-bold">{{ substr($item->jam_mulai, 0, 5) }}</span>
                                    <span class="text-muted" style="font-size: 6.5pt;">WIB</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if (!empty($item->durasi_menit))
                                    @php
                                        $jam = intdiv($item->durasi_menit, 60);
                                        $mnt = $item->durasi_menit % 60;
                                        if ($jam > 0 && $mnt > 0) {
                                            $durText = "{$jam} Jam {$mnt} M";
                                        } elseif ($jam > 0) {
                                            $durText = "{$jam} Jam";
                                        } else {
                                            $durText = "{$mnt} Menit";
                                        }
                                    @endphp
                                    <span class="text-success fw-bold">{{ $durText }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            @if ($currentGroupBy !== 'karyawan')
                                <td class="text-left">{{ $item->nama_karyawan }} ({{ $item->no_karyawan }})</td>
                            @endif

                            @if ($currentGroupBy === 'karyawan' || $currentGroupBy === 'subcon')
                                <td class="text-left">
                                    <span class="fw-bold">[{{ $item->kode_barang }}]</span>
                                    {{ $item->nama_barang }}
                                </td>
                            @endif

                            @if ($currentGroupBy !== 'subcon')
                                <td class="text-center">{{ $item->nama_lokasi }}</td>
                            @endif

                            <td class="text-center">{{ $item->jenis_pekerjaan ?: '-' }}</td>
                            <td class="text-right fw-bold" style="padding-right: 5px;">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->satuan ?? 'PCS' }}</td>
                            <td class="text-left">{{ $item->keterangan ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-left fw-bold" style="padding-left: 5px;">Total :</td>
                        <td class="text-center text-success fw-bold">{{ $durasiTotalText }}</td>

                        @php
                            $midColspan = 0;
                            if ($currentGroupBy !== 'karyawan') $midColspan++;
                            if ($currentGroupBy === 'karyawan' || $currentGroupBy === 'subcon') $midColspan++;
                            if ($currentGroupBy !== 'subcon') $midColspan++;
                            $midColspan++; // Jenis Pekerjaan
                        @endphp

                        <td colspan="{{ $midColspan }}"></td>
                        <td class="text-right text-primary fw-bold" style="padding-right: 5px;">{{ number_format($subtotal, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty
        <div style="text-align: center; padding: 25px; color: #64748b; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 4px;">
            Tidak ada data pengerjaan barang pada periode / filter ini.
        </div>
    @endforelse

    {{-- FOOTER INFO --}}
    <div class="doc-footer">
        <table class="footer-table">
            <tr>
                <td style="text-align: left;">
                    Dicetak oleh: <strong>{{ auth()->user()->name }}</strong>
                </td>
                <td style="text-align: right;">
                    Waktu cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
