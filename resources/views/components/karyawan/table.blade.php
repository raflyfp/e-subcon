<div class="dt-responsive table-responsive">
    <table id="karyawan-table" class="table table-striped table-bordered nowrap">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>No. Karyawan</th>
                <th>Nama Karyawan</th>
                <th>Penempatan Subcon</th>
                <th class="no-export text-center" style="width: 90px; min-width: 90px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($karyawan as $item)
                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->no_karyawan }}</td>
                    <td class="text-start">{{ $item->nama_karyawan }}</td>
                    <td>{{ $item->lokasiSubcon->nama_lokasi ?? '-' }}</td>
                    <td class="no-export text-center">
                        <div class="d-inline-flex gap-2 justify-content-center align-items-center">
                            @if (auth()->user()->canAccess('master_karyawan.edit'))
                                <button type="button" class="btn btn-warning btn-sm d-inline-flex align-items-center justify-content-center shadow-sm btn-edit"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#update_karyawan"
                                    data-id="{{ $item->id }}"
                                    data-no_karyawan="{{ $item->no_karyawan }}"
                                    data-nama="{{ $item->nama_karyawan }}"
                                    data-lokasi_subcon_id="{{ $item->lokasi_subcon_id }}"
                                    title="Edit Karyawan">
                                    <i class="ti ti-edit fs-6"></i>
                                </button>
                            @endif

                            @if (auth()->user()->canAccess('master_karyawan.toggle'))
                                <button type="button"
                                    class="btn btn-sm d-inline-flex align-items-center justify-content-center shadow-sm btn-toggle-status {{ $item->is_active ? 'btn-danger' : 'btn-success' }}"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_karyawan }}"
                                    data-status="{{ $item->is_active }}"
                                    title="{{ $item->is_active ? 'Nonaktifkan Karyawan' : 'Aktifkan Karyawan' }}">
                                    <i class="ti {{ $item->is_active ? 'ti-ban' : 'ti-check' }} fs-6"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        $('#karyawan-table').DataTable({
            pageLength: 15,
            info: false,
            dom: 'Bfrtip',
            language: {
                emptyTable: "Data karyawan belum tersedia",
                zeroRecords: "Tidak ditemukan data yang sesuai"
            },
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
