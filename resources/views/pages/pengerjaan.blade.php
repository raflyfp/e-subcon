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
                            <i class="ti ti-shield me-1"></i> Admin IT Input
                        </span>
                    @else
                        <span class="badge bg-primary-subtle text-primary border px-3 py-2 fs-6">
                            <i class="ti ti-building me-1"></i> Subcon: <strong>{{ $subcon->nama_lokasi ?? auth()->user()->name }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form method="POST" action="{{ route('pengerjaan.store') }}" id="formPengerjaanAuth">
                @csrf

                {{-- 1. Lokasi Subcon (HANYA DITAMPILKAN JIKA ADMIN) --}}
                @if (auth()->user()->is_admin)
                    <div class="mb-4 p-3 bg-light rounded border">
                        <label class="form-label fw-bold" for="auth_lokasi_subcon_id">
                            1. Lokasi Subcon <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted small mb-2">Pilih lokasi tempat pengerjaan barang dilakukan:</p>

                        <select class="form-select form-select-lg" name="lokasi_subcon_id" id="auth_lokasi_subcon_id" required>
                            <option value="">-- Pilih Lokasi Subcon --</option>
                            @foreach ($lokasiList as $l)
                                <option value="{{ $l->id }}" {{ old('lokasi_subcon_id') == $l->id ? 'selected' : '' }}>
                                    {{ $l->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- 2. Karyawan Pelaksana --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_karyawan_id">
                        {{ auth()->user()->is_admin ? '2.' : '1.' }} Karyawan yang Mengerjakan <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">
                        @if (auth()->user()->is_admin)
                            Pilih karyawan pelaksana pengerjaan barang:
                        @else
                            Pilih nama karyawan dalam subcon <strong>{{ $subcon->nama_lokasi ?? auth()->user()->name }}</strong>:
                        @endif
                    </p>

                    <select class="form-select form-select-lg" name="karyawan_id" id="auth_karyawan_id" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach ($karyawanList as $k)
                            <option value="{{ $k->id }}" {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_karyawan }} ({{ $k->no_karyawan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Barang yang Dikerjakan --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_barang_id">
                        {{ auth()->user()->is_admin ? '3.' : '2.' }} Barang yang Dikerjakan <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Pilih kode / nama barang yang dikerjakan:</p>

                    <select class="form-select form-select-lg" name="barang_id" id="auth_barang_id" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangList as $b)
                            <option value="{{ $b->id }}" data-satuan="{{ $b->satuan ?? 'PCS' }}" {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                                [{{ $b->kode_barang }}] {{ $b->nama_barang }} (Satuan: {{ $b->satuan ?? 'PCS' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Tanggal Pengerjaan --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_tanggal">
                        {{ auth()->user()->is_admin ? '4.' : '3.' }} Tanggal Pengerjaan <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Tanggal pengerjaan otomatis terisi hari ini:</p>

                    <input type="date" class="form-control form-control-lg" name="tanggal" id="auth_tanggal"
                        value="{{ date('Y-m-d') }}" readonly style="background-color: #f1f5f9; cursor: not-allowed;">
                    <small class="text-muted mt-2 d-block"><i class="ti ti-lock me-1"></i>Tanggal terkunci otomatis hari ini
                        ({{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}).</small>
                </div>

                {{-- 5. Jumlah Pengerjaan Selesai (dengan Pilihan Jenis Pekerjaan di Bawahnya) --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_jumlah">
                        {{ auth()->user()->is_admin ? '5.' : '4.' }} Jumlah Barang Selesai <span id="label_satuan_text" class="text-primary">(PCS)</span> <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Masukkan kuantitas barang yang diselesaikan dan pilih jenis pekerjaannya:</p>

                    {{-- 1 Input Jumlah Barang Selesai --}}
                    <div class="input-group input-group-lg mb-3">
                        <input type="number" class="form-control form-control-lg" name="jumlah" id="auth_jumlah"
                            min="1" placeholder="Masukkan jumlah (contoh: 50)" value="{{ old('jumlah') }}"
                            required>
                        <span class="input-group-text bg-white fw-bold text-primary span_satuan_addon" id="span_satuan_addon">PCS</span>
                    </div>

                    {{-- Pilihan Jenis Pekerjaan (Checkbox Tunggal - Awalnya Kosong) --}}
                    <div class="d-flex gap-4 p-3 bg-white rounded border">
                        <div class="form-check">
                            <input class="form-check-input check-single-jenis" type="checkbox" name="jenis_pekerjaan" id="check_folding" value="Folding"
                                {{ old('jenis_pekerjaan') == 'Folding' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="check_folding" style="cursor: pointer;">
                                Folding
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input check-single-jenis" type="checkbox" name="jenis_pekerjaan" id="check_packing" value="Packing"
                                {{ old('jenis_pekerjaan') == 'Packing' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="check_packing" style="cursor: pointer;">
                                Packing
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 6. Keterangan --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_keterangan">
                        {{ auth()->user()->is_admin ? '6.' : '5.' }} Catatan / Keterangan (Opsional)
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
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                            <i class="ti ti-send me-1"></i> Kirim Data Pengerjaan
                        </button>
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

            // Checkbox single-selection (jika salah satu dicentang, yang lain uncheck)
            $('.check-single-jenis').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.check-single-jenis').not(this).prop('checked', false);
                }
            });

            // Validasi submit client side
            $('#formPengerjaanAuth').on('submit', function(e) {
                let qty = parseInt($('#auth_jumlah').val()) || 0;
                let anyChecked = $('.check-single-jenis:checked').length > 0;

                if (qty <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jumlah Kuantitas Kosong',
                        text: 'Masukkan kuantitas barang selesai yang valid (minimal 1).'
                    });
                    $('#auth_jumlah').focus();
                    return false;
                }

                if (!anyChecked) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilihan Kosong',
                        text: 'Silakan centang salah satu jenis pekerjaan (Folding atau Packing).'
                    });
                    return false;
                }
            });

            // Fokus otomatis ke search field saat dibuka
            $(document).on('select2:open', () => {
                setTimeout(() => {
                    document.querySelector('.select2-search__field')?.focus();
                }, 50);
            });
        });
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'OK',
                timer: 2500,
                timerProgressBar: true
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Tutup'
            });
        </script>
    @endif
@endpush
