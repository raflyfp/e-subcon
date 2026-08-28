@extends('layouts.mantis')

@section('title', 'Dashboard')

@section('content')
<h1 class="mb-3">Dashboard</h1>

<div class="card p-4 shadow-sm">
    <p class="fs-4 mb-1 fw-semibold">
        Halo, <span class="text-primary">{{ auth()->user()->name }}</span> 👋
    </p>
    <p class="text-muted mb-4">
        Berikut ringkasan aktivitas e-Subcon Anda
    </p>

    <div class="row g-4">

        @if (auth()->user()->is_admin)
            {{-- Admin: ringkasan seluruh data --}}
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 text-center shadow border-0 bg-primary bg-opacity-10">
                    <div class="fs-2 mb-2">👥</div>
                    <p class="mb-1 text-muted fw-medium">Karyawan Aktif</p>
                    <div class="fs-1 fw-bold text-primary">{{ $totalKaryawan }}</div>
                    <small class="text-muted">Total karyawan terdaftar</small>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 text-center shadow border-0 bg-info bg-opacity-10">
                    <div class="fs-2 mb-2">📦</div>
                    <p class="mb-1 text-muted fw-medium">Total Barang</p>
                    <div class="fs-1 fw-bold text-info">{{ $totalBarang }}</div>
                    <small class="text-muted">Barang aktif terdaftar</small>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 text-center shadow border-0 bg-success bg-opacity-10">
                    <div class="fs-2 mb-2">📋</div>
                    <p class="mb-1 text-muted fw-medium">Pengerjaan Hari Ini</p>
                    <div class="fs-1 fw-bold text-success">{{ $pengerjaanHariIni }}</div>
                    <small class="text-muted">Total unit dikerjakan hari ini</small>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 text-center shadow border-0 bg-warning bg-opacity-10">
                    <div class="fs-2 mb-2">📊</div>
                    <p class="mb-1 text-muted fw-medium">Pengerjaan Bulan Ini</p>
                    <div class="fs-1 fw-bold text-warning">{{ $pengerjaanBulanIni }}</div>
                    <small class="text-muted">Total unit bulan berjalan</small>
                </div>
            </div>
        @else
            {{-- Karyawan: ringkasan milik sendiri --}}
            <p class="text-primary fw-semibold fs-5 mb-2">📊 Ringkasan Pengerjaan Anda</p>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 p-4 text-center shadow border-0 bg-success bg-opacity-10">
                    <div class="fs-2 mb-2">📋</div>
                    <p class="mb-1 text-muted fw-medium">Pengerjaan Hari Ini</p>
                    <div class="fs-1 fw-bold text-success">{{ $pengerjaanHariIni }}</div>
                    <small class="text-muted">Total unit Anda hari ini</small>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 p-4 text-center shadow border-0 bg-primary bg-opacity-10">
                    <div class="fs-2 mb-2">📊</div>
                    <p class="mb-1 text-muted fw-medium">Pengerjaan Bulan Ini</p>
                    <div class="fs-1 fw-bold text-primary">{{ $pengerjaanBulanIni }}</div>
                    <small class="text-muted">Total unit bulan berjalan</small>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
    @if(session('unauthorized'))
        <script>
            Swal.fire({
                icon: "{{ session('unauthorized.type') }}",
                title: "{{ session('unauthorized.title') }}",
                text: "{{ session('unauthorized.text') }}",
                confirmButtonText: 'Kembali',
            });
        </script>
    @endif
@endpush
