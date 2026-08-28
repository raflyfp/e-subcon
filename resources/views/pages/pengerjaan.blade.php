@extends('layouts.mantis')
@section('title', 'Pengerjaan')

@section('content')

    <h1 class="content">Data Pengerjaan</h1>

    <div class="card">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col px-4 py-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#tambah_pengerjaan">
                            <i class="ti ti-plus me-1"></i> Tambah Pengerjaan
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col px-4">
                        @include('components.pengerjaan.modal')
                        <x-pengerjaan.table :pengerjaan="$pengerjaan" />
                    </div>
                </div>
            </div>
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
