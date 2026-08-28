{{-- Modal Update Karyawan --}}
<div class="modal fade" id="update_karyawan" tabindex="-1" aria-labelledby="updateKaryawanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateKaryawanLabel">
                    <i class="ti ti-edit text-warning me-2"></i>Edit Data Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="formUpdate">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Karyawan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_karyawan" id="nama_update" placeholder="Masukkan Nama Karyawan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. Karyawan / NIK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_karyawan" id="no_karyawan_update" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penempatan Subcon <span class="text-danger">*</span></label>
                        <select class="form-select" name="lokasi_subcon_id" id="lokasi_subcon_update" required>
                            <option value="">-- Pilih Lokasi Subcon --</option>
                            @foreach ($subconList as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Karyawan akan muncul pada formulir pengerjaan akun subcon ini.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i> Update Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
