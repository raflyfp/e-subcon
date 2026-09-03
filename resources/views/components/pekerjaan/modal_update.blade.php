{{-- Modal Update Pekerjaan --}}
<div class="modal fade" id="update_pekerjaan" tabindex="-1" aria-labelledby="updatePekerjaanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePekerjaanLabel">
                    <i class="ti ti-pencil me-2 text-warning"></i>Edit Pekerjaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="formUpdatePekerjaan">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_pekerjaan" id="nama_pekerjaan_update" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
