{{-- Modal Tambah Pengerjaan --}}
<div class="modal fade" id="tambah_pengerjaan" tabindex="-1" aria-labelledby="tambahPengerjaanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPengerjaanLabel">Tambah Pengerjaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('pengerjaan.store') }}">
                @csrf
                <div class="modal-body">

                    {{-- Admin: pilih karyawan --}}
                    @if (auth()->user()->is_admin)
                        <div class="mb-3">
                            <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                            <select class="form-select" name="karyawan_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawanList as $k)
                                    <option value="{{ $k->id }}">{{ $k->no_karyawan }} - {{ $k->nama_karyawan }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Barang <span class="text-danger">*</span></label>
                        <select class="form-select" name="barang_id" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barangList as $b)
                                <option value="{{ $b->id }}">{{ $b->kode_barang }} - {{ $b->nama_barang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi Subcon <span class="text-danger">*</span></label>
                        <select class="form-select" name="lokasi_subcon_id" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($lokasiList as $l)
                                <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="jumlah" min="1" placeholder="Masukkan jumlah pengerjaan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="2" placeholder="Keterangan (opsional)"></textarea>
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
