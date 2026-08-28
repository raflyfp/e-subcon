{{-- Modal Tambah Barang --}}
<div class="modal fade" id="tambah_barang" tabindex="-1" aria-labelledby="tambahBarangLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahBarangLabel">
                    <i class="ti ti-box me-2 text-primary"></i>Tambah Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('barang.store') }}">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_barang" placeholder="Contoh: BRG-001 / PART-A" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_barang" placeholder="Contoh: Casing Filter Udara" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="satuan" placeholder="Contoh: PCS, UNIT, LEMBAR, SET, KG, BOX" value="PCS" required>
                        <small class="text-muted">Unit pengukuran untuk pengerjaan barang ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penempatan Lokasi Subcon</label>
                        <select class="form-select" name="lokasi_subcon_id">
                            <option value="">-- Tanpa Lokasi Subcon (Umum) --</option>
                            @foreach ($subconList as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_lokasi }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Barang ini akan ditugaskan dan dapat dikerjakan oleh subcon yang dipilih.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
