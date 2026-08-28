<div class="dt-responsive table-responsive">
    <table id="pengerjaan-table" class="table table-striped table-bordered align-middle nowrap w-100">
        <thead>
            <tr class="text-center">
                <th style="width: 50px;">No</th>
                <th>Tanggal</th>
                @if (auth()->user()->is_admin)
                    <th>Karyawan</th>
                @endif
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Lokasi</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th class="no-export" style="width: 70px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengerjaan as $item)
                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    @if (auth()->user()->is_admin)
                        <td class="text-start">{{ $item->nama_karyawan }} ({{ $item->no_karyawan }})</td>
                    @endif
                    <td><span class="badge bg-secondary">{{ $item->kode_barang }}</span></td>
                    <td class="text-start">{{ $item->nama_barang }}</td>
                    <td>{{ $item->nama_lokasi }}</td>
                    <td><strong>{{ number_format($item->jumlah, 0, ',', '.') }}</strong> Unit</td>
                    <td class="text-start">{{ $item->keterangan ?: '-' }}</td>
                    <td class="no-export">
                        <button type="button" class="btn btn-danger btn-sm btn-hapus-pengerjaan"
                            data-id="{{ $item->id }}"
                            title="Hapus">
                            <i class="ti ti-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#pengerjaan-table')) {
                $('#pengerjaan-table').DataTable({
                    pageLength: 20,
                    info: false,
                    dom: 'Bfrtip',
                    order: [[1, 'desc']],
                    language: {
                        emptyTable: "Belum ada data pengerjaan barang",
                        zeroRecords: "Tidak ditemukan data yang sesuai",
                        search: "Cari:"
                    },
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="ti ti-file-spreadsheet me-1"></i> Excel',
                            className: 'btn btn-outline-secondary btn-sm',
                            filename: function() {
                                return 'Data_Pengerjaan_Barang_' + new Date().toISOString().slice(0, 10);
                            },
                            exportOptions: { columns: ':not(.no-export)' },
                            footer: true
                        },
                        {
                            extend: 'colvis',
                            text: '<i class="ti ti-columns me-1"></i> Kolom',
                            className: 'btn btn-outline-secondary btn-sm',
                            columns: ':not(.d-none)'
                        }
                    ]
                });
            }
        });

        // Hapus pengerjaan
        $(document).on('click', '.btn-hapus-pengerjaan', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data pengerjaan barang akan dihapus permanen',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('pengerjaan') }}/" + id,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
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
                        error: function(err) {
                            let msg = 'Terjadi kesalahan koneksi.';
                            if (err.status === 403) {
                                msg = 'Anda tidak memiliki akses untuk menghapus data ini.';
                            }
                            Swal.fire('Gagal', msg, 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
