{{-- Modal Tambah Karyawan --}}
<div class="modal fade" id="tambah_karyawan" tabindex="-1" aria-labelledby="tambahKaryawanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahKaryawanLabel">
                    <i class="ti ti-user-plus text-primary me-2"></i>Tambah Karyawan Subcon
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('karyawan.store') }}" id="formTambahKaryawan">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Karyawan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_karyawan" placeholder="Masukkan Nama Lengkap Karyawan" value="{{ old('nama_karyawan') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. Karyawan / NIK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_karyawan" placeholder="Contoh: KRY-001" value="{{ old('no_karyawan') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penempatan Subcon <span class="text-danger">*</span></label>
                        <select class="form-select" name="lokasi_subcon_id" required>
                            <option value="">-- Pilih Lokasi Subcon --</option>
                            @foreach ($subconList as $s)
                                <option value="{{ $s->id }}" {{ old('lokasi_subcon_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Karyawan akan muncul pada formulir pengerjaan akun subcon ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. Telepon / WhatsApp</label>
                        <input type="text" class="form-control" name="telepon" placeholder="Contoh: 081234567890" value="{{ old('telepon') }}">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i> Simpan Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
