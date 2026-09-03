<div class="modal fade" id="update_user" tabindex="-1" aria-labelledby="modalUpdateUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title fw-bold" id="modalUpdateUserLabel">
                    <i class="ti ti-edit me-2"></i> Edit Data User & Hak Akses
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="formUpdateUser">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="edit_user_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username Login <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" id="edit_user_username" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ganti Password (Opsional)</label>
                            <input type="password" class="form-control" name="password" id="edit_user_password"
                                placeholder="Kosongkan jika tidak diubah" minlength="5">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah password user.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="edit_user_role">Role / Tipe User <span class="text-danger">*</span></label>
                            <select class="form-select" name="role" id="edit_user_role" required>
                                <option value="super_admin">Super Admin (Akses Penuh Semua Menu)</option>
                                <option value="admin_ppic">Admin PPIC (Produksi & Monitoring)</option>
                                <option value="admin_biasa">Admin Biasa (Operasional)</option>
                                <option value="user">User (Pengguna Biasa)</option>
                                <option value="subcon">Subcon (Mitra Subkontraktor)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Section Hak Akses Menu --}}
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <div>
                                <label class="form-label fw-bold mb-0 text-dark">
                                    <i class="ti ti-shield-lock text-primary me-1"></i> Hak Akses Halaman & Menu
                                </label>
                                <p class="text-muted small mb-0">Atur checklist menu yang boleh diakses oleh user ini:</p>
                            </div>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" id="btn_select_all_edit">
                                    <i class="ti ti-check-all me-1"></i> Pilih Semua
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" id="btn_unselect_all_edit">
                                    <i class="ti ti-x me-1"></i> Reset
                                </button>
                            </div>
                        </div>

                        <hr class="my-2 text-muted">

                        <div class="row g-2 pt-2">
                            @foreach ($availablePermissions as $key => $label)
                                <div class="col-md-6">
                                    <div class="form-check p-2 rounded bg-white border border-light-subtle d-flex align-items-center">
                                        <input class="form-check-input check-permission-edit ms-1 me-2" type="checkbox"
                                            name="permissions[]" value="{{ $key }}" id="perm_edit_{{ $key }}">
                                        <label class="form-check-label fw-semibold text-dark mb-0 flex-grow-1" for="perm_edit_{{ $key }}" style="cursor: pointer;">
                                            @if (str_contains($key, 'dashboard'))
                                                <i class="ti ti-dashboard text-primary me-1"></i>
                                            @elseif (str_contains($key, 'master_user'))
                                                <i class="ti ti-users text-danger me-1"></i>
                                            @elseif (str_contains($key, 'master_karyawan'))
                                                <i class="ti ti-user-check text-success me-1"></i>
                                            @elseif (str_contains($key, 'master_barang'))
                                                <i class="ti ti-package text-warning me-1"></i>
                                            @elseif (str_contains($key, 'master_pekerjaan'))
                                                <i class="ti ti-briefcase text-info me-1"></i>
                                            @elseif (str_contains($key, 'master_lokasi'))
                                                <i class="ti ti-map-pin text-danger me-1"></i>
                                            @elseif (str_contains($key, 'formulir'))
                                                <i class="ti ti-edit text-primary me-1"></i>
                                            @elseif (str_contains($key, 'laporan'))
                                                <i class="ti ti-file-analytics text-success me-1"></i>
                                            @else
                                                <i class="ti ti-point text-secondary me-1"></i>
                                            @endif
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning fw-semibold px-4">
                        <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
