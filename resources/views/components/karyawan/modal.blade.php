{{-- Modal Tambah Karyawan --}}
<div class="modal fade" id="tambah_karyawan" tabindex="-1" aria-labelledby="tambahKaryawanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahKaryawanLabel">Tambah Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('karyawan.store') }}" id="formTambahKaryawan">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Pilih User <span class="text-danger">*</span></label>
                        <select class="form-select" name="user_id" id="user_id" required>
                            <option value="">-- Pilih User --</option>
                        </select>
                        <small class="text-muted">Hanya user yang belum terdaftar sebagai karyawan</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Karyawan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_karyawan" placeholder="Masukkan No. Karyawan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" class="form-control" name="telepon" placeholder="Masukkan No. Telepon">
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

@push('scripts')
<script>
    // Load user dropdown ketika modal dibuka
    $('#tambah_karyawan').on('show.bs.modal', function() {
        $.ajax({
            url: "{{ url('master-karyawan/data') }}",
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                let userSelect = $('#user_id');
                userSelect.find('option:not(:first)').remove();

                if (data.user && data.user.length > 0) {
                    data.user.forEach(function(u) {
                        userSelect.append(
                            `<option value="${u.id}">${u.username} - ${u.name}</option>`
                        );
                    });
                }
            }
        });
    });
</script>
@endpush
