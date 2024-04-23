@extends('layouts.app')

@section('title', 'Ringkasan Penjualan | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Penjualan')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow">
            <span class="info-box-icon bg-danger"><i class="fas fa-money-bill"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Penjualan Hari Ini</span>
                <span class="info-box-number"><h5 class="font-weight-bold text-danger">{{ $omsetToday }}</h5></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow">
            <span class="info-box-icon bg-success"><i class="fas fa-cart-arrow-down"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Omset (1 bulan)</span>
                <span class="info-box-number"><h5 class="font-weight-bold text-success">{{ $omsetMonth }}</h5></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Laporan Penjualan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="table" class="table table-bordered table-striped display nowarp" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
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
            searching: false,
            ordering: false,
            paging: false,
            info: false,
            scrollX: true,
            ajax: "{{ route('pos.laporanBahanJson') }}",
            columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'no_invoice', name: 'no_invoice'},
                    {data: 'tanggal', name: 'tanggal'},
                    {data: 'customer', name: 'customer'},
                    {data: 'total', name: 'total'},
                    {data: 'action', name: 'action'},
                ]
        });
    });
</script>
@endsection