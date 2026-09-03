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

                        <select class="form-select form-select-lg" name="lokasi_subcon_id" id="auth_lokasi_subcon_id"
                            required>
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

                    <select class="form-select form-select-lg" name="karyawan_id" id="auth_karyawan_id" required>
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

                    <select class="form-select form-select-lg" name="barang_id" id="auth_barang_id" required>
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
                            <label class="form-label small fw-semibold text-muted mb-1" for="auth_jam_mulai">
                                <i class="ti ti-clock-play text-primary me-1"></i> Jam Mulai (WIB)
                            </label>
                            <input type="time" class="form-control form-control-lg bg-white" name="jam_mulai"
                                id="auth_jam_mulai" value="{{ old('jam_mulai') }}" style="cursor: pointer;">
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-muted mb-1" for="auth_jam_selesai">
                                <i class="ti ti-clock-stop text-danger me-1"></i> Jam Selesai (WIB)
                            </label>
                            <input type="time" class="form-control form-control-lg bg-white" name="jam_selesai"
                                id="auth_jam_selesai" value="{{ old('jam_selesai') }}" style="cursor: pointer;">
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

            // Buka timepicker popup secara langsung saat input jam mulai / selesai diklik
            $('#auth_jam_mulai, #auth_jam_selesai').on('click focus', function() {
                try {
                    if (typeof this.showPicker === 'function') {
                        this.showPicker();
                    }
                } catch (e) {
                    // Fallback
                }
            });

            $('#auth_jam_mulai, #auth_jam_selesai').on('input change blur keyup', hitungDurasi);
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
