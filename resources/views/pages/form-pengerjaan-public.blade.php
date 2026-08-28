<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Formulir Pengerjaan Barang — e-Subcon</title>
    <link rel="icon" href="{{ asset('template/dist') }}/assets/images/favicon.png" type="image/x-icon">
    
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/fonts/tabler-icons.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/css/style.css">
    
    <!-- Select2 Searchable Dropdown CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Public Sans', sans-serif;
            color: #334155;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 20px;
        }
        .form-container {
            max-width: 760px;
            margin: 25px auto 40px auto;
            padding: 0 15px;
        }
        .gf-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 18px;
            padding: 22px 24px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .gf-header-card {
            border-top: 6px solid #2563eb;
        }
        .form-label {
            font-size: 1.05rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
            display: block;
        }
        .form-help {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 12px;
        }
        .form-control, .form-select {
            font-size: 1rem;
            padding: 10px 14px;
            border-color: #cbd5e1;
            border-radius: 6px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        /* Select2 Customization for Senior Friendly Readability */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 48px;
            padding: 8px 12px;
            font-size: 1rem;
            border-color: #cbd5e1;
            border-radius: 6px;
        }
        .select2-container--bootstrap-5 .select2-dropdown .select2-search .select2-search__field {
            font-size: 1rem;
            padding: 8px 12px;
        }
        .select2-container--bootstrap-5 .select2-results__option {
            font-size: 1rem;
            padding: 10px 14px;
        }
        .btn-submit {
            background-color: #2563eb;
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 600;
            padding: 12px 32px;
            border-radius: 6px;
            border: none;
            transition: all 0.2s;
        }
        .btn-submit:hover {
            background-color: #1d4ed8;
            color: #ffffff;
        }
        .btn-quick {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #334155;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        .btn-quick:hover {
            background: #e2e8f0;
        }
    </style>
</head>
<body>

<!-- Top Bar with Login Button -->
<div class="top-navbar">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="max-width: 960px;">
        <div class="d-flex align-items-center gap-2">
            <span class="fs-4 fw-bold text-primary">e-Subcon</span>
            <span class="text-muted d-none d-sm-inline">| Pencatatan Pengerjaan Barang</span>
        </div>
        <div>
            @if (auth()->check())
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                    <i class="ti ti-dashboard me-1"></i> Buka Dashboard ({{ auth()->user()->name }})
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                    <i class="ti ti-lock me-1"></i> Login Dashboard
                </a>
            @endif
        </div>
    </div>
</div>

<div class="form-container">

    @if (session('form_success'))
        {{-- Layar Sukses Pengisian (Google Form style) --}}
        <div class="gf-card gf-header-card text-center py-5">
            <div class="mb-3 text-success">
                <i class="ti ti-circle-check" style="font-size: 64px;"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">Pengerjaan Berhasil Dicatat!</h3>
            <p class="text-muted fs-6 mb-4">
                {{ session('form_success') }}<br>
                Terima kasih, data Anda sudah langsung tersimpan ke sistem rekap e-Subcon.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="ti ti-plus me-1"></i> Isi Formulir Lagi
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4 py-2">
                    <i class="ti ti-login me-1"></i> Halaman Login
                </a>
            </div>
        </div>
    @else
        {{-- Header Form --}}
        <div class="gf-card gf-header-card">
            <h2 class="fw-bold text-dark mb-1">Formulir Pengerjaan Barang</h2>
            <p class="text-muted mb-3">
                Silakan cari dan pilih nama karyawan, barang, dan lokasi, lalu masukkan jumlah pengerjaan.
            </p>
            <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                <small class="text-danger fw-semibold">* Menunjukkan pertanyaan yang wajib diisi</small>
                <small class="text-muted"><i class="ti ti-search me-1"></i>Dropdown dapat diketik untuk mencari</small>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger mb-3">
                <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('pengerjaan.store-public') }}" id="publicPengerjaanForm">
            @csrf

            {{-- 1. Nama Karyawan (Searchable) --}}
            <div class="gf-card">
                <label class="form-label" for="karyawan_id">
                    1. Nama Karyawan <span class="text-danger">*</span>
                </label>
                <div class="form-help">Ketik atau pilih nama / nomor karyawan Anda:</div>
                
                <select class="form-select select2-search" name="karyawan_id" id="karyawan_id" data-placeholder="-- Cari Nama atau Nomor Karyawan --" required>
                    <option value=""></option>
                    @foreach ($karyawanList as $k)
                        <option value="{{ $k->id }}" 
                            data-nama="{{ $k->nama_karyawan }}"
                            data-nokaryawan="{{ $k->no_karyawan }}"
                            data-lastlokasi="{{ $k->last_lokasi_id }}"
                            {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_karyawan }} (No: {{ $k->no_karyawan }})
                        </option>
                    @endforeach
                </select>

                <div id="karyawan_info" class="mt-2 text-primary small d-none">
                    <i class="ti ti-user-check me-1"></i> Terpilih: <strong id="karyawan_nama_text"></strong>
                </div>
            </div>

            {{-- 2. Barang (Searchable) --}}
            <div class="gf-card">
                <label class="form-label" for="barang_id">
                    2. Barang yang Dikerjakan <span class="text-danger">*</span>
                </label>
                <div class="form-help">Ketik kode atau nama barang yang sedang/telah dikerjakan:</div>

                <select class="form-select select2-search" name="barang_id" id="barang_id" data-placeholder="-- Cari Kode atau Nama Barang --" required>
                    <option value=""></option>
                    @foreach ($barangList as $b)
                        <option value="{{ $b->id }}"
                            data-kode="{{ $b->kode_barang }}"
                            data-nama="{{ $b->nama_barang }}"
                            {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                            [{{ $b->kode_barang }}] {{ $b->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 3. Lokasi Subcon (Searchable) --}}
            <div class="gf-card">
                <label class="form-label" for="lokasi_subcon_id">
                    3. Lokasi Subcon <span class="text-danger">*</span>
                </label>
                <div class="form-help">Lokasi pengerjaan subcon (otomatis terisi saat memilih nama karyawan):</div>

                <select class="form-select select2-search" name="lokasi_subcon_id" id="lokasi_subcon_id" data-placeholder="-- Pilih / Cari Lokasi Subcon --" required>
                    <option value=""></option>
                    @foreach ($lokasiList as $l)
                        <option value="{{ $l->id }}" {{ old('lokasi_subcon_id') == $l->id ? 'selected' : '' }}>
                            {{ $l->nama_lokasi }}
                        </option>
                    @endforeach
                </select>

                <div id="lokasi_auto_note" class="mt-2 text-success small d-none">
                    <i class="ti ti-check me-1"></i> Lokasi otomatis disesuaikan dengan riwayat kerja Anda.
                </div>
            </div>

            {{-- 4. Tanggal Pengerjaan --}}
            <div class="gf-card">
                <label class="form-label" for="tanggal">
                    4. Tanggal Pengerjaan <span class="text-danger">*</span>
                </label>
                <div class="form-help">Tanggal saat barang dikerjakan:</div>

                <input type="date" class="form-control" name="tanggal" id="tanggal" 
                    value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>

            {{-- 5. Jumlah Selesai --}}
            <div class="gf-card">
                <label class="form-label" for="jumlah">
                    5. Jumlah Barang Selesai (Unit) <span class="text-danger">*</span>
                </label>
                <div class="form-help">Masukkan jumlah unit pengerjaan yang berhasil diselesaikan:</div>

                <div class="input-group mb-2">
                    <input type="number" class="form-control form-control-lg fw-bold text-primary" 
                        name="jumlah" id="jumlah" min="1" step="1" 
                        placeholder="Contoh: 50" value="{{ old('jumlah') }}" required>
                    <span class="input-group-text bg-white fw-bold">Unit / Pcs</span>
                </div>

                <div class="d-flex gap-2 flex-wrap align-items-center mt-2">
                    <small class="text-muted me-1">Tambah cepat:</small>
                    <button type="button" class="btn-quick" onclick="addQty(10)">+10</button>
                    <button type="button" class="btn-quick" onclick="addQty(25)">+25</button>
                    <button type="button" class="btn-quick" onclick="addQty(50)">+50</button>
                    <button type="button" class="btn-quick" onclick="addQty(100)">+100</button>
                    <button type="button" class="btn-quick text-danger" onclick="document.getElementById('jumlah').value=''">Reset</button>
                </div>
            </div>

            {{-- 6. Keterangan --}}
            <div class="gf-card">
                <label class="form-label" for="keterangan">
                    6. Keterangan / Catatan (Opsional)
                </label>
                <div class="form-help">Catatan tambahan bila ada kondisi khusus:</div>

                <input type="text" class="form-control" name="keterangan" id="keterangan" 
                    placeholder="Tulis catatan pengerjaan di sini..." value="{{ old('keterangan') }}">
            </div>

            {{-- Tombol Kirim --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button type="submit" class="btn-submit">
                    <i class="ti ti-send me-1"></i> Kirim Data Pengerjaan
                </button>
                <button type="reset" class="btn btn-link text-secondary text-decoration-none" onclick="resetForm()">
                    Kosongkan formulir
                </button>
            </div>
        </form>

        <div class="text-center mt-4 text-muted small">
            <p class="mb-1">Aplikasi e-Subcon &copy; {{ date('Y') }}</p>
        </div>
    @endif

</div>

<!-- jQuery & Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi Select2 Searchable Dropdown
        $('.select2-search').select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true
        });

        // Auto-fill Lokasi saat Karyawan dipilih
        $('#karyawan_id').on('change', function() {
            const selectedOpt = $(this).find(':selected');
            const lastLokasi = selectedOpt.data('lastlokasi');
            const nama = selectedOpt.data('nama');

            if (nama) {
                $('#karyawan_nama_text').text(nama);
                $('#karyawan_info').removeClass('d-none');
            } else {
                $('#karyawan_info').addClass('d-none');
            }

            if (lastLokasi) {
                $('#lokasi_subcon_id').val(lastLokasi).trigger('change');
                $('#lokasi_auto_note').removeClass('d-none');
            } else {
                $('#lokasi_auto_note').addClass('d-none');
            }
        });

        // Trigger jika sudah ada nilai
        if ($('#karyawan_id').val()) {
            $('#karyawan_id').trigger('change');
        }
    });

    function addQty(n) {
        const el = document.getElementById('jumlah');
        let current = parseInt(el.value) || 0;
        el.value = current + n;
    }

    function resetForm() {
        $('#karyawan_id').val('').trigger('change');
        $('#barang_id').val('').trigger('change');
        $('#lokasi_subcon_id').val('').trigger('change');
        document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
    }
</script>

</body>
</html>
