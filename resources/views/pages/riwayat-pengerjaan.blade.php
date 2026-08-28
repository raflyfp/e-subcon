@extends('layouts.mantis')
@section('title', 'Riwayat Pengerjaan Barang')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="content mb-1">Riwayat Pengerjaan Barang</h1>
            <p class="text-muted mb-0">Daftar rekapan hasil pengerjaan barang yang telah tercatat di sistem</p>
        </div>
        <div>
            <a href="{{ url('pengerjaan') }}" class="btn btn-primary">
                <i class="ti ti-edit me-1"></i> Buka Formulir Pengerjaan
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark fw-bold">
                <i class="ti ti-table me-2 text-primary"></i>Tabel Riwayat Pengerjaan
            </h5>
            <span class="badge bg-light-primary text-primary fw-medium px-3 py-2">
                Total: {{ $pengerjaan->count() }} Data
            </span>
        </div>
        <div class="card-body p-4">
            <x-pengerjaan.table :pengerjaan="$pengerjaan" />
        </div>
    </div>

@endsection

@push('scripts')
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
@endpush
