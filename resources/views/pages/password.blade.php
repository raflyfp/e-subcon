@extends('layouts.mantis')
@section('title', 'Ganti Password')

@section('content')

    {{-- Page Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Ganti Password</h2>
        <p class="text-muted mb-0">Perbarui kata sandi akun Anda secara berkala untuk menjaga keamanan.</p>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    {{-- Section Title --}}
                    <div class="mb-4">
                        <h5 class="fs-4 fw-semibold mb-1">Reset Password</h5>
                        <p class="text-muted small mb-0">
                            Masukkan password lama Anda, lalu buat password baru yang kuat.
                        </p>
                    </div>

                    <hr class="my-3">
                    <form action="{{ route('password.change') }}" id="formReset">
                        {{-- Password Lama --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-uppercase small text-secondary">Password Lama</label>
                            <div class="input-group">
                                <input type="password" id="old_password" name="old_password" class="form-control"
                                    placeholder="Masukkan password saat ini">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="#old_password">
                                    <i class="ti ti-eye-off"></i>
                                </button>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Password Baru --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-uppercase small text-secondary">Password Baru</label>
                            <div class="input-group">
                                <input type="password" id="new_password" name="new_password" class="form-control"
                                    placeholder="Password Baru">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="#new_password">
                                    <i class="ti ti-eye-off"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-secondary">Konfirmasi
                                Password </label>
                            <div class="input-group">
                                <input type="password" id="conf_password" name="conf_password" class="form-control"
                                    placeholder="Konfirmasi Password">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="#conf_password">
                                    <i class="ti ti-eye-off"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="alert alert-primary d-flex gap-2 align-items-start py-2 px-3 mb-4">
                            <i class="ti ti-shield-check fs-5 mt-1"></i>
                            <div class="small">
                                <strong>Tips keamanan:</strong> Hindari menggunakan tanggal lahir atau nama yang mudah
                                ditebak.
                                Ganti password secara rutin setiap 3 bulan.
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="d-grid">
                            <button type="submit" id="btnReset" class="btn btn-primary btn-lg fw-semibold">
                                <i class="ti ti-lock me-1"></i> Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let baseUrl = '{{ url('') }}';
    </script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false,
                timerProgressBar: true
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                timer: 2000,
                showConfirmButton: false,
                timerProgressBar: true
            });
        </script>
    @endif

    <script>
        $('#formReset').on('submit', function(e) {
            e.preventDefault();
            let old_password = $('#old_password').val()
            let new_password = $('#new_password').val()
            let conf_password = $('#conf_password').val()


            if (!old_password || !new_password || !conf_password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kolom Belum Lengkap',
                    text: 'Semua kolom password wajib diisi sebelum melanjutkan.',
                });
                return;
            }

            if ((new_password).length < 5) {
                Swal.fire({
                    icon: 'error',
                    title: 'Panjang Password',
                    text: 'Password baru harus memiliki minimal 5 karakter. Silakan periksa kembali.',
                    confirmButtonText: 'Coba Lagi'
                });
                return;
            }

            if (new_password !== conf_password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Tidak Cocok',
                    text: 'Password baru dan konfirmasi password yang Anda masukkan berbeda. Silakan periksa kembali.',
                    confirmButtonText: 'Coba Lagi'
                });
                return;
            }

            Swal.fire({
                title: 'Ganti Password?',
                text: 'Tindakan ini tidak dapat dibatalkan',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ganti Password',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // disabled dulu button submit
                    $('#btnReset')
                        .prop('disabled', true)
                        .text('Loading...');

                    //kemudian submit form 
                    this.submit();
                }
            });
        })

        $(document).on('click', '.toggle-password', function() {
            const target = $($(this).data('target'));
            const icon = $(this).find('i');

            if (target.attr('type') === 'password') {
                target.attr('type', 'text');
                icon.removeClass('ti-eye-off').addClass('ti-eye');
            } else {
                target.attr('type', 'password');
                icon.removeClass('ti-eye').addClass('ti-eye-off');
            }
        });
    </script>

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: @json($errors->first()),
                timer: 3000,
                showConfirmButton: false, 
                timerProgressBar: true
            });
        </script>
    @endif
@endpush
