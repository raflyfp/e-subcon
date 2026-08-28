{{-- Modal Update Barang --}}
<div class="modal fade" id="update_barang" tabindex="-1" aria-labelledby="updateBarangLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateBarangLabel">
                    <i class="ti ti-pencil me-2 text-warning"></i>Edit Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="formUpdateBarang">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_barang" id="kode_barang_update" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_barang" id="nama_barang_update" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="satuan" id="satuan_update" required>
                        <small class="text-muted">Unit pengukuran untuk pengerjaan barang ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penempatan Lokasi Subcon</label>
                        <select class="form-select" name="lokasi_subcon_id" id="lokasi_subcon_id_update">
                            <option value="">-- Tanpa Lokasi Subcon (Umum) --</option>
                            @foreach ($subconList as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_lokasi }}</option>
                            @endforeach
                        </select>
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
