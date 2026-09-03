<div class="dt-responsive table-responsive">
    <table id="pekerjaan-table" class="table table-striped table-bordered nowrap">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama Pekerjaan</th>
                <th>Status</th>
                <th class="no-export text-center" style="width: 90px; min-width: 90px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pekerjaan as $item)
                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-start fw-bold">{{ $item->nama_pekerjaan }}</td>
                    <td>
                        <span class="badge {{ $item->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border px-2 py-1">
                            {{ $item->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </td>
                    <td class="no-export text-center">
                        <div class="d-inline-flex gap-2 justify-content-center align-items-center">
                            @if (auth()->user()->canAccess('master_pekerjaan.edit'))
                                <button type="button" class="btn btn-warning btn-sm d-inline-flex align-items-center justify-content-center shadow-sm btn-edit-pekerjaan"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#update_pekerjaan"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_pekerjaan }}"
                                    title="Edit Pekerjaan">
                                    <i class="ti ti-edit fs-6"></i>
                                </button>
                            @endif

                            @if (auth()->user()->canAccess('master_pekerjaan.toggle'))
                                <button type="button"
                                    class="btn btn-sm d-inline-flex align-items-center justify-content-center shadow-sm btn-toggle-pekerjaan {{ $item->is_active ? 'btn-danger' : 'btn-success' }}"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_pekerjaan }}"
                                    data-status="{{ $item->is_active }}"
                                    title="{{ $item->is_active ? 'Nonaktifkan Pekerjaan' : 'Aktifkan Pekerjaan' }}">
                                    <i class="ti {{ $item->is_active ? 'ti-ban' : 'ti-check' }} fs-6"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Data pekerjaan kosong</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        $('#pekerjaan-table').DataTable({
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
        $(document).on('click', '.btn-edit-pekerjaan', function() {
            let id = $(this).data('id');
            $('#formUpdatePekerjaan').attr('action', "{{ url('master-pekerjaan') }}/" + id);
            $('#nama_pekerjaan_update').val($(this).data('nama'));
        });

        // Toggle status
        $(document).on('click', '.btn-toggle-pekerjaan', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let currentStatus = $(this).data('status');
            let actionText = currentStatus == 1 ? 'menonaktifkan' : 'mengaktifkan';

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan ${actionText} pekerjaan ${nama}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: currentStatus == 1 ? '#d33' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master-pekerjaan') }}/" + id + "/toggle-status",
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
