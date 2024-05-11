@extends('layouts.app')

@section('title', 'Detail Penjualan | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Detail Penjualan')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <a href="javascript:void()" onclick="goBack()"><i class="fas fa-arrow-left"></i></a> <span class=" ml-2 h5 font-weight-bold"> Detail Transaksi</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table id="tableDetail" class="table table-borderless">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->produk->nama_produk }}</td>
                                <td>Rp. {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>Rp. {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td>Total Harga</td>
                                <td>:</td>
                                <td>Rp. {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Bayar</td>
                                <td>:</td>
                                <td>Rp. {{ number_format($penjualan->bayar, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Kembalian</td>
                                <td>:</td>
                                <td>Rp. {{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

@endsection