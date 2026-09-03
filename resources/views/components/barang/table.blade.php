<div class="dt-responsive table-responsive">
    <table id="barang-table" class="table table-striped table-bordered nowrap">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jenis Pekerjaan</th>
                <th>Satuan</th>
                <th>Lokasi Subcon</th>
                <th class="no-export text-center" style="width: 90px; min-width: 90px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barang as $item)
                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td class="text-start">{{ $item->nama_barang }}</td>
                    <td>{{ $item->pekerjaan->nama_pekerjaan ?? $item->jenis_pekerjaan ?? '-' }}</td>
                    <td>{{ $item->satuan ?? 'PCS' }}</td>
                    <td>{{ $item->lokasiSubcon->nama_lokasi ?? 'Semua / Umum' }}</td>
                    <td class="no-export text-center">
                        <div class="d-inline-flex gap-2 justify-content-center align-items-center">
                            @if (auth()->user()->canAccess('master_barang.edit'))
                                <button type="button" class="btn btn-warning btn-sm d-inline-flex align-items-center justify-content-center shadow-sm btn-edit-barang"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#update_barang"
                                    data-id="{{ $item->id }}"
                                    data-kode="{{ $item->kode_barang }}"
                                    data-nama="{{ $item->nama_barang }}"
                                    data-pekerjaan_id="{{ $item->pekerjaan_id }}"
                                    data-satuan="{{ $item->satuan ?? 'PCS' }}"
                                    data-lokasi_subcon_id="{{ $item->lokasi_subcon_id }}"
                                    title="Edit Barang">
                                    <i class="ti ti-edit fs-6"></i>
                                </button>
                            @endif

                            @if (auth()->user()->canAccess('master_barang.toggle'))
                                <button type="button"
                                    class="btn btn-sm d-inline-flex align-items-center justify-content-center shadow-sm btn-toggle-barang {{ $item->is_active ? 'btn-danger' : 'btn-success' }}"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_barang }}"
                                    data-status="{{ $item->is_active }}"
                                    title="{{ $item->is_active ? 'Nonaktifkan Barang' : 'Aktifkan Barang' }}">
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
        $('#barang-table').DataTable({
            pageLength: 15,
            info: false,
            dom: 'Bfrtip',
            language: {
                emptyTable: "Data barang belum tersedia",
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
        $(document).on('click', '.btn-edit-barang', function() {
            let id = $(this).data('id');
            $('#formUpdateBarang').attr('action', "{{ url('master-barang') }}/" + id);
            $('#kode_barang_update').val($(this).data('kode'));
            $('#nama_barang_update').val($(this).data('nama'));
            $('#pekerjaan_id_update').val($(this).data('pekerjaan_id') || '');
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
