<div class="dt-responsive table-responsive">
    <table id="lokasi-table" class="table table-striped table-bordered nowrap">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama Subcon</th>
                <th>Akun Login (Username)</th>
                <th>Alamat</th>
                <th>Barang Terdaftar</th>
                <th class="no-export">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lokasi as $item)
                <tr class="text-center {{ !$item->is_active ? 'inactive-row' : '' }}"
                    style="{{ !$item->is_active ? 'background-color: #ffeef0 !important; color: #b02a37 !important;' : '' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-bold text-start">{{ $item->nama_lokasi }}</td>
                    <td>
                        @if ($item->user)
                            <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                                <i class="ti ti-user me-1"></i>{{ $item->user->username }}
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border">Belum ada akun</span>
                        @endif
                    </td>
                    <td class="text-start">{{ $item->alamat ?? '-' }}</td>
                    <td>
                        <span class="badge bg-info-subtle text-info border">
                            {{ $item->barang->count() }} Barang
                        </span>
                    </td>
                    <td class="no-export">
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm btn-edit-lokasi" data-bs-toggle="modal"
                                data-bs-target="#update_lokasi"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama_lokasi }}"
                                data-alamat="{{ $item->alamat }}"
                                data-username="{{ $item->user?->username ?? '' }}"
                                title="Edit Lokasi">
                                <i class="ti ti-pencil"></i>
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-toggle-lokasi {{ $item->is_active ? 'btn-danger' : 'btn-success' }}"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama_lokasi }}"
                                data-status="{{ $item->is_active }}"
                                title="{{ $item->is_active ? 'Nonaktifkan Lokasi' : 'Aktifkan Lokasi' }}">
                                <i class="ti {{ $item->is_active ? 'ti-map-pin-off' : 'ti-map-pin' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Data lokasi subcon kosong</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3 text-muted ps-2"
        style="font-size: 0.875rem; border-left: 3px solid #ffccd5; background-color: #fff9fa; padding: 6px 12px; border-radius: 4px;">
        <span
            style="display: inline-block; width: 12px; height: 12px; background-color: #ffeef0; border: 1px solid #ffccd5; vertical-align: middle; margin-right: 5px; border-radius: 2px;"></span>
        <strong>Note:</strong> Baris berwarna merah muda menandakan Lokasi Nonaktif.
    </div>
</div>

@push('scripts')
    <script>
        $('#lokasi-table').DataTable({
            pageLength: 15,
            info: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    exportOptions: { columns: ':not(.no-export)' },
                    footer: true
                },
                {
                    extend: 'colvis',
                    columns: ':not(.d-none)'
                }
            ]
        });

        // Edit
        $(document).on('click', '.btn-edit-lokasi', function() {
            let id = $(this).data('id');
            $('#formUpdateLokasi').attr('action', "{{ url('master-lokasi-subcon') }}/" + id);
            $('#nama_lokasi_update').val($(this).data('nama'));
            $('#alamat_update').val($(this).data('alamat'));
            $('#username_update').val($(this).data('username'));
            $('#password_update').val('');
        });

        // Toggle status
        $(document).on('click', '.btn-toggle-lokasi', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let currentStatus = $(this).data('status');
            let actionText = currentStatus == 1 ? 'menonaktifkan' : 'mengaktifkan';

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan ${actionText} lokasi ${nama}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: currentStatus == 1 ? '#d33' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master-lokasi-subcon') }}/" + id + "/toggle-status",
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'PUT' },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success', title: 'Berhasil', text: res.message,
                                    timer: 1500, showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Terjadi kesalahan koneksi.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
