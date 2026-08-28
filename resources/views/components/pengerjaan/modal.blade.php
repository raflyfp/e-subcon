{{-- Modal Tambah Pengerjaan Barang --}}
<div class="modal fade" id="tambah_pengerjaan" tabindex="-1" aria-labelledby="tambahPengerjaanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-bottom-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="tambahPengerjaanLabel">
                        <i class="ti ti-plus-circle text-primary me-2"></i>Tambah Pengerjaan Barang
                    </h5>
                    <p class="text-muted small mb-0">Catat pengerjaan barang baru ke dalam sistem</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('pengerjaan.store') }}">
                @csrf
                <div class="modal-body p-4">

                    {{-- Admin: pilih karyawan --}}
                    @if (auth()->user()->is_admin)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                            <select class="form-select" name="karyawan_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawanList as $k)
                                    <option value="{{ $k->id }}">{{ $k->no_karyawan }} - {{ $k->nama_karyawan }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Barang <span class="text-danger">*</span></label>
                                <select class="form-select" name="barang_id" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach ($barangList as $b)
                                        <option value="{{ $b->id }}">[{{ $b->kode_barang }}] {{ $b->nama_barang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Lokasi Subcon <span class="text-danger">*</span></label>
                                <select class="form-select" name="lokasi_subcon_id" required>
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach ($lokasiList as $l)
                                        <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Pengerjaan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jumlah Selesai (Unit) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="jumlah" min="1" placeholder="Masukkan jumlah unit" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Catatan / Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="2" placeholder="Keterangan pengerjaan (opsional)"></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="ti ti-check me-1"></i> Simpan Pengerjaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
