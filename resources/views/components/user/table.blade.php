<div class="dt-responsive table-responsive">
    <table id="user-table" class="table table-striped table-bordered align-middle">
        <thead>
            <tr class="text-center">
                <th style="width: 50px;">No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Hak Akses</th>
                <th style="width: 90px;">Status</th>
                <th class="no-export text-center" style="width: 90px; min-width: 90px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($user as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <span class="fw-semibold">{{ $item->name }}</span>
                        @if ($item->lokasiSubcon)
                            <small class="text-muted d-block">Lokasi: {{ $item->lokasiSubcon->nama_lokasi }}</small>
                        @endif
                    </td>
                    <td>{{ $item->username }}</td>
                    <td class="text-center">{{ $item->role_label }}</td>
                    <td>
                        @if ($item->role === 'super_admin' || ($item->is_admin && empty($item->permissions)))
                            <span class="text-muted">Semua Menu</span>
                        @elseif (!empty($item->permissions) && is_array($item->permissions))
                            @php
                                $permNames = array_map(function($k) {
                                    return \App\Models\User::AVAILABLE_PERMISSIONS[$k] ?? $k;
                                }, $item->permissions);
                            @endphp
                            <span>{{ implode(', ', $permNames) }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $item->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </td>
                    <td class="no-export text-center">
                        <div class="d-inline-flex gap-2 justify-content-center align-items-center">
                            @if (auth()->user()->canAccess('master_user.edit'))
                                <button type="button" class="btn btn-warning btn-sm d-inline-flex align-items-center justify-content-center shadow-sm btn-edit-user"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#update_user"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-username="{{ $item->username }}"
                                    data-role="{{ $item->role }}"
                                    data-permissions="{{ json_encode($item->permissions ?? []) }}"
                                    title="Edit User & Hak Akses">
                                    <i class="ti ti-edit fs-6"></i>
                                </button>
                            @endif

                            @if (auth()->user()->canAccess('master_user.toggle') && auth()->id() != $item->id && $item->role !== 'super_admin')
                                <button type="button"
                                    class="btn btn-sm d-inline-flex align-items-center justify-content-center shadow-sm btn-toggle-user {{ $item->is_active ? 'btn-danger' : 'btn-success' }}"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-status="{{ $item->is_active }}"
                                    title="{{ $item->is_active ? 'Nonaktifkan User' : 'Aktifkan User' }}">
                                    <i class="ti {{ $item->is_active ? 'ti-ban' : 'ti-check' }} fs-6"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Data user belum tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#user-table').DataTable({
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

            // Preset default permissions per role (Granular: View, Create, Edit, Toggle/Delete)
            const rolePresets = {
                'super_admin': [
                    'dashboard.view',
                    'master_user.view', 'master_user.create', 'master_user.edit', 'master_user.toggle',
                    'master_karyawan.view', 'master_karyawan.create', 'master_karyawan.edit', 'master_karyawan.toggle',
                    'master_barang.view', 'master_barang.create', 'master_barang.edit', 'master_barang.toggle',
                    'master_pekerjaan.view', 'master_pekerjaan.create', 'master_pekerjaan.edit', 'master_pekerjaan.toggle',
                    'master_lokasi_subcon.view', 'master_lokasi_subcon.create', 'master_lokasi_subcon.edit', 'master_lokasi_subcon.toggle',
                    'formulir_pengerjaan.view',
                    'laporan_subcon.view',
                    'log_report.view'
                ],
                'admin_ppic': [
                    'dashboard.view',
                    'master_barang.view', 'master_barang.create', 'master_barang.edit', 'master_barang.toggle',
                    'master_pekerjaan.view', 'master_pekerjaan.create', 'master_pekerjaan.edit', 'master_pekerjaan.toggle',
                    'formulir_pengerjaan.view',
                    'laporan_subcon.view',
                    'log_report.view'
                ],
                'admin_biasa': [
                    'dashboard.view',
                    'master_karyawan.view', 'master_karyawan.create', 'master_karyawan.edit', 'master_karyawan.toggle',
                    'master_barang.view', 'master_barang.create', 'master_barang.edit', 'master_barang.toggle',
                    'master_pekerjaan.view', 'master_pekerjaan.create', 'master_pekerjaan.edit', 'master_pekerjaan.toggle',
                    'formulir_pengerjaan.view',
                    'laporan_subcon.view',
                    'log_report.view'
                ],
                'user': [
                    'dashboard.view',
                    'formulir_pengerjaan.view',
                    'laporan_subcon.view'
                ],
                'subcon': [
                    'dashboard.view',
                    'formulir_pengerjaan.view',
                    'laporan_subcon.view'
                ]
            };

            // Saat memilih role pada modal Tambah
            $('#select_role_tambah').on('change', function() {
                const role = $(this).val();
                const presets = rolePresets[role] || [];
                $('.check-permission-tambah').each(function() {
                    $(this).prop('checked', presets.includes($(this).val()));
                });
                updateColCheckboxes('tambah');
            });

            // Helper untuk update status checkbox di header kolom
            function updateColCheckboxes(type) {
                ['view', 'create', 'edit', 'delete'].forEach(function(col) {
                    const total = $(`.permission-row-${type} .perm-${col}`).length;
                    const checked = $(`.permission-row-${type} .perm-${col}:checked`).length;
                    $(`.col-check-${type}[data-col="${col}"]`).prop('checked', total > 0 && total === checked);
                });
            }

            // Handler centang seluruh kolom di modal Tambah
            $(document).on('change', '.col-check-tambah', function() {
                const col = $(this).data('col');
                const isChecked = $(this).is(':checked');
                $(`.permission-row-tambah .perm-${col}`).prop('checked', isChecked).trigger('change');
            });

            // Handler centang seluruh kolom di modal Edit
            $(document).on('change', '.col-check-edit', function() {
                const col = $(this).data('col');
                const isChecked = $(this).is(':checked');
                $(`.permission-row-edit .perm-${col}`).prop('checked', isChecked).trigger('change');
            });

            // Relasi baris: Jika Tambah/Ubah/Hapus dicentang, otomatis centang Hak Akses (Lihat)
            $(document).on('change', '.permission-row-tambah .perm-create, .permission-row-tambah .perm-edit, .permission-row-tambah .perm-delete', function() {
                if ($(this).is(':checked')) {
                    $(this).closest('tr').find('.perm-view').prop('checked', true);
                }
                updateColCheckboxes('tambah');
            });

            // Relasi baris: Jika Hak Akses (Lihat) dimatikan, otomatis matikan Tambah/Ubah/Hapus
            $(document).on('change', '.permission-row-tambah .perm-view', function() {
                if (!$(this).is(':checked')) {
                    $(this).closest('tr').find('.perm-create, .perm-edit, .perm-delete').prop('checked', false);
                }
                updateColCheckboxes('tambah');
            });

            // Relasi baris untuk modal Edit
            $(document).on('change', '.permission-row-edit .perm-create, .permission-row-edit .perm-edit, .permission-row-edit .perm-delete', function() {
                if ($(this).is(':checked')) {
                    $(this).closest('tr').find('.perm-view').prop('checked', true);
                }
                updateColCheckboxes('edit');
            });

            $(document).on('change', '.permission-row-edit .perm-view', function() {
                if (!$(this).is(':checked')) {
                    $(this).closest('tr').find('.perm-create, .perm-edit, .perm-delete').prop('checked', false);
                }
                updateColCheckboxes('edit');
            });

            // Tombol Pilih Semua / Reset Tambah
            $('#btn_select_all_tambah').on('click', function() {
                $('.check-permission-tambah').prop('checked', true);
                $('.col-check-tambah').prop('checked', true);
            });
            $('#btn_unselect_all_tambah').on('click', function() {
                $('.check-permission-tambah').prop('checked', false);
                $('.col-check-tambah').prop('checked', false);
            });

            // Tombol Edit User
            $(document).on('click', '.btn-edit-user', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const username = $(this).data('username');
                const role = $(this).data('role') || 'admin_biasa';
                let permissions = $(this).data('permissions');

                if (typeof permissions === 'string') {
                    try {
                        permissions = JSON.parse(permissions);
                    } catch (e) {
                        permissions = [];
                    }
                }

                $('#formUpdateUser').attr('action', "{{ url('master-user') }}/" + id);
                $('#edit_user_name').val(name);
                $('#edit_user_username').val(username);
                $('#edit_user_password').val('');
                $('#edit_user_role').val(role);

                // Jika bukan Super Admin dan Super Admin sudah ada, disable opsi Super Admin di modal edit
                const isThisSuperAdmin = (role === 'super_admin');
                const superAdminExists = {{ !empty($hasSuperAdmin) ? 'true' : 'false' }};
                if (!isThisSuperAdmin && superAdminExists) {
                    $('#opt_super_admin_edit').prop('disabled', true).text('Super Admin (Sudah ada - Maks. 1 Akun)');
                } else {
                    $('#opt_super_admin_edit').prop('disabled', false).text('Super Admin (Akses Penuh Semua Menu & Aksi)');
                }

                // Set checkbox permissions
                $('.check-permission-edit').each(function() {
                    const val = $(this).val();
                    if (role === 'super_admin') {
                        $(this).prop('checked', true);
                    } else if (Array.isArray(permissions)) {
                        const baseModule = val.split('.')[0];
                        const isChecked = permissions.includes(val) || permissions.includes(baseModule);
                        $(this).prop('checked', isChecked);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
                updateColCheckboxes('edit');
            });

            // Saat role diubah pada modal Edit
            $('#edit_user_role').on('change', function() {
                const role = $(this).val();
                const presets = rolePresets[role] || [];
                $('.check-permission-edit').each(function() {
                    $(this).prop('checked', presets.includes($(this).val()));
                });
                updateColCheckboxes('edit');
            });

            // Tombol Pilih Semua / Reset Edit
            $('#btn_select_all_edit').on('click', function() {
                $('.check-permission-edit').prop('checked', true);
                $('.col-check-edit').prop('checked', true);
            });
            $('#btn_unselect_all_edit').on('click', function() {
                $('.check-permission-edit').prop('checked', false);
                $('.col-check-edit').prop('checked', false);
            });

            // Toggle Status Aktif/Nonaktif User
            $(document).on('click', '.btn-toggle-user', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const currentStatus = $(this).data('status');
                const actionText = currentStatus == 1 ? 'menonaktifkan' : 'mengaktifkan';

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Anda akan ${actionText} akun user "${name}"`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: currentStatus == 1 ? '#d33' : '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('master-user') }}/" + id + "/toggle-status",
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
                            error: function(xhr) {
                                let msg = 'Terjadi kesalahan koneksi.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire('Gagal', msg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
