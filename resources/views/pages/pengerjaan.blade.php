@extends('layouts.mantis')
@section('title', 'Formulir Pengerjaan Barang')

@section('content')

    <div class="mb-4">
        <h1 class="content mb-1">Formulir Pengerjaan Barang</h1>
        <p class="text-muted mb-0">Input pencatatan kuantitas pengerjaan barang subcon</p>
    </div>

    {{-- Flat & Simple Google Form Style Card --}}
    <div class="card border mb-4" style="max-width: 800px; margin: 0 auto; border-top: 6px solid #2563eb !important;">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="ti ti-edit text-primary me-2"></i>Formulir Input Pengerjaan Barang
                    </h5>
                    <small class="text-muted">Silakan pilih karyawan pelaksana dan data barang yang dikerjakan</small>
                </div>
                <div>
                    @if (auth()->user()->is_admin)
                        <span class="badge bg-danger-subtle text-danger border px-3 py-2">
                            <i class="ti ti-shield me-1"></i> Admin Input
                        </span>
                    @else
                        <span class="badge bg-primary-subtle text-primary border px-3 py-2 fs-6">
                            <i class="ti ti-building me-1"></i> Subcon:
                            <strong>{{ $subcon->nama_lokasi ?? auth()->user()->name }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form method="POST" action="{{ route('pengerjaan.store') }}" id="formPengerjaanAuth" novalidate>
                @csrf

                {{-- 1. Lokasi Subcon (HANYA DITAMPILKAN JIKA ADMIN) --}}
                @if (auth()->user()->is_admin)
                    <div class="mb-4 p-3 bg-light rounded border">
                        <label class="form-label fw-bold" for="auth_lokasi_subcon_id">
                            1. Lokasi Subcon <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted small mb-2">Pilih lokasi tempat pengerjaan barang dilakukan:</p>

                        <select class="form-select" name="lokasi_subcon_id" id="auth_lokasi_subcon_id" required>
                            <option value="">-- Pilih Lokasi Subcon --</option>
                            @foreach ($lokasiList as $l)
                                <option value="{{ $l->id }}"
                                    {{ old('lokasi_subcon_id') == $l->id ? 'selected' : '' }}>
                                    {{ $l->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- 2. Karyawan Pelaksana --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_karyawan_id">
                        {{ auth()->user()->is_admin ? '2.' : '1.' }} Karyawan yang Mengerjakan <span
                            class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">
                        @if (auth()->user()->is_admin)
                            Pilih karyawan pelaksana pengerjaan barang:
                        @else
                            Pilih nama karyawan dalam subcon
                            <strong>{{ $subcon->nama_lokasi ?? auth()->user()->name }}</strong>:
                        @endif
                    </p>

                    <select class="form-select" name="karyawan_id" id="auth_karyawan_id" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach ($karyawanList as $k)
                            <option value="{{ $k->id }}" data-lokasi="{{ $k->lokasi_subcon_id }}"
                                {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_karyawan }} ({{ $k->no_karyawan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. (Admin) / 2. (Subcon) Jenis Pekerjaan (SEBELUM KODE BARANG - CENTANG / CHECKBOX) --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold">
                        {{ auth()->user()->is_admin ? '3.' : '2.' }} Jenis Pekerjaan <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Pilih jenis pekerjaan untuk memfilter daftar barang yang dikerjakan:
                    </p>

                    <div class="d-flex gap-4 p-3 bg-white rounded border flex-wrap" id="container_jenis_pekerjaan">
                        @foreach ($pekerjaanList as $p)
                            <div class="form-check">
                                <input class="form-check-input check-single-jenis" type="checkbox" name="jenis_pekerjaan"
                                    id="check_{{ strtolower($p->nama_pekerjaan) }}" value="{{ $p->nama_pekerjaan }}"
                                    {{ old('jenis_pekerjaan') == $p->nama_pekerjaan ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="check_{{ strtolower($p->nama_pekerjaan) }}"
                                    style="cursor: pointer;">
                                    {{ $p->nama_pekerjaan }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 4. (Admin) / 3. (Subcon) Barang yang Dikerjakan --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_barang_id">
                        {{ auth()->user()->is_admin ? '4.' : '3.' }} Barang yang Dikerjakan <span
                            class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Pilih kode / nama barang yang dikerjakan (terfilter sesuai jenis
                        pekerjaan):</p>

                    <select class="form-select" name="barang_id" id="auth_barang_id" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangList as $b)
                            <option value="{{ $b->id }}" data-satuan="{{ $b->satuan ?? 'PCS' }}"
                                data-kode="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }}"
                                data-lokasi="{{ $b->lokasi_subcon_id ?? '' }}"
                                data-pekerjaan-id="{{ $b->pekerjaan_id ?? '' }}"
                                data-jenis-pekerjaan="{{ $b->pekerjaan->nama_pekerjaan ?? ($b->jenis_pekerjaan ?? '') }}"
                                {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                                [{{ $b->kode_barang }}] {{ $b->nama_barang }} (Satuan: {{ $b->satuan ?? 'PCS' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 5. (Admin) / 4. (Subcon) Tanggal Pengerjaan --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_tanggal">
                        {{ auth()->user()->is_admin ? '5.' : '4.' }} Tanggal Pengerjaan <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Tanggal pengerjaan otomatis terisi hari ini:</p>

                    <input type="date" class="form-control form-control-lg" name="tanggal" id="auth_tanggal"
                        value="{{ date('Y-m-d') }}" readonly style="background-color: #f1f5f9; cursor: not-allowed;">
                    <small class="text-muted mt-2 d-block"><i class="ti ti-lock me-1"></i>Tanggal terkunci otomatis hari ini
                        ({{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}).</small>
                </div>

                {{-- 6. (Admin) / 5. (Subcon) Jam Kerja & Durasi Pengerjaan --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold">
                        {{ auth()->user()->is_admin ? '6.' : '5.' }} Waktu Pengerjaan & Durasi
                    </label>
                    <p class="text-muted small mb-2">Pilih jam mulai dan jam selesai pengerjaan (durasi otomatis terhitung):
                    </p>

                    <div class="row g-3 align-items-center">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-muted mb-1">
                                <i class="ti ti-clock-play text-primary me-1"></i> Jam Mulai (WIB)
                            </label>
                            <input type="hidden" name="jam_mulai" id="auth_jam_mulai" value="{{ old('jam_mulai') }}">
                            <div class="custom-time-picker" id="picker_jam_mulai" data-target="auth_jam_mulai">
                                <div class="ctp-display" id="display_jam_mulai">
                                    <span class="ctp-display-time" id="display_text_jam_mulai">--:--</span>
                                    <i class="ti ti-clock fs-5 text-muted"></i>
                                </div>
                                <div class="ctp-dropdown" id="dropdown_jam_mulai">
                                    <div class="ctp-header">
                                        <div class="ctp-handle"></div>
                                        <div class="ctp-title text-primary">
                                            <i class="ti ti-clock-play me-1"></i> Jam Mulai (WIB)
                                        </div>
                                    </div>
                                    <div class="ctp-columns">
                                        <div class="ctp-col">
                                            <button type="button" class="ctp-arrow ctp-arrow-up" data-dir="up"
                                                data-col="hour" data-picker="jam_mulai"><i
                                                    class="ti ti-chevron-up"></i></button>
                                            <div class="ctp-scroll" id="scroll_hour_jam_mulai" data-col="hour"
                                                data-picker="jam_mulai"></div>
                                            <button type="button" class="ctp-arrow ctp-arrow-down" data-dir="down"
                                                data-col="hour" data-picker="jam_mulai"><i
                                                    class="ti ti-chevron-down"></i></button>
                                        </div>
                                        <div class="ctp-separator">:</div>
                                        <div class="ctp-col">
                                            <button type="button" class="ctp-arrow ctp-arrow-up" data-dir="up"
                                                data-col="minute" data-picker="jam_mulai"><i
                                                    class="ti ti-chevron-up"></i></button>
                                            <div class="ctp-scroll" id="scroll_minute_jam_mulai" data-col="minute"
                                                data-picker="jam_mulai"></div>
                                            <button type="button" class="ctp-arrow ctp-arrow-down" data-dir="down"
                                                data-col="minute" data-picker="jam_mulai"><i
                                                    class="ti ti-chevron-down"></i></button>
                                        </div>
                                    </div>
                                    <div class="ctp-ok-wrapper">
                                        <button type="button" class="ctp-ok-btn"><i
                                                class="ti ti-check me-1"></i>OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-muted mb-1">
                                <i class="ti ti-clock-stop text-danger me-1"></i> Jam Selesai (WIB)
                            </label>
                            <input type="hidden" name="jam_selesai" id="auth_jam_selesai"
                                value="{{ old('jam_selesai') }}">
                            <div class="custom-time-picker" id="picker_jam_selesai" data-target="auth_jam_selesai">
                                <div class="ctp-display" id="display_jam_selesai">
                                    <span class="ctp-display-time" id="display_text_jam_selesai">--:--</span>
                                    <i class="ti ti-clock fs-5 text-muted"></i>
                                </div>
                                <div class="ctp-dropdown" id="dropdown_jam_selesai">
                                    <div class="ctp-header">
                                        <div class="ctp-handle"></div>
                                        <div class="ctp-title text-danger">
                                            <i class="ti ti-clock-stop me-1"></i> Jam Selesai (WIB)
                                        </div>
                                    </div>
                                    <div class="ctp-columns">
                                        <div class="ctp-col">
                                            <button type="button" class="ctp-arrow ctp-arrow-up" data-dir="up"
                                                data-col="hour" data-picker="jam_selesai"><i
                                                    class="ti ti-chevron-up"></i></button>
                                            <div class="ctp-scroll" id="scroll_hour_jam_selesai" data-col="hour"
                                                data-picker="jam_selesai"></div>
                                            <button type="button" class="ctp-arrow ctp-arrow-down" data-dir="down"
                                                data-col="hour" data-picker="jam_selesai"><i
                                                    class="ti ti-chevron-down"></i></button>
                                        </div>
                                        <div class="ctp-separator">:</div>
                                        <div class="ctp-col">
                                            <button type="button" class="ctp-arrow ctp-arrow-up" data-dir="up"
                                                data-col="minute" data-picker="jam_selesai"><i
                                                    class="ti ti-chevron-up"></i></button>
                                            <div class="ctp-scroll" id="scroll_minute_jam_selesai" data-col="minute"
                                                data-picker="jam_selesai"></div>
                                            <button type="button" class="ctp-arrow ctp-arrow-down" data-dir="down"
                                                data-col="minute" data-picker="jam_selesai"><i
                                                    class="ti ti-chevron-down"></i></button>
                                        </div>
                                    </div>
                                    <div class="ctp-ok-wrapper">
                                        <button type="button" class="ctp-ok-btn"><i
                                                class="ti ti-check me-1"></i>OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2" id="durasi_badge_container">
                        <div id="durasi_badge_info" class="small"></div>
                    </div>
                </div>

                {{-- 7. (Admin) / 6. (Subcon) Jumlah Pengerjaan Selesai --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_jumlah">
                        {{ auth()->user()->is_admin ? '7.' : '6.' }} Jumlah Barang Selesai <span id="label_satuan_text"
                            class="text-primary">(PCS)</span> <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Masukkan kuantitas barang yang telah diselesaikan:</p>

                    {{-- Input Jumlah Barang Selesai --}}
                    <div class="input-group input-group-lg">
                        <input type="number" class="form-control form-control-lg" name="jumlah" id="auth_jumlah"
                            min="1" placeholder="Masukkan jumlah (contoh: 50)" value="{{ old('jumlah') }}"
                            required>
                        <span class="input-group-text bg-white fw-bold text-primary span_satuan_addon"
                            id="span_satuan_addon">PCS</span>
                    </div>
                </div>

                {{-- 8. (Admin) / 7. (Subcon) Keterangan --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_keterangan">
                        {{ auth()->user()->is_admin ? '8.' : '7.' }} Catatan / Keterangan (Opsional)
                    </label>
                    <p class="text-muted small mb-2">Tuliskan keterangan tambahan bila ada kendala atau catatan khusus:</p>

                    <input type="text" class="form-control" name="keterangan" id="auth_keterangan"
                        placeholder="Tulis catatan opsional..." value="{{ old('keterangan') }}">
                </div>

                {{-- Submit Buttons --}}
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <small class="text-muted"><span class="text-danger">*</span> Kolom wajib diisi</small>
                    <div class="d-flex gap-2">
                        <button type="reset" class="btn btn-secondary">
                            <i class="ti ti-rotate-2 me-1"></i> Reset
                        </button>
                        @if (auth()->user()->canAccess('formulir_pengerjaan'))
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                                <i class="ti ti-send me-1"></i> Kirim Data Pengerjaan
                            </button>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Styling Select2 agar nama barang / teks panjang tidak terpotong dengan wrap text */
        .select2-container--bootstrap-5 .select2-selection--single {
            height: auto !important;
            min-height: 42px !important;
            padding: 7px 12px !important;
            font-size: 0.95rem !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            white-space: normal !important;
            word-break: break-word !important;
            line-height: 1.45 !important;
            color: #1e293b !important;
            padding-right: 25px !important;
        }

        .select2-container--bootstrap-5 .select2-dropdown .select2-results__option {
            white-space: normal !important;
            word-break: break-word !important;
            line-height: 1.45 !important;
            font-size: 0.92rem !important;
            padding: 8px 12px !important;
        }

        /* ====== Custom 24h Time Picker ====== */
        .custom-time-picker {
            position: relative;
            width: 100%;
        }

        .ctp-display {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            min-height: 48px;
            transition: border-color .2s, box-shadow .2s;
            user-select: none;
        }

        .ctp-display:hover {
            border-color: #2563eb;
        }

        .ctp-display:focus-within,
        .custom-time-picker.open .ctp-display {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .ctp-display-time {
            font-weight: 600;
            font-variant-numeric: tabular-nums;
            letter-spacing: 2px;
            color: #1e293b;
        }

        .ctp-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            z-index: 1055;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .12);
            padding: 12px 8px;
            animation: ctpFadeIn .15s ease;
        }

        .custom-time-picker.open .ctp-dropdown {
            display: block;
        }

        @keyframes ctpFadeIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .ctp-header {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }

        .ctp-handle {
            display: none;
        }

        .ctp-title {
            font-size: 0.95rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: .3px;
        }

        .ctp-columns {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .ctp-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80px;
        }

        .ctp-separator {
            font-size: 1.6rem;
            font-weight: 700;
            color: #64748b;
            padding: 0 2px;
            line-height: 1;
        }

        .ctp-arrow {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 0;
            color: #94a3b8;
            font-size: 1.3rem;
            line-height: 1;
            width: 100%;
            display: flex;
            justify-content: center;
            transition: color .15s;
        }

        .ctp-arrow:hover {
            color: #2563eb;
        }

        .ctp-arrow:active {
            color: #1d4ed8;
            transform: scale(0.92);
        }

        .ctp-scroll {
            height: 180px;
            overflow: hidden;
            position: relative;
            width: 100%;
            -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 30%, black 70%, transparent 100%);
            mask-image: linear-gradient(to bottom, transparent 0%, black 30%, black 70%, transparent 100%);
        }

        .ctp-scroll-inner {
            transition: transform .2s cubic-bezier(.4, 0, .2, 1);
        }

        .ctp-item {
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            font-weight: 500;
            color: #94a3b8;
            cursor: pointer;
            border-radius: 6px;
            transition: all .15s;
            user-select: none;
        }

        .ctp-item:hover {
            background: #f1f5f9;
            color: #475569;
        }

        .ctp-item.active {
            background: #2563eb;
            color: #fff;
            font-weight: 700;
            font-size: 1.25rem;
            box-shadow: 0 2px 8px rgba(37, 99, 235, .25);
        }

        @media (max-width: 576px) {
            .ctp-dropdown {
                position: fixed;
                top: auto;
                bottom: 0;
                left: 0;
                right: 0;
                border-radius: 16px 16px 0 0;
                padding: 16px 12px 24px;
                animation: ctpSlideUp .25s ease;
            }

            .ctp-header {
                margin-bottom: 12px;
                padding-bottom: 12px;
                border-bottom: 1px solid #e2e8f0;
            }

            .ctp-handle {
                display: block;
                width: 40px;
                height: 5px;
                background: #cbd5e1;
                border-radius: 10px;
                margin: 0 auto 12px;
            }

            .ctp-title {
                font-size: 1.15rem;
                font-weight: 700;
            }

            @keyframes ctpSlideUp {
                from {
                    opacity: 0;
                    transform: translateY(100%);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .ctp-col {
                width: 100px;
            }

            .ctp-scroll {
                height: 210px;
            }

            .ctp-item {
                height: 42px;
                font-size: 1.25rem;
            }

            .ctp-item.active {
                font-size: 1.4rem;
            }

            .ctp-overlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .35);
                z-index: 1054;
                animation: ctpFadeIn .15s ease;
            }
        }

        @media (min-width: 577px) {
            .ctp-overlay {
                display: none !important;
            }
        }

        .ctp-ok-wrapper {
            text-align: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }

        .ctp-ok-btn {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 40px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, transform .1s;
            letter-spacing: .5px;
        }

        .ctp-ok-btn:hover {
            background: #1d4ed8;
        }

        .ctp-ok-btn:active {
            background: #1e40af;
            transform: scale(0.96);
        }

        @media (max-width: 576px) {
            .ctp-ok-btn {
                width: 100%;
                padding: 14px;
                font-size: 1.15rem;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#auth_karyawan_id, #auth_barang_id, #auth_lokasi_subcon_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Pilih data...';
                }
            });

            // Update label satuan secara dinamis saat barang dipilih
            function updateSatuanDisplay() {
                let selectedOption = $('#auth_barang_id').find(':selected');
                let satuan = selectedOption.data('satuan') || 'PCS';
                $('#label_satuan_text').text('(' + satuan + ')');
                $('.span_satuan_addon').text(satuan);
            }

            $('#auth_barang_id').on('change', updateSatuanDisplay);
            updateSatuanDisplay();

            // Simpan referensi opsi asli untuk Karyawan dan Barang agar Select2 dapat memfilter dengan sempurna
            let allKaryawanOptions = $('#auth_karyawan_id option').clone();
            let allBarangOptions = $('#auth_barang_id option').clone();

            // Fungsi filter barang berdasarkan lokasi subcon (jika ada) dan jenis pekerjaan
            function filterBarangOptions() {
                let selectedLokasi = $('#auth_lokasi_subcon_id').length ? $('#auth_lokasi_subcon_id').val() : '';
                let selectedJenis = $('.check-single-jenis:checked').val() || '';
                let currentBarang = $('#auth_barang_id').val();

                $('#auth_barang_id').empty();

                allBarangOptions.each(function() {
                    let optVal = $(this).val();
                    if (!optVal) {
                        $('#auth_barang_id').append($(this).clone());
                        return;
                    }

                    let optLokasi = $(this).data('lokasi');
                    let optJenis = $(this).data('jenis-pekerjaan');

                    // Filter Lokasi: jika tidak ada lokasi dipilih, atau lokasi cocok, atau barang bersifat umum (optLokasi kosong)
                    let matchLokasi = !selectedLokasi || String(optLokasi) === String(selectedLokasi) || !
                        optLokasi;

                    // Filter Jenis Pekerjaan: jika jenis pekerjaan dipilih, hanya tampilkan yang sesuai jenis pekerjaan tersebut
                    let matchJenis = true;
                    if (selectedJenis) {
                        matchJenis = optJenis && String(optJenis).trim().toLowerCase() === String(
                            selectedJenis).trim().toLowerCase();
                    }

                    if (matchLokasi && matchJenis) {
                        $('#auth_barang_id').append($(this).clone());
                    }
                });

                // Cek apakah barang yang terpilih sebelumnya masih ada dalam opsi yang difilter
                if (currentBarang && $('#auth_barang_id').find(`option[value="${currentBarang}"]`).length) {
                    $('#auth_barang_id').val(currentBarang);
                } else {
                    $('#auth_barang_id').val('');
                }
                $('#auth_barang_id').trigger('change.select2');
                updateSatuanDisplay();
            }

            // Filter karyawan berdasarkan lokasi subcon (khusus admin)
            function filterKaryawanOptions() {
                if (!$('#auth_lokasi_subcon_id').length) return;

                let selectedLokasi = $('#auth_lokasi_subcon_id').val();
                let currentKaryawan = $('#auth_karyawan_id').val();

                $('#auth_karyawan_id').empty();
                allKaryawanOptions.each(function() {
                    let optVal = $(this).val();
                    let optLokasi = $(this).data('lokasi');
                    if (!optVal || !selectedLokasi || String(optLokasi) === String(selectedLokasi)) {
                        $('#auth_karyawan_id').append($(this).clone());
                    }
                });
                if (currentKaryawan && $('#auth_karyawan_id').find(`option[value="${currentKaryawan}"]`).length) {
                    $('#auth_karyawan_id').val(currentKaryawan);
                } else {
                    $('#auth_karyawan_id').val('');
                }
                $('#auth_karyawan_id').trigger('change.select2');
            }

            $('#auth_lokasi_subcon_id').on('change', function() {
                filterKaryawanOptions();
                filterBarangOptions();
            });

            // Checkbox single-selection (jika salah satu dicentang, yang lain uncheck) & trigger filter barang
            $('.check-single-jenis').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.check-single-jenis').not(this).prop('checked', false);
                }
                filterBarangOptions();
            });

            if ($('#auth_lokasi_subcon_id').length && $('#auth_lokasi_subcon_id').val()) {
                $('#auth_lokasi_subcon_id').trigger('change');
            }

            let currentDurasiText = '';

            // Fungsi Hitung Durasi Otomatis dari Jam Mulai & Jam Selesai
            function hitungDurasi() {
                let mulai = $('#auth_jam_mulai').val();
                let selesai = $('#auth_jam_selesai').val();

                if (!mulai || !selesai) {
                    currentDurasiText = '';
                    $('#durasi_badge_info').empty();
                    return;
                }

                let [hMulai, mMulai] = mulai.split(':').map(Number);
                let [hSelesai, mSelesai] = selesai.split(':').map(Number);

                if (isNaN(hMulai) || isNaN(mMulai) || isNaN(hSelesai) || isNaN(mSelesai)) {
                    currentDurasiText = '';
                    $('#durasi_badge_info').empty();
                    return;
                }

                let menitMulai = hMulai * 60 + mMulai;
                let menitSelesai = hSelesai * 60 + mSelesai;

                // Tangani kasus lintas hari / tengah malam jika selesai < mulai
                if (menitSelesai < menitMulai) {
                    menitSelesai += 24 * 60;
                }

                let diffMenit = menitSelesai - menitMulai;
                let hours = Math.floor(diffMenit / 60);
                let mins = diffMenit % 60;

                let durasiText = '';
                if (hours > 0 && mins > 0) {
                    durasiText = `${hours} Jam ${mins} Menit`;
                } else if (hours > 0) {
                    durasiText = `${hours} Jam`;
                } else {
                    durasiText = `${mins} Menit`;
                }

                currentDurasiText = durasiText;
                $('#durasi_badge_info').html(
                    `<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">` +
                    `<i class="ti ti-circle-check fs-6 me-1"></i> Durasi terhitung: <strong>${durasiText}</strong> (${diffMenit} Menit)` +
                    `</span>`
                );
            }

            // ====== Custom 24h Scroll Time Picker ======
            const ITEM_H = window.innerWidth <= 576 ? 42 : 36;
            const VISIBLE = 5;
            const CENTER = Math.floor(VISIBLE / 2); // index 2 = tengah

            function buildColumn(scrollEl, max, pad) {
                let inner = $('<div class="ctp-scroll-inner"></div>');
                // Padding items di atas
                for (let p = 0; p < CENTER; p++) {
                    inner.append('<div class="ctp-item ctp-pad" style="visibility:hidden;">&nbsp;</div>');
                }
                for (let i = 0; i < max; i++) {
                    let label = String(i).padStart(pad, '0');
                    inner.append(`<div class="ctp-item" data-value="${i}">${label}</div>`);
                }
                // Padding items di bawah
                for (let p = 0; p < CENTER; p++) {
                    inner.append('<div class="ctp-item ctp-pad" style="visibility:hidden;">&nbsp;</div>');
                }
                scrollEl.empty().append(inner);
            }

            function scrollToValue(scrollEl, value) {
                let inner = scrollEl.find('.ctp-scroll-inner');
                let offset = -(value) * ITEM_H;
                inner.css('transform', `translateY(${offset}px)`);
                // Update active
                scrollEl.find('.ctp-item').removeClass('active');
                scrollEl.find(`.ctp-item[data-value="${value}"]`).addClass('active');
            }

            function getSelectedValue(scrollEl) {
                let activeItem = scrollEl.find('.ctp-item.active');
                return activeItem.length ? parseInt(activeItem.data('value')) : 0;
            }

            function updateHiddenInput(pickerName) {
                let h = getSelectedValue($(`#scroll_hour_${pickerName}`));
                let m = getSelectedValue($(`#scroll_minute_${pickerName}`));
                let timeStr = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
                let targetId = $(`#picker_${pickerName}`).data('target');
                $(`#${targetId}`).val(timeStr);
                $(`#display_text_${pickerName}`).text(timeStr);
                hitungDurasi();
            }

            function initPicker(pickerName) {
                let hourScroll = $(`#scroll_hour_${pickerName}`);
                let minScroll = $(`#scroll_minute_${pickerName}`);

                buildColumn(hourScroll, 24, 2);
                buildColumn(minScroll, 60, 2);

                // Set initial value from hidden input if exists
                let targetId = $(`#picker_${pickerName}`).data('target');
                let existingVal = $(`#${targetId}`).val();
                if (existingVal && existingVal.includes(':')) {
                    let [h, m] = existingVal.split(':').map(Number);
                    scrollToValue(hourScroll, h);
                    scrollToValue(minScroll, m);
                    $(`#display_text_${pickerName}`).text(existingVal);
                } else {
                    scrollToValue(hourScroll, 0);
                    scrollToValue(minScroll, 0);
                }

                // Click on item to select
                hourScroll.on('click', '.ctp-item:not(.ctp-pad)', function() {
                    scrollToValue(hourScroll, parseInt($(this).data('value')));
                    updateHiddenInput(pickerName);
                });
                minScroll.on('click', '.ctp-item:not(.ctp-pad)', function() {
                    scrollToValue(minScroll, parseInt($(this).data('value')));
                    updateHiddenInput(pickerName);
                });

                // Mouse wheel scroll
                [hourScroll, minScroll].forEach(function(scrollEl) {
                    scrollEl[0].addEventListener('wheel', function(e) {
                        e.preventDefault();
                        let col = scrollEl.data('col');
                        let max = col === 'hour' ? 24 : 60;
                        let current = getSelectedValue(scrollEl);
                        let next = e.deltaY > 0 ? Math.min(current + 1, max - 1) : Math.max(
                            current - 1, 0);
                        scrollToValue(scrollEl, next);
                        updateHiddenInput(pickerName);
                    }, {
                        passive: false
                    });
                });

                // Touch swipe support
                [hourScroll, minScroll].forEach(function(scrollEl) {
                    let touchStartY = 0;
                    let touchAccum = 0;

                    scrollEl[0].addEventListener('touchstart', function(e) {
                        touchStartY = e.touches[0].clientY;
                        touchAccum = 0;
                        scrollEl.find('.ctp-scroll-inner').css('transition', 'none');
                    }, {
                        passive: true
                    });

                    scrollEl[0].addEventListener('touchmove', function(e) {
                        e.preventDefault();
                        let diff = touchStartY - e.touches[0].clientY;
                        touchAccum += diff;
                        touchStartY = e.touches[0].clientY;

                        let col = scrollEl.data('col');
                        let max = col === 'hour' ? 24 : 60;
                        let threshold = ITEM_H / 2;

                        while (Math.abs(touchAccum) >= threshold) {
                            let current = getSelectedValue(scrollEl);
                            let next = touchAccum > 0 ? Math.min(current + 1, max - 1) : Math.max(
                                current - 1, 0);
                            scrollToValue(scrollEl, next);
                            touchAccum -= (touchAccum > 0 ? threshold : -threshold);
                        }
                    }, {
                        passive: false
                    });

                    scrollEl[0].addEventListener('touchend', function() {
                        scrollEl.find('.ctp-scroll-inner').css('transition',
                            'transform .2s cubic-bezier(.4,0,.2,1)');
                        updateHiddenInput(pickerName);
                    }, {
                        passive: true
                    });
                });
            }

            // Arrow button handlers
            $(document).on('click', '.ctp-arrow', function() {
                let dir = $(this).data('dir');
                let col = $(this).data('col');
                let pickerName = $(this).data('picker');
                let scrollEl = $(`#scroll_${col}_${pickerName}`);
                let max = col === 'hour' ? 24 : 60;
                let current = getSelectedValue(scrollEl);
                let next = dir === 'up' ? Math.max(current - 1, 0) : Math.min(current + 1, max - 1);
                scrollToValue(scrollEl, next);
                updateHiddenInput(pickerName);
            });

            // OK button handler - tutup picker
            $(document).on('click', '.ctp-ok-btn', function(e) {
                e.stopPropagation();
                let picker = $(this).closest('.custom-time-picker');
                picker.removeClass('open');
                $('.ctp-overlay').remove();
            });

            // Toggle dropdown
            $(document).on('click', '.ctp-display', function(e) {
                e.stopPropagation();
                let picker = $(this).closest('.custom-time-picker');
                let isOpen = picker.hasClass('open');

                // Close all other pickers
                $('.custom-time-picker').removeClass('open');
                $('.ctp-overlay').remove();

                if (!isOpen) {
                    picker.addClass('open');
                    // Add overlay for mobile
                    if (window.innerWidth <= 576) {
                        let overlay = $('<div class="ctp-overlay"></div>');
                        overlay.on('click', function() {
                            picker.removeClass('open');
                            $(this).remove();
                        });
                        $('body').append(overlay);
                    }
                }
            });

            // Close picker when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.custom-time-picker').length) {
                    $('.custom-time-picker').removeClass('open');
                    $('.ctp-overlay').remove();
                }
            });

            // Init both pickers
            initPicker('jam_mulai');
            initPicker('jam_selesai');

            // Trigger durasi if both have values
            if ($('#auth_jam_mulai').val() && $('#auth_jam_selesai').val()) {
                hitungDurasi();
            }

            // Validasi & Konfirmasi submit form
            let isConfirmed = false;

            $('#formPengerjaanAuth').on('submit', function(e) {
                if (isConfirmed) {
                    return true;
                }

                e.preventDefault();

                let form = this;
                let karyawanId = $('#auth_karyawan_id').val();
                let selectedJenis = $('.check-single-jenis:checked').val() || '';
                let barangId = $('#auth_barang_id').val();
                let qty = parseInt($('#auth_jumlah').val()) || 0;
                let satuan = $('.span_satuan_addon').first().text() || 'PCS';
                let jamMulai = $('#auth_jam_mulai').val() || '';
                let jamSelesai = $('#auth_jam_selesai').val() || '';

                let selectedBarangOption = $('#auth_barang_id').find(':selected');
                let kodeBarang = selectedBarangOption.data('kode') || '';
                let namaBarang = selectedBarangOption.data('nama') || '';
                let displayBarang = (kodeBarang && namaBarang) ? `[${kodeBarang}] ${namaBarang}` : (
                    selectedBarangOption.text().trim() || '-');

                if (!karyawanId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Karyawan Belum Dipilih',
                        text: 'Silakan pilih karyawan pelaksana terlebih dahulu.'
                    });
                    $('#auth_karyawan_id').select2('open');
                    return false;
                }

                if (!selectedJenis) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilihan Kosong',
                        text: 'Silakan centang salah satu jenis pekerjaan terlebih dahulu.'
                    });
                    return false;
                }

                if (!barangId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Barang Belum Dipilih',
                        text: 'Silakan pilih barang yang dikerjakan terlebih dahulu.'
                    });
                    $('#auth_barang_id').select2('open');
                    return false;
                }

                if (qty <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jumlah Kuantitas Kosong',
                        text: 'Masukkan kuantitas barang selesai yang valid (minimal 1).'
                    });
                    $('#auth_jumlah').focus();
                    return false;
                }

                let waktuInfoHtml = '';
                if (jamMulai && jamSelesai) {
                    let durasiLabel = currentDurasiText ? ` (${currentDurasiText})` : '';
                    waktuInfoHtml =
                        `<strong>Jam Kerja:</strong> ${jamMulai} - ${jamSelesai} WIB${durasiLabel}<br>`;
                }

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    html: `<div class="text-start p-3 bg-light rounded border mb-2" style="font-size: 0.95rem; line-height: 1.6;">` +
                        `<strong>Jenis Pekerjaan:</strong> ${selectedJenis}<br>` +
                        `<strong>Nama Barang:</strong> ${displayBarang}<br>` +
                        waktuInfoHtml +
                        `<strong>Jumlah Selesai:</strong> ${qty} ${satuan}` +
                        `</div>` +
                        `<small class="text-muted">Pastikan data di atas sudah benar sebelum disimpan.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Periksa Kembali'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan Data...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Tarik CSRF token terbaru dari server sebelum form dikirimkan
                        $.ajax({
                            url: "{{ route('refresh-csrf') }}",
                            type: "GET",
                            dataType: "json",
                            timeout: 3000,
                            success: function(data) {
                                if (data.csrf_token) {
                                    $('input[name="_token"]').val(data.csrf_token);
                                }
                            },
                            complete: function() {
                                isConfirmed = true;
                                form.submit();
                            }
                        });
                    }
                });
            });

            // Fokus otomatis ke search field saat dibuka
            $(document).on('select2:open', () => {
                setTimeout(() => {
                    document.querySelector('.select2-search__field')?.focus();
                }, 50);
            });
        });
    </script>
@endpush
