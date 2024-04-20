@extends('layouts.app')

@section('title', 'Invoice Penjualan | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Invoice Penjualan')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <h5 class="col-md-6 font-weight-bold">Faktur Penjualan</h5>
                <div class="col-md-6 text-right">
                    <h6>INVOICE</h6>
                    <p>{{ $penjualan->no_invoice }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6>DITERBITKAN ATAS NAMA</h6>
                    <p>Jl. Raya Kassab No. 1, Kassab, Kec. Kassab, Kab. Kassab, Prov. Kassab</p>
                    <p>Telp. (021) 12345678</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

@endsection