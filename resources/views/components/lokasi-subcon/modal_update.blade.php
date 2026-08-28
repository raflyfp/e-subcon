{{-- Modal Update Lokasi Subcon --}}
<div class="modal fade" id="update_lokasi" tabindex="-1" aria-labelledby="updateLokasiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateLokasiLabel">Edit Lokasi Subcon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="formUpdateLokasi">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_lokasi" id="nama_lokasi_update" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" id="alamat_update" rows="3"></textarea>
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
