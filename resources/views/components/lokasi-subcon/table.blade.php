<div class="dt-responsive table-responsive">
    <table id="lokasi-table" class="table table-striped table-bordered nowrap">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama Subcon</th>
                <th>Akun Login (Username)</th>
                <th>Alamat</th>
                <th class="no-export text-center" style="width: 90px; min-width: 90px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lokasi as $item)
                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-start">{{ $item->nama_lokasi }}</td>
                    <td>{{ $item->user?->username ?? '-' }}</td>
                    <td class="text-start">{{ $item->alamat ?? '-' }}</td>
                    <td class="no-export text-center">
                        <div class="d-inline-flex gap-2 justify-content-center align-items-center">
                            @if (auth()->user()->canAccess('master_lokasi_subcon.edit'))
                                <button type="button" class="btn btn-warning btn-sm d-inline-flex align-items-center justify-content-center shadow-sm btn-edit-lokasi"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#update_lokasi"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_lokasi }}"
                                    data-alamat="{{ $item->alamat }}"
                                    data-username="{{ $item->user?->username ?? '' }}"
                                    title="Edit Lokasi">
                                    <i class="ti ti-edit fs-6"></i>
                                </button>
                            @endif

                            @if (auth()->user()->canAccess('master_lokasi_subcon.toggle'))
                                <button type="button"
                                    class="btn btn-sm d-inline-flex align-items-center justify-content-center shadow-sm btn-toggle-lokasi {{ $item->is_active ? 'btn-danger' : 'btn-success' }}"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_lokasi }}"
                                    data-status="{{ $item->is_active }}"
                                    title="{{ $item->is_active ? 'Nonaktifkan Lokasi' : 'Aktifkan Lokasi' }}">
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
        $('#lokasi-table').DataTable({
            pageLength: 15,
            info: false,
            dom: 'Bfrtip',
            language: {
                emptyTable: "Data lokasi subcon belum tersedia",
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
