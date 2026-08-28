<div class="dt-responsive table-responsive">
    <table id="barang-table" class="table table-striped table-bordered nowrap">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Lokasi Subcon</th>
                <th class="no-export">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($barang as $item)
                <tr class="text-center {{ !$item->is_active ? 'inactive-row' : '' }}"
                    style="{{ !$item->is_active ? 'background-color: #ffeef0 !important; color: #b02a37 !important;' : '' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-bold">{{ $item->kode_barang }}</td>
                    <td class="text-start">{{ $item->nama_barang }}</td>
                    <td>
                        <span class="badge bg-secondary-subtle text-dark border px-2 py-1">
                            {{ $item->satuan ?? 'PCS' }}
                        </span>
                    </td>
                    <td>
                        @if ($item->lokasiSubcon)
                            <span class="badge bg-primary-subtle text-primary border">
                                <i class="ti ti-building me-1"></i>{{ $item->lokasiSubcon->nama_lokasi }}
                            </span>
                        @else
                            <span class="badge bg-light text-muted border">Semua / Umum</span>
                        @endif
                    </td>
                    <td class="no-export">
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm btn-edit-barang" data-bs-toggle="modal"
                                data-bs-target="#update_barang"
                                data-id="{{ $item->id }}"
                                data-kode="{{ $item->kode_barang }}"
                                data-nama="{{ $item->nama_barang }}"
                                data-satuan="{{ $item->satuan ?? 'PCS' }}"
                                data-lokasi_subcon_id="{{ $item->lokasi_subcon_id }}"
                                title="Edit Barang">
                                <i class="ti ti-pencil"></i>
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-toggle-barang {{ $item->is_active ? 'btn-danger' : 'btn-success' }}"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama_barang }}"
                                data-status="{{ $item->is_active }}"
                                title="{{ $item->is_active ? 'Nonaktifkan Barang' : 'Aktifkan Barang' }}">
                                <i class="ti {{ $item->is_active ? 'ti-box-off' : 'ti-box' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Data barang kosong</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3 text-muted ps-2"
        style="font-size: 0.875rem; border-left: 3px solid #ffccd5; background-color: #fff9fa; padding: 6px 12px; border-radius: 4px;">
        <span
            style="display: inline-block; width: 12px; height: 12px; background-color: #ffeef0; border: 1px solid #ffccd5; vertical-align: middle; margin-right: 5px; border-radius: 2px;"></span>
        <strong>Note:</strong> Baris berwarna merah muda menandakan Barang Nonaktif.
    </div>
</div>

@push('scripts')
    <script>
        $('#barang-table').DataTable({
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
        $(document).on('click', '.btn-edit-barang', function() {
            let id = $(this).data('id');
            $('#formUpdateBarang').attr('action', "{{ url('master-barang') }}/" + id);
            $('#kode_barang_update').val($(this).data('kode'));
            $('#nama_barang_update').val($(this).data('nama'));
            $('#satuan_update').val($(this).data('satuan'));
            $('#lokasi_subcon_id_update').val($(this).data('lokasi_subcon_id') || '');
        });

        // Toggle status
        $(document).on('click', '.btn-toggle-barang', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let currentStatus = $(this).data('status');
            let actionText = currentStatus == 1 ? 'menonaktifkan' : 'mengaktifkan';

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan ${actionText} barang ${nama}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: currentStatus == 1 ? '#d33' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master-barang') }}/" + id + "/toggle-status",
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
