@extends('layouts.mantis')
@section('title', 'Formulir Pengerjaan Barang')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="content mb-1">Formulir Pengerjaan Barang</h1>
            <p class="text-muted mb-0">Catat hasil pengerjaan barang harian dengan cepat dan akurat</p>
        </div>
        <div>
            <a href="{{ url('laporan-subcon') }}" class="btn btn-outline-primary">
                <i class="ti ti-file-analytics me-1"></i> Buka Laporan Subcon
            </a>
        </div>
    </div>

    {{-- Flat & Simple Google Form Style Card --}}
    <div class="card border mb-4" style="max-width: 800px; margin: 0 auto; border-top: 6px solid #2563eb !important;">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="ti ti-edit text-primary me-2"></i>Formulir Input Pengerjaan Barang
                    </h5>
                    <small class="text-muted">Silakan isi formulir di bawah ini dengan lengkap dan benar</small>
                </div>
                <div>
                    @if (auth()->user()->is_admin)
                        <span class="badge bg-danger-subtle text-danger border">Admin Input</span>
                    @else
                        <span class="badge bg-primary-subtle text-primary border">{{ auth()->user()->name }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form method="POST" action="{{ route('pengerjaan.store') }}" id="formPengerjaanAuth">
                @csrf

                {{-- 1. Karyawan --}}
                @if (auth()->user()->is_admin)
                    <div class="mb-4 p-3 bg-light rounded border">
                        <label class="form-label fw-bold" for="auth_karyawan_id">
                            1. Karyawan yang Mengerjakan <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted small mb-2">Pilih karyawan yang bertanggung jawab atas pengerjaan ini:</p>
                        
                        <select class="form-select form-select-lg" name="karyawan_id" id="auth_karyawan_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach ($karyawanList as $k)
                                <option value="{{ $k->id }}"
                                    data-nama="{{ $k->nama_karyawan }}"
                                    data-nokaryawan="{{ $k->no_karyawan }}"
                                    data-lastlokasi="{{ $k->last_lokasi_id }}"
                                    {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_karyawan }} ({{ $k->no_karyawan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="mb-4 p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted d-block">Nama Karyawan Pelaksana:</small>
                            <strong class="fs-6 text-dark">{{ auth()->user()->name }}</strong>
                            @if (auth()->user()->karyawan)
                                <span class="badge bg-secondary ms-2">{{ auth()->user()->karyawan->no_karyawan }}</span>
                            @endif
                        </div>
                        <span class="text-success small fw-medium">
                            <i class="ti ti-check me-1"></i> Akun Anda Aktif
                        </span>
                    </div>
                @endif

                {{-- 2. Barang --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_barang_id">
                        {{ auth()->user()->is_admin ? '2.' : '1.' }} Barang yang Dikerjakan <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Pilih kode / nama barang yang dikerjakan dari master barang:</p>

                    <select class="form-select form-select-lg" name="barang_id" id="auth_barang_id" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangList as $b)
                            <option value="{{ $b->id }}" {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                                [{{ $b->kode_barang }}] {{ $b->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Lokasi Subcon --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_lokasi_subcon_id">
                        {{ auth()->user()->is_admin ? '3.' : '2.' }} Lokasi Subcon <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Pilih lokasi tempat pengerjaan barang dilakukan:</p>

                    <select class="form-select form-select-lg" name="lokasi_subcon_id" id="auth_lokasi_subcon_id" required>
                        <option value="">-- Pilih Lokasi Subcon --</option>
                        @foreach ($lokasiList as $l)
                            <option value="{{ $l->id }}"
                                {{ old('lokasi_subcon_id', $currentKaryawan?->last_lokasi_id) == $l->id ? 'selected' : '' }}>
                                {{ $l->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Tanggal --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_tanggal">
                        {{ auth()->user()->is_admin ? '4.' : '3.' }} Tanggal Pengerjaan <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Tanggal pengerjaan otomatis terisi hari ini:</p>

                    <input type="date" class="form-control form-control-lg" name="tanggal" id="auth_tanggal"
                        value="{{ date('Y-m-d') }}" readonly style="background-color: #f1f5f9; cursor: not-allowed;">
                    <small class="text-muted mt-2 d-block"><i class="ti ti-lock me-1"></i>Tanggal terkunci otomatis hari ini ({{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}).</small>
                </div>

                {{-- 5. Jumlah --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold" for="auth_jumlah">
                        {{ auth()->user()->is_admin ? '5.' : '4.' }} Jumlah Barang Selesai (Unit) <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-2">Masukkan kuantitas unit produk yang berhasil diselesaikan:</p>

                    <div class="input-group input-group-lg">
                        <input type="number" class="form-control form-control-lg" name="jumlah" id="auth_jumlah"
                            min="1" placeholder="Masukkan jumlah unit (contoh: 50)" value="{{ old('jumlah') }}" required>
                        <span class="input-group-text bg-white fw-bold text-muted">Unit / Pcs</span>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2: Search dulu baru muncul pilihan
            $('#auth_karyawan_id, #auth_barang_id, #auth_lokasi_subcon_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                minimumInputLength: 1,
                language: {
                    inputTooShort: function() {
                        return "Ketik minimal 1 huruf/angka untuk mencari...";
                    },
                    noResults: function() {
                        return "Tidak ditemukan data yang cocok";
                    },
                    searching: function() {
                        return "Mencari data...";
                    }
                }
            });

            // Fokus otomatis ke search field saat dibuka
            $(document).on('select2:open', () => {
                setTimeout(() => {
                    document.querySelector('.select2-search__field')?.focus();
                }, 50);
            });

            // Auto-fill Lokasi saat Karyawan dipilih (Admin)
            $('#auth_karyawan_id').on('change', function() {
                const selectedOpt = $(this).find(':selected');
                const lastLokasi = selectedOpt.data('lastlokasi');
                if (lastLokasi) {
                    $('#auth_lokasi_subcon_id').val(lastLokasi).trigger('change');
                }
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
