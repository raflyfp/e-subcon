<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>e-Subcon</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="e-Subcon — Pencatatan Pengerjaan Barang Subcon">
    <meta name="keywords" content="e-Subcon, subcon, pengerjaan">

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('template/dist') }}/assets/images/favicon.png" type="image/x-icon">
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

    <!-- Override Inline Style -->
    <style>
        .bg-header {
            background-color: #E20114 !important;
        }

        .pc-sidebar {
            background-color: #1e293b !important;
        }

        .pc-sidebar .pc-navbar {
            background-color: #1e293b !important;
        }

        .pc-sidebar .pc-link {
            color: #e2e8f0 !important;
        }

        .pc-sidebar .pc-link:hover {
            background-color: #334155 !important;
        }

        .pc-sidebar .pc-link.active {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
        }

        .enterprise-header>a:hover {
            color: #3b82f6 !important
        }
    </style>
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="dark">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    @php
        // Cek status keaktifan user yang sedang login: jika status subcon is_active = 0, maka akun ditangguhkan (suspend)
        $isSuspended =
            auth()->check() &&
            !auth()->user()->is_admin &&
            (
                (auth()->user()->lokasiSubcon && auth()->user()->lokasiSubcon->is_active == 0) ||
                (auth()->user()->karyawan && auth()->user()->karyawan->is_active == 0)
            );
    @endphp

    @if ($isSuspended)
        {{-- Sembunyikan sidebar dan atur layout full screen jika akun ditangguhkan --}}
        <style>
            .pc-sidebar {
                display: none !important;
            }

            .pc-header {
                left: 0 !important;
            }

            .pc-container {
                margin-left: 0 !important;
                padding-top: 90px !important;
            }
        </style>
    @endif

    <!-- [ Sidebar Menu ] start -->
    {{-- Tampilkan sidebar hanya jika akun tidak sedang ditangguhkan --}}
    @if (!$isSuspended)
        @include('components.sidebar')
    @endif
    <!-- [ Sidebar Menu ] end --> <!-- [ Header Topbar ] start -->
    <header class="pc-header">
        <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- [Mobile Media Block end] -->
            <div class="ms-auto">
                <ul class="list-unstyled">
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                            role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                            <img src="{{ asset('template/dist') }}/assets/images/user/avatar-2.jpg" alt="user-image"
                                class="user-avtar">
                            <span>{{ auth()->user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex flex-col mb-1 justify-content-start">
                                    <div class="flex">
                                        <h6 class="mb-1">{{ auth()->user()->name }}</h6>
                                        {{-- <span>UI/UX Designer</span> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="tab-content" id="mysrpTabContent">
                                <div class="tab-pane fade show active" id="drp-tab-1" role="tabpanel"
                                    aria-labelledby="drp-t1" tabindex="0">
                                    <a href="{{ url('/password') }}" class="dropdown-item">
                                        <i class="ti ti-lock-access"></i>
                                        <span>Ganti Password</span>
                                    </a>
                                </div>
                            </div>
                            <div class="tab-content" id="mysrpTabContent">
                                <div class="tab-pane fade show active" id="drp-tab-1" role="tabpanel"
                                    aria-labelledby="drp-t1" tabindex="0">
                                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="ti ti-power"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <!-- [ Header ] end -->



    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                {{-- Tampilkan halaman peringatan suspend jika is_active = 0, sebaliknya tampilkan konten halaman utama --}}
                @if ($isSuspended)
                    <div class="col-12 mt-5">
                        <div class="card border-0 shadow-sm text-center py-5 px-4"
                            style="background-color: #fff2f4; border-radius: 12px; border-left: 5px solid #dc3545 !important;">
                            <div class="card-body">
                                <div class="mb-4">
                                    <i class="ti ti-ban text-danger"
                                        style="font-size: 4.5rem; display: inline-block;"></i>
                                </div>
                                <h2 class="text-danger fw-bold mb-3">Akun Ditangguhkan (Suspended)</h2>
                                <p class="text-muted fs-5 mb-4 mx-auto" style="max-width: 600px;">
                                    Akun Anda saat ini dinonaktifkan. Anda tidak dapat melakukan pengisian atau
                                    mengakses data pengerjaan saat ini.
                                </p>
                                <div
                                    class="bg-white d-inline-block py-2 px-4 rounded-pill border border-danger shadow-xs mb-4">
                                    <span class="text-danger fw-semibold"><i class="ti ti-info-circle me-1"></i>
                                        Silakan Hubungi Administrator untuk info lebih lanjut.</span>
                                </div>
                                <div>
                                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger px-4">
                                            <i class="ti ti-power me-1"></i> Keluar / Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @yield('content')
                @endif
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm my-1">
                    <p>e-Subcon</p>
                </div>

            </div>
        </div>
    </footer>

    <!-- [Page Specific JS] start -->
    <script src="{{ asset('template/dist') }}/assets/js/plugins/jquery.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/sweetalert2.all.min.js"></script>

    <script src="{{ asset('template/dist') }}/assets/js/plugins/apexcharts.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/pages/dashboard-default.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/popper.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/simplebar.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/bootstrap.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/fonts/custom-font.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/pcoded.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/feather.min.js"></script>


    <script src="{{ asset('template/dist') }}/assets/js/plugins/jquery.dataTables.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/buttons.colVis.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/buttons.print.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/pdfmake.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/jszip.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/dataTables.buttons.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/vfs_fonts.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/buttons.html5.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/buttons.bootstrap5.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/choices.min.js"></script>

    <script>
        // [ Column Selectors ]
        $('#cbtn-selectors').DataTable({
            pageLength: 15,
            info: false,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, ':visible']
                    },
                    footer: true
                },
                {
                    extend: 'excelHtml5',
                    filename: function() {
                        let url = window.location.href.toLowerCase();
                        let dateStr = new Date().toISOString().slice(0, 10);
                        if (url.includes('culture')) {
                            return 'Laporan_Penilaian_Culture_' + dateStr;
                        } else if (url.includes('leadership')) {
                            return 'Laporan_Penilaian_Leadership_' + dateStr;
                        }
                        return 'Laporan_Penilaian_' + dateStr;
                    },
                    exportOptions: {
                        columns: ':not(.no-export)'
                    },
                    footer: true
                },
                {
                    extend: 'pdfHtml5',
                    filename: function() {
                        let url = window.location.href.toLowerCase();
                        let dateStr = new Date().toISOString().slice(0, 10);
                        if (url.includes('culture')) {
                            return 'Laporan_Penilaian_Culture_' + dateStr;
                        } else if (url.includes('leadership')) {
                            return 'Laporan_Penilaian_Leadership_' + dateStr;
                        }
                        return 'Laporan_Penilaian_' + dateStr;
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 5]
                    },
                    footer: true
                },
                {
                    extend: 'colvis',
                    columns: ':not(.d-none)'
                }
            ]
        });
        $('#btn-report').DataTable({
            pageLength: 50,
            paging: false,
            info: false,
            searching: false,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, ':visible']
                    },
                    footer: true
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    },
                    footer: true
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, ':visible']
                    },
                    footer: true
                },
                'colvis'
            ]
        });

        $('#btn-penilaian').DataTable({
            pageLength: 20,
            paging: true,
            info: false,
            searching: false,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, ':visible']
                    },
                    footer: true
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    },
                    footer: true
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, ':visible']
                    },
                    footer: true
                },
                'colvis'
            ]
        });
    </script>


    {{-- multiple select AJAX logic --}}
    <script>
        // form multiple pakai ajax yg ini
        document.addEventListener('DOMContentLoaded', function() {
            var genericExamples = document.querySelectorAll('[data-trigger]');
            for (i = 0; i < genericExamples.length; ++i) {
                var element = genericExamples[i];
                new Choices(element, {
                    placeholderValue: 'Pilih Data',
                    searchPlaceholderValue: 'Cari Data'
                });
            }

            var textRemove = new Choices(document.getElementById('choices-text-remove-button'), {
                delimiter: ',',
                editItems: true,
                maxItemCount: 5,
                removeItemButton: true
            });

            var text_Unique_Val = new Choices('#choices-text-unique-values', {
                paste: false,
                duplicateItemsAllowed: false,
                editItems: true
            });


        });
    </script>

    {{--
    <script>
        layout_change('light');
    </script> --}}




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            change_box_container('false');
            layout_rtl_change('false');
            font_change("Public-Sans");
        });
    </script>

    <script>
        const LIFETIME_MS = {{ config('session.lifetime') * 60 * 1000 }};
        let showAlert = false;
        let lastActivity = Date.now()

        function CheckSession() {
            if (showAlert) return

            fetch('{{ route('check-session') }}', {
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.authenticated) Alert()
                })
                .catch(() => {});
        }

        function Alert() {
            if (showAlert) return
            showAlert = true;
            Swal.fire({
                title: 'Sesi Berakhir',
                text: 'Sesi kamu telah habis. Silakan login kembali.',
                icon: 'warning',
                confirmButtonText: 'Login Ulang',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then(() => {
                window.location.href = '{{ route('login') }}';
            });
        }


        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                CheckSession();
                fetch('{{ route('refresh-csrf') }}')
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.csrf_token) {
                            document.querySelectorAll('input[name="_token"]').forEach(input => {
                                input.value = data.csrf_token;
                            });
                        }
                    })
                    .catch(() => {});
            }
        });

        ['mousedown', 'keydown', 'touchstart', 'click'].forEach(function(e) {
            document.addEventListener(e, function() {
                const idleTime = Date.now() - lastActivity;
                lastActivity = Date.now()

                if (idleTime > LIFETIME_MS * 0.9) {
                    CheckSession();
                }
            }, {
                passive: true
            });
        })

        setTimeout(CheckSession, LIFETIME_MS);
    </script>


    @stack('scripts')
</body>
<!-- [Body] end -->

</html>
