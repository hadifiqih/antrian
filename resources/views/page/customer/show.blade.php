@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('breadcrumb', 'Pelanggan')

@section('page', 'Data Pelanggan')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="mr-3">
                                <div class="bg-secondary rounded-circle" style="width: 48px; height: 48px;">
                                    <!-- Placeholder untuk foto profil -->
                                </div>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $customer->nama }}</h5>
                                <p class="card-text text-muted text-sm">{{ $customer->instansi }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-user"></i><strong> Tentang Ananda Putra</strong></h5>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Tentang</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="mr-3">
                                <div class="bg-secondary rounded-circle" style="width: 48px; height: 48px;">
                                    <!-- Placeholder untuk foto profil -->
                                </div>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $customer->nama }}</h5>
                                <p class="card-text text-muted text-sm">{{ $customer->instansi }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection