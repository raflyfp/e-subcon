{{-- Modal Update Lokasi Subcon --}}
<div class="modal fade" id="update_lokasi" tabindex="-1" aria-labelledby="updateLokasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateLokasiLabel">
                    <i class="ti ti-edit text-primary me-2"></i>Edit Lokasi Subcon & Akun Login
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="formUpdateLokasi">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Nama Lokasi Subcon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_lokasi" id="nama_lokasi_update" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea class="form-control" name="alamat" id="alamat_update" rows="2"></textarea>
                        </div>
                    </div>

                    {{-- Akun Login Subcon --}}
                    <div class="p-3 bg-light rounded border mb-3">
                        <h6 class="fw-bold mb-2 text-dark"><i class="ti ti-lock me-1 text-primary"></i>Akun Login Subcon</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Username Login</label>
                                <input type="text" class="form-control" name="username" id="username_update" placeholder="Username login subcon">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Ganti Password</label>
                                <input type="password" class="form-control" name="password" id="password_update" placeholder="Kosongkan jika tidak diubah">
                            </div>
                        </div>
                    </div>

                    {{-- Pilihan Barang yang Dikerjakan --}}
                    <div class="p-3 bg-light rounded border">
                        <h6 class="fw-bold mb-2 text-dark"><i class="ti ti-box me-1 text-primary"></i>Daftar Barang yang Dikerjakan</h6>
                        <p class="text-muted small mb-2">Barang yang diceklis dapat dipilih saat pengisian form pengerjaan di subcon ini:</p>
                        
                        <div class="row g-2" style="max-height: 180px; overflow-y: auto;">
                            @foreach ($barangList as $b)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input chk-barang-update" type="checkbox" name="barang_ids[]" value="{{ $b->id }}" id="brg_edit_{{ $b->id }}">
                                        <label class="form-check-label small" for="brg_edit_{{ $b->id }}">
                                            <strong>[{{ $b->kode_barang }}]</strong> {{ $b->nama_barang }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i> Update Subcon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
