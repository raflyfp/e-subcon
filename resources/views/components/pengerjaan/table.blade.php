<div class="dt-responsive table-responsive">
    <table id="pengerjaan-table" class="table table-striped table-bordered nowrap">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Tanggal</th>
                @if (auth()->user()->is_admin)
                    <th>Karyawan</th>
                @endif
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Lokasi</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th class="no-export">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengerjaan as $item)
                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    @if (auth()->user()->is_admin)
                        <td>{{ $item->nama_karyawan }}</td>
                    @endif
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->nama_lokasi }}</td>
                    <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="text-start">{{ $item->keterangan ?? '-' }}</td>
                    <td class="no-export">
                        <button type="button" class="btn btn-danger btn-sm btn-hapus-pengerjaan"
                            data-id="{{ $item->id }}"
                            title="Hapus Pengerjaan">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ auth()->user()->is_admin ? 9 : 8 }}" class="text-center">
                        Belum ada data pengerjaan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        $('#pengerjaan-table').DataTable({
            pageLength: 20,
            info: false,
            dom: 'Bfrtip',
            order: [[1, 'desc']],
            buttons: [
                {
                    extend: 'excelHtml5',
                    filename: function() {
                        return 'Data_Pengerjaan_' + new Date().toISOString().slice(0, 10);
                    },
                    exportOptions: { columns: ':not(.no-export)' },
                    footer: true
                },
                {
                    extend: 'colvis',
                    columns: ':not(.d-none)'
                }
            ]
        });

        // Hapus pengerjaan
        $(document).on('click', '.btn-hapus-pengerjaan', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data pengerjaan akan dihapus permanen',
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
