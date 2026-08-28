{{-- Modal Update Barang --}}
<div class="modal fade" id="update_barang" tabindex="-1" aria-labelledby="updateBarangLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateBarangLabel">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="formUpdateBarang">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_barang" id="kode_barang_update" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_barang" id="nama_barang_update" required>
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
