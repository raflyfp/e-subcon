<div class="dt-responsive table-responsive">
    <table id="karyawan-table" class="table table-striped table-bordered nowrap">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>No. Karyawan</th>
                <th>Nama Karyawan</th>
                <th>Penempatan Subcon</th>
                <th>Telepon</th>
                <th class="no-export">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($karyawan as $item)
                <tr class="text-center {{ $item->is_active == 0 ? 'inactive-row' : '' }}"
                    style="{{ $item->is_active == 0 ? 'background-color: #ffeef0 !important; color: #b02a37 !important;' : '' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->no_karyawan }}</td>
                    <td class="text-start fw-bold">{{ $item->nama_karyawan }}</td>
                    <td>
                        @if ($item->lokasiSubcon)
                            <span class="badge bg-primary-subtle text-primary border">
                                <i class="ti ti-building me-1"></i>{{ $item->lokasiSubcon->nama_lokasi }}
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Belum Ditempatkan</span>
                        @endif
                    </td>
                    <td>{{ $item->telepon ?? '-' }}</td>
                    <td class="no-export">
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm btn-edit" data-bs-toggle="modal"
                                data-bs-target="#update_karyawan"
                                data-id="{{ $item->id }}"
                                data-no_karyawan="{{ $item->no_karyawan }}"
                                data-nama="{{ $item->nama_karyawan }}"
                                data-lokasi_subcon_id="{{ $item->lokasi_subcon_id }}"
                                data-telepon="{{ $item->telepon }}"
                                data-is_active="{{ $item->is_active }}"
                                title="Edit Karyawan">
                                <i class="ti ti-pencil"></i>
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-toggle-status {{ $item->is_active == 1 ? 'btn-danger' : 'btn-success' }}"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama_karyawan }}"
                                data-status="{{ $item->is_active }}"
                                title="{{ $item->is_active == 1 ? 'Nonaktifkan Karyawan' : 'Aktifkan Karyawan' }}">
                                <i class="ti {{ $item->is_active == 1 ? 'ti-user-off' : 'ti-user-check' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Data karyawan kosong</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3 text-muted ps-2"
        style="font-size: 0.875rem; border-left: 3px solid #ffccd5; background-color: #fff9fa; padding: 6px 12px; border-radius: 4px;">
        <span
            style="display: inline-block; width: 12px; height: 12px; background-color: #ffeef0; border: 1px solid #ffccd5; vertical-align: middle; margin-right: 5px; border-radius: 2px;"></span>
        <strong>Note:</strong> Baris berwarna merah muda menandakan Karyawan Nonaktif.
    </div>
</div>

@push('scripts')
    <script>
        $('#karyawan-table').DataTable({
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

        // Edit button click — populate modal
        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            $('#formUpdate').attr('action', "{{ url('master-karyawan') }}/" + id);
            $('#no_karyawan_update').val($(this).data('no_karyawan'));
            $('#nama_update').val($(this).data('nama'));
            $('#lokasi_subcon_update').val($(this).data('lokasi_subcon_id'));
            $('#telepon_update').val($(this).data('telepon'));
        });

        // Reset button state when modal closes
        $('#update_karyawan').on('hidden.bs.modal', function() {
            $('.btn-edit').prop('disabled', false);
        });

        // Toggle status (aktif/nonaktif)
        $(document).on('click', '.btn-toggle-status', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let currentStatus = $(this).data('status');
            let actionText = currentStatus == 1 ? 'menonaktifkan' : 'mengaktifkan';
            let confirmButtonColor = currentStatus == 1 ? '#d33' : '#28a745';

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan ${actionText} karyawan ${nama}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master-karyawan') }}/" + id + "/toggle-status",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'PUT'
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
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
