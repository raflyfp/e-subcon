@extends('layouts.mantis')
@section('title', 'Master Pekerjaan')

@section('content')

    <h1 class="content">Master Pekerjaan</h1>

    <div class="card">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col px-4 py-4">
                        @if (auth()->user()->canAccess('master_pekerjaan.create'))
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambah_pekerjaan">
                                <i class="ti ti-plus me-1"></i> Tambah Pekerjaan
                            </button>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col px-4">
                        @include('components.pekerjaan.modal')
                        @include('components.pekerjaan.modal_update')
                        <x-pekerjaan.table :pekerjaan="$pekerjaan" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
