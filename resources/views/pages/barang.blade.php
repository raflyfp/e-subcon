@extends('layouts.mantis')
@section('title', 'Master Barang')

@section('content')

    <h1 class="content">Master Barang</h1>

    <div class="card">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col px-4 py-4">
                        @if (auth()->user()->canAccess('master_barang.create'))
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambah_barang">
                                <i class="ti ti-plus me-1"></i> Tambah Barang
                            </button>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col px-4">
                        @include('components.barang.modal')
                        @include('components.barang.modal_update')
                        <x-barang.table :barang="$barang" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
