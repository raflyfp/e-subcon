{{-- Modal Update Karyawan --}}
<div class="modal fade" id="update_karyawan" tabindex="-1" aria-labelledby="updateKaryawanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateKaryawanLabel">Edit Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="formUpdate">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Karyawan</label>
                        <input type="text" class="form-control" id="nama_update" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Karyawan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_karyawan" id="no_karyawan_update" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" class="form-control" name="telepon" id="telepon_update">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
