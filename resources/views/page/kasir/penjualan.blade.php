@extends('layouts.app')

@section('title', 'Ringkasan Penjualan | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Penjualan')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan Penjualan</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>Tanggal</th>
                                <th>Nama Pelanggan</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->no_invoice }}</td>
                                <td>{{ date_format($item->created_at, 'd F Y') }}</td>
                                <td>{{ $item->customer->nama }}</td>
                                <td>{{ number_format($item->total, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('pos.invoice', $item->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-clipboard-list"></i> Invoice</a>
                                    <a href="{{ route('pos.printFaktur', $item->id) }}" class="btn btn-danger btn-sm"><i class="fas fa-print"></i> Cetak Nota</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection