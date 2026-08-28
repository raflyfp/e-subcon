{{-- Formulir Pengerjaan Barang Bergaya Google Form --}}
<div class="google-form-container mx-auto" style="max-width: 820px;">

    {{-- Form Header Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative gf-header-card">
        <div class="gf-top-stripe" style="height: 10px; background: linear-gradient(90deg, #4f46e5 0%, #3b82f6 50%, #06b6d4 100%);"></div>
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">
                    <i class="ti ti-clipboard-list me-1"></i> E-Subcon Form
                </span>
                <span class="badge bg-success-subtle text-success fw-medium px-3 py-2 rounded-pill">
                    <i class="ti ti-circle-check me-1"></i> Aktif
                </span>
            </div>
            
            <h2 class="fw-bold text-dark mb-2">Formulir Pengerjaan Barang</h2>
            <p class="text-muted fs-6 mb-4">
                Silakan isi data hasil pengerjaan barang di bawah ini dengan lengkap dan benar untuk pencatatan sistem subcon.
            </p>

            <hr class="text-secondary opacity-25 mb-3">

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 bg-light rounded-3 border">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-s bg-primary text-white rounded-circle">
                        <i class="ti ti-user-check"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Tercatat sebagai:</small>
                        <strong class="text-dark">{{ auth()->user()->name }}</strong>
                        @if (!auth()->user()->is_admin && auth()->user()->karyawan)
                            <span class="badge bg-secondary-subtle text-secondary ms-1">{{ auth()->user()->karyawan->no_karyawan }}</span>
                        @elseif(auth()->user()->is_admin)
                            <span class="badge bg-danger-subtle text-danger ms-1">Administrator</span>
                        @endif
                    </div>
                </div>
                <div class="text-danger small fw-semibold">
                    <i class="ti ti-asterisk me-1"></i> Menunjukkan pertanyaan yang wajib diisi
                </div>
            </div>
        </div>
    </div>

    {{-- Form Body --}}
    <form method="POST" action="{{ route('pengerjaan.store') }}" id="formPengerjaanBarang">
        @csrf

        {{-- 1. Karyawan (Jika Admin) --}}
        @if (auth()->user()->is_admin)
            <div class="card border-0 shadow-sm rounded-4 mb-4 gf-card">
                <div class="card-body p-4 p-md-4">
                    <div class="mb-2">
                        <label for="gf_karyawan_id" class="fw-bold text-dark fs-6 d-flex align-items-center gap-1">
                            1. Karyawan yang Mengerjakan <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted small mb-3">Pilih nama karyawan yang mengerjakan atau menyelesaikan tugas barang ini.</p>
                    </div>

                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="ti ti-user"></i>
                        </span>
                        <select class="form-select form-select-lg border-start-0 ps-0" id="gf_karyawan_id" name="karyawan_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach ($karyawanList as $k)
                                <option value="{{ $k->id }}" {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->no_karyawan }} — {{ $k->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('karyawan_id')
                        <div class="text-danger small mt-2"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endif

        {{-- 2. Barang --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 gf-card">
            <div class="card-body p-4 p-md-4">
                <div class="mb-2">
                    <label for="gf_barang_id" class="fw-bold text-dark fs-6 d-flex align-items-center gap-1">
                        {{ auth()->user()->is_admin ? '2.' : '1.' }} Barang yang Dikerjakan <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-3">Pilih jenis kode & nama barang yang sedang / selesai dikerjakan.</p>
                </div>

                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="ti ti-package"></i>
                    </span>
                    <select class="form-select form-select-lg border-start-0 ps-0" id="gf_barang_id" name="barang_id" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangList as $b)
                            <option value="{{ $b->id }}" {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                                [{{ $b->kode_barang }}] {{ $b->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('barang_id')
                    <div class="text-danger small mt-2"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- 3. Lokasi Subcon --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 gf-card">
            <div class="card-body p-4 p-md-4">
                <div class="mb-2">
                    <label for="gf_lokasi_subcon_id" class="fw-bold text-dark fs-6 d-flex align-items-center gap-1">
                        {{ auth()->user()->is_admin ? '3.' : '2.' }} Lokasi Subcon <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-3">Pilih stasiun kerja atau lokasi subcon tempat pengerjaan berlangsung.</p>
                </div>

                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="ti ti-map-pin"></i>
                    </span>
                    <select class="form-select form-select-lg border-start-0 ps-0" id="gf_lokasi_subcon_id" name="lokasi_subcon_id" required>
                        <option value="">-- Pilih Lokasi Subcon --</option>
                        @foreach ($lokasiList as $l)
                            <option value="{{ $l->id }}" {{ old('lokasi_subcon_id') == $l->id ? 'selected' : '' }}>
                                {{ $l->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('lokasi_subcon_id')
                    <div class="text-danger small mt-2"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- 4. Tanggal Pengerjaan --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 gf-card">
            <div class="card-body p-4 p-md-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="gf_tanggal" class="fw-bold text-dark fs-6 d-flex align-items-center gap-1 mb-0">
                        {{ auth()->user()->is_admin ? '4.' : '3.' }} Tanggal Pengerjaan <span class="text-danger">*</span>
                    </label>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1" onclick="setTodayDate()">
                        <i class="ti ti-calendar-event me-1"></i> Hari Ini
                    </button>
                </div>
                <p class="text-muted small mb-3">Tanggal saat pengerjaan barang dilaksanakan.</p>

                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="ti ti-calendar"></i>
                    </span>
                    <input type="date" class="form-control form-control-lg border-start-0 ps-0" id="gf_tanggal" name="tanggal"
                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                @error('tanggal')
                    <div class="text-danger small mt-2"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- 5. Jumlah Unit Selesai --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 gf-card">
            <div class="card-body p-4 p-md-4">
                <div class="mb-2">
                    <label for="gf_jumlah" class="fw-bold text-dark fs-6 d-flex align-items-center gap-1">
                        {{ auth()->user()->is_admin ? '5.' : '4.' }} Jumlah Barang Selesai (Unit) <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-3">Kuantitas total unit produk yang berhasil diselesaikan.</p>
                </div>

                <div class="input-group input-group-lg mb-2">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="ti ti-numbers"></i>
                    </span>
                    <input type="number" class="form-control form-control-lg border-start-0 ps-0 fw-bold text-primary"
                        id="gf_jumlah" name="jumlah" min="1" step="1"
                        placeholder="Contoh: 50" value="{{ old('jumlah') }}" required>
                    <span class="input-group-text bg-light fw-medium text-muted">Unit / Pcs</span>
                </div>

                {{-- Quick Step Buttons --}}
                <div class="d-flex gap-2 flex-wrap mt-2">
                    <small class="text-muted align-self-center me-1">Tambah cepat:</small>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1" onclick="addQuantity(10)">+10</button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1" onclick="addQuantity(25)">+25</button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1" onclick="addQuantity(50)">+50</button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1" onclick="addQuantity(100)">+100</button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-danger" onclick="resetQuantity()">Reset</button>
                </div>

                @error('jumlah')
                    <div class="text-danger small mt-2"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- 6. Keterangan --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 gf-card">
            <div class="card-body p-4 p-md-4">
                <div class="mb-2">
                    <label for="gf_keterangan" class="fw-bold text-dark fs-6 d-flex align-items-center gap-1">
                        {{ auth()->user()->is_admin ? '6.' : '5.' }} Catatan / Keterangan Tambahan
                    </label>
                    <p class="text-muted small mb-3">Tuliskan keterangan opsional jika terdapat kondisi khusus atau catatan pengerjaan.</p>
                </div>

                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted align-items-start pt-3">
                        <i class="ti ti-notes"></i>
                    </span>
                    <textarea class="form-control border-start-0 ps-0" id="gf_keterangan" name="keterangan" rows="3"
                        placeholder="Ketik keterangan atau catatan pengerjaan di sini (opsional)...">{{ old('keterangan') }}</textarea>
                </div>
                @error('keterangan')
                    <div class="text-danger small mt-2"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Form Actions Card --}}
        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-body p-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                <button type="submit" class="btn btn-primary btn-lg px-4 py-3 rounded-pill fw-bold shadow-sm w-100 w-sm-auto d-flex align-items-center justify-content-center gap-2">
                    <i class="ti ti-send"></i> Kirim Data Pengerjaan
                </button>

                <button type="button" class="btn btn-link text-secondary text-decoration-none d-flex align-items-center gap-1" onclick="clearGoogleForm()">
                    <i class="ti ti-rotate-2"></i> Kosongkan Formulir
                </button>
            </div>
        </div>

    </form>
</div>

<style>
    .gf-card {
        border-left: 4px solid transparent !important;
        transition: all 0.25s ease-in-out;
    }
    .gf-card:focus-within {
        border-left: 4px solid #4f46e5 !important;
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.12) !important;
        transform: translateY(-2px);
    }
    .gf-card label {
        letter-spacing: -0.01em;
    }
    .google-form-container .form-control:focus,
    .google-form-container .form-select:focus {
        border-color: #4f46e5;
        box-shadow: none;
    }
</style>

@push('scripts')
    <script>
        function setTodayDate() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('gf_tanggal').value = today;
        }

        function addQuantity(amount) {
            const input = document.getElementById('gf_jumlah');
            let current = parseInt(input.value) || 0;
            input.value = current + amount;
            input.dispatchEvent(new Event('input'));
        }

        function resetQuantity() {
            const input = document.getElementById('gf_jumlah');
            input.value = '';
            input.dispatchEvent(new Event('input'));
        }

        function clearGoogleForm() {
            Swal.fire({
                title: 'Kosongkan formulir?',
                text: 'Semua isian formulir yang belum dikirim akan dihapus.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formPengerjaanBarang').reset();
                    setTodayDate();
                }
            });
        }
    </script>
@endpush
