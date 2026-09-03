@extends('layouts.mantis')
@section('title', 'Master User')

@section('content')
    <h1 class="content">Master User</h1>

    <div class="row">
        <div class="col-md-12">
            <div class="card p-2">
                <div class="row">
                    <div class="col px-4 py-4">
                        @if (auth()->user()->canAccess('master_user.create'))
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambah_user">
                                <i class="ti ti-plus me-1"></i> Tambah User
                            </button>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col px-4">
                        @include('components.user.modal')
                        @include('components.user.modal_update')
                        <x-user.table :user="$user" />
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
                timer: 2000, // durasi 2.5 detik
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
                timer: 2000, // durasi 3 detik
                showConfirmButton: false,
                timerProgressBar: true
            });
        </script>
    @endif
@endpush
