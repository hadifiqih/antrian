@extends('layouts.app')

@section('title', 'Ringkasan Penjualan Bahan | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Penjualan Bahan')

@section('content')

<div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Laporan Penjualan Bahan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="table" class="table table-bordered table-striped display nowarp" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Kulak</th>
                                    <th>Jual</th>
                                    <th>Total</th>
                                    <th>Laba</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "/pos/laporan-item-json",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'produk', name: 'produk'},
                {data: 'jumlah', name: 'jumlah'},
                {data: 'kulak', name: 'kulak'},
                {data: 'harga', name: 'harga'},
                {data: 'total', name: 'total'},
                {data: 'laba', name: 'laba'}
            ]
        });
    });
</script>
@endsection