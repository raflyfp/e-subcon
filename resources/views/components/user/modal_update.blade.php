<div class="modal fade" id="update_user" tabindex="-1" aria-labelledby="modalUpdateUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark py-3 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center" id="modalUpdateUserLabel">
                    <i class="ti ti-edit fs-4 me-2"></i> Edit Data User & Hak Akses
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="formUpdateUser">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-light-subtle">
                    {{-- Form Informasi Pengguna --}}
                    <div class="card border mb-3 shadow-none bg-white">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="edit_user_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark mb-1">Username Login <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="username" id="edit_user_username" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark mb-1">Ganti Password (Opsional)</label>
                                    <input type="password" class="form-control" name="password" id="edit_user_password"
                                        placeholder="Kosongkan jika tidak diubah" minlength="5">
                                    <small class="text-muted" style="font-size: 0.8rem;">Biarkan kosong jika tidak ingin mengubah password user.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark mb-1" for="edit_user_role">Role / Tipe User <span class="text-danger">*</span></label>
                                    <select class="form-select" name="role" id="edit_user_role" required>
                                        <option value="super_admin" id="opt_super_admin_edit">Super Admin (Akses Penuh Semua Menu & Aksi)</option>
                                        <option value="admin_ppic">Admin PPIC (Produksi & Monitoring)</option>
                                        <option value="admin_biasa">Admin Biasa (Operasional)</option>
                                        <option value="user">User (Pengguna Biasa)</option>
                                        <option value="subcon">Subcon (Mitra Subkontraktor)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Matriks Hak Akses Grid Table --}}
                    <div class="card border shadow-none bg-white">
                        <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <span class="fw-bold text-dark fs-6 d-flex align-items-center">
                                    <i class="ti ti-shield-lock text-primary me-2 fs-5"></i> Pengaturan Hak Akses Menu & Aksi
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm py-1 px-3 fw-semibold" id="btn_select_all_edit">
                                    <i class="ti ti-check-all me-1"></i> Pilih Semua
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-3 fw-semibold" id="btn_unselect_all_edit">
                                    <i class="ti ti-x me-1"></i> Reset
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="perm-table-scroll-wrapper" style="max-height: 380px; overflow-y: auto; overflow-x: auto; position: relative;">
                                <table class="table table-hover align-middle mb-0 enterprise-perm-table" style="font-size: 0.88rem; width: 100%; border-collapse: separate; border-spacing: 0;">
                                    <thead>
                                        <tr class="text-center" style="color: #334155;">
                                            <th style="width: 45px; vertical-align: middle; background-color: #eef2f6 !important; position: sticky; top: 0; z-index: 10; border-bottom: 2px solid #cbd5e1; border-right: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0;" class="py-2">No</th>
                                            <th class="text-start py-2 px-3" style="min-width: 260px; vertical-align: middle; background-color: #eef2f6 !important; position: sticky; top: 0; z-index: 10; border-bottom: 2px solid #cbd5e1; border-right: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0;">Menu / Modul</th>
                                            <th style="width: 105px; vertical-align: middle; background-color: #eef2f6 !important; position: sticky; top: 0; z-index: 10; border-bottom: 2px solid #cbd5e1; border-right: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0;" class="py-2">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <span class="fw-bold mb-1">Hak Akses</span>
                                                    <input class="form-check-input col-check-edit mt-0" type="checkbox" data-col="view" title="Pilih Semua Hak Akses">
                                                </div>
                                            </th>
                                            <th style="width: 95px; vertical-align: middle; background-color: #eef2f6 !important; position: sticky; top: 0; z-index: 10; border-bottom: 2px solid #cbd5e1; border-right: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0;" class="py-2">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <span class="fw-bold mb-1">Tambah</span>
                                                    <input class="form-check-input col-check-edit mt-0" type="checkbox" data-col="create" title="Pilih Semua Tambah">
                                                </div>
                                            </th>
                                            <th style="width: 95px; vertical-align: middle; background-color: #eef2f6 !important; position: sticky; top: 0; z-index: 10; border-bottom: 2px solid #cbd5e1; border-right: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0;" class="py-2">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <span class="fw-bold mb-1">Ubah</span>
                                                    <input class="form-check-input col-check-edit mt-0" type="checkbox" data-col="edit" title="Pilih Semua Ubah">
                                                </div>
                                            </th>
                                            <th style="width: 95px; vertical-align: middle; background-color: #eef2f6 !important; position: sticky; top: 0; z-index: 10; border-bottom: 2px solid #cbd5e1; border-right: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0;" class="py-2">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <span class="fw-bold mb-1">Hapus</span>
                                                    <input class="form-check-input col-check-edit mt-0" type="checkbox" data-col="delete" title="Pilih Semua Hapus">
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $rowNo = 1; @endphp
                                        @foreach ($permissionMatrix as $catName => $modules)
                                            {{-- Header Kategori Grup Menu --}}
                                            <tr style="background-color: #f8fafc; font-weight: 700; color: #1e293b;">
                                                <td class="text-center text-muted fw-semibold" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">{{ $rowNo++ }}</td>
                                                <td colspan="5" class="py-2 px-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                                    @if (str_contains($catName, 'Dashboard'))
                                                        <i class="ti ti-dashboard text-primary me-2"></i>
                                                    @elseif (str_contains($catName, 'Master'))
                                                        <i class="ti ti-folder text-warning me-2"></i>
                                                    @elseif (str_contains($catName, 'Transaksi') || str_contains($catName, 'Laporan'))
                                                        <i class="ti ti-clipboard-list text-success me-2"></i>
                                                    @else
                                                        <i class="ti ti-folders text-secondary me-2"></i>
                                                    @endif
                                                    {{ $catName }}
                                                </td>
                                            </tr>

                                            @foreach ($modules as $mod)
                                                <tr class="permission-row-edit bg-white">
                                                    <td class="text-center text-muted fw-normal" style="background-color: #fafafa; border: 1px solid #e2e8f0;">{{ $rowNo++ }}</td>
                                                    <td class="ps-4 py-2" style="border: 1px solid #e2e8f0;">
                                                        <span class="text-muted opacity-75 font-monospace me-2" style="font-size: 0.85rem;">├──</span>
                                                        <span class="text-dark fw-medium">{{ $mod['name'] }}</span>
                                                    </td>

                                                    {{-- Kolom 1: Hak Akses --}}
                                                    <td class="text-center py-2" style="border: 1px solid #e2e8f0;">
                                                        @if (!empty($mod['view']))
                                                            <input class="form-check-input check-permission-edit perm-view" type="checkbox"
                                                                name="permissions[]" value="{{ $mod['view'] }}"
                                                                id="perm_edit_{{ str_replace('.', '_', $mod['view']) }}"
                                                                style="width: 1.15rem; height: 1.15rem; cursor: pointer;">
                                                        @else
                                                            <span class="text-muted" style="opacity: 0.3;">-</span>
                                                        @endif
                                                    </td>

                                                    {{-- Kolom 2: Tambah --}}
                                                    <td class="text-center py-2" style="border: 1px solid #e2e8f0;">
                                                        @if (!empty($mod['create']))
                                                            <input class="form-check-input check-permission-edit perm-create" type="checkbox"
                                                                name="permissions[]" value="{{ $mod['create'] }}"
                                                                id="perm_edit_{{ str_replace('.', '_', $mod['create']) }}"
                                                                style="width: 1.15rem; height: 1.15rem; cursor: pointer;">
                                                        @else
                                                            <span class="text-muted" style="opacity: 0.3;">-</span>
                                                        @endif
                                                    </td>

                                                    {{-- Kolom 3: Ubah --}}
                                                    <td class="text-center py-2" style="border: 1px solid #e2e8f0;">
                                                        @if (!empty($mod['edit']))
                                                            <input class="form-check-input check-permission-edit perm-edit" type="checkbox"
                                                                name="permissions[]" value="{{ $mod['edit'] }}"
                                                                id="perm_edit_{{ str_replace('.', '_', $mod['edit']) }}"
                                                                style="width: 1.15rem; height: 1.15rem; cursor: pointer;">
                                                        @else
                                                            <span class="text-muted" style="opacity: 0.3;">-</span>
                                                        @endif
                                                    </td>

                                                    {{-- Kolom 4: Hapus --}}
                                                    <td class="text-center py-2" style="border: 1px solid #e2e8f0;">
                                                        @if (!empty($mod['delete']))
                                                            <input class="form-check-input check-permission-edit perm-delete" type="checkbox"
                                                                name="permissions[]" value="{{ $mod['delete'] }}"
                                                                id="perm_edit_{{ str_replace('.', '_', $mod['delete']) }}"
                                                                style="width: 1.15rem; height: 1.15rem; cursor: pointer;">
                                                        @else
                                                            <span class="text-muted" style="opacity: 0.3;">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-3 px-4">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
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
