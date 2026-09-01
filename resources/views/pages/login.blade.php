<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Login — e-Subcon</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="e-Subcon — Pencatatan Pengerjaan Barang Subcon">
    <meta name="keywords" content="e-Subcon, subcon, pengerjaan">
    <meta name="author" content="e-Subcon">

    <!-- [Favicon] icon -->
    {{-- <link rel="icon" href="{{ asset('template/dist') }}/assets/images/favicon.svg" type="image/x-icon"> --}}
    <!-- [Google Font] Family -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/fonts/material.css">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/css/style-preset.css">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">
                <div class="auth-header">
                    {{-- logo --}}

                </div>
                <div class="card my-5">
                    <form method="POST" action="{{ route('login.post') }}" id="formLogin">
                        @csrf

                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-end mb-4">
                                <h3 class="fw-bold text-primary">e-Subcon</h3>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Username</label>
                                <input type="username" class="form-control" name="username" placeholder="Username"
                                    required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Password"
                                    required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input input-primary" type="checkbox" id="remember" name="remember" value="1" checked>
                                    <label class="form-check-label text-muted small" for="remember" style="cursor: pointer;">
                                        Tetap masuk / Ingat saya di perangkat ini
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid mt-4 mb-3">
                                <button type="submit" id="btn-login" class="btn btn-primary btn-lg fw-semibold">
                                    <i class="ti ti-login me-1"></i> Masuk ke Sistem
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="text-center pb-3">
                        <small class="text-muted">Sistem Pencatatan Pengerjaan Barang e-Subcon</small>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- [ Main Content ] end -->
    <!-- Required Js -->
    <script src="{{ asset('template/dist') }}/assets/js/plugins/popper.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/simplebar.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/bootstrap.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/fonts/custom-font.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/pcoded.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>



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
        $(document).ready(function() {
            let isSubmitting = false;

            $('#formLogin').on('submit', function(e) {
                if (isSubmitting) return;

                @if(filter_var(env('AUTO_REFRESH_CSRF', true), FILTER_VALIDATE_BOOLEAN))
                    e.preventDefault();
                    let form = this;

                    $('#btn-login')
                        .prop('disabled', true)
                        .text('Loading...');

                    // Tarik CSRF Token terbaru dari server sesaat sebelum form dikirimkan
                    $.ajax({
                        url: "{{ route('refresh-csrf') }}",
                        type: "GET",
                        dataType: "json",
                        timeout: 3000,
                        success: function(data) {
                            if (data.csrf_token) {
                                $('input[name="_token"]').val(data.csrf_token);
                            }
                        },
                        complete: function() {
                            isSubmitting = true;
                            form.submit();
                        }
                    });
                @else
                    $('#btn-login')
                        .prop('disabled', true)
                        .text('Loading...');
                @endif
            });
        });
    </script>

    <script>
        layout_change('light');
    </script>




    <script>
        change_box_container('false');
    </script>



    <script>
        layout_rtl_change('false');
    </script>


    <script>
        preset_change("preset-1");
    </script>


    <script>
        font_change("Public-Sans");
    </script>



</body>
<!-- [Body] end -->



</html>
