{{-- Modal Tambah Lokasi Subcon --}}
<div class="modal fade" id="tambah_lokasi" tabindex="-1" aria-labelledby="tambahLokasiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahLokasiLabel">Tambah Lokasi Subcon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('lokasi.store') }}">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_lokasi" placeholder="Masukkan Nama Lokasi" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3" placeholder="Masukkan Alamat (opsional)"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
