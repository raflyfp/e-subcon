{{-- Modal Tambah Lokasi Subcon --}}
<div class="modal fade" id="tambah_lokasi" tabindex="-1" aria-labelledby="tambahLokasiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahLokasiLabel">
                    <i class="ti ti-building text-primary me-2"></i>Tambah Lokasi Subcon & Akun Login
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('lokasi.store') }}">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lokasi Subcon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_lokasi" placeholder="Contoh: Subcon 1 / Subcon Alpha" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="2" placeholder="Masukkan Alamat Pabrik / Lokasi (opsional)"></textarea>
                    </div>

                    {{-- Akun Login Subcon --}}
                    <div class="p-3 bg-light rounded border mb-3">
                        <h6 class="fw-bold mb-2 text-dark"><i class="ti ti-lock me-1 text-primary"></i>Akun Login Subcon (Wajib)</h6>
                        <small class="text-muted d-block mb-3">1 akun ini akan digunakan oleh seluruh karyawan di subcon ini untuk membuka form pengerjaan.</small>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Username Login <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="username" placeholder="Contoh: subcon1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" placeholder="Minimal 4 karakter" required>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i> Simpan Subcon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
