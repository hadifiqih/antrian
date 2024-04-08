@extends('layouts.app')

@section('title', 'Ringkasan Sales | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Antrian')

@section('breadcrumb', 'Ringkasan Sales')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-3">
                <h3 class="card-title text-bold">Ringkasan Sales</h3>
            </div>
            <div class="col-md-9">
                <span class="text-muted d-flex justify-content-end">Bulan : {{ date('F - Y') }}</span>
            </div>
        </div>

    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-warning"><i class="fas fa-money-bill"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Penjualan Bulan Ini</span>
                      <span id="totalOmset" class="info-box-number">Rp {{ number_format($totalOmset, 0, ',' , '.') }}</span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
            </div>
            <div class="col-md-3">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Penjualan Hari Ini</span>
                      <span class="info-box-number">Rp {{ number_format($totalOmsetToday, 0, ',' , '.') }}</span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
            </div>
            <div class="col-md-1"></div>
            {{-- Pilih tanggal --}}
            <div class="col-md-5">
                <form action="{{ route('report.salesByDate')}}" method="POST">
                    @csrf
                    <div class="form-group d-flex justify-content-end">
                        <label for="tanggal" class="mr-5">Bulan</label>
                        <select name="bulan" id="bulan" class="form-control form-control-sm">
                            <option value="">Pilih Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table id="ringkasanSales" class="table table-borderless table-striped" style="width: 100%">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Nama Pelanggan</th>
                        <th>Nama Produk</th>
                        <th>Total Omset</th>
                        <th>Status Pengerjaan</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#ringkasanSales').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ringkasan.omsetSales') }}",
                type: 'GET',
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'nama_pelanggan', name: 'nama_pelanggan'},
                {data: 'nama_produk', name: 'nama_produk'},
                {data: 'total_omset', name: 'total_omset'},
                {data: 'status_pengerjaan', name: 'status_pengerjaan'},
            ]
        });

        //setel pencarian berdasarkan nama bulan yang dipilih
        $('#bulan').on('change', function() {
            var month = $(this).val();
            
            $.ajax({
                url: "/getTotalOmset/"+ month,
                type: 'GET',
                success: function(data) {
                    $('#totalOmset').text('Rp ' + data);
                }
            });

            if(month) {
                $('#ringkasanSales').DataTable().destroy();
                $('#ringkasanSales').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('ringkasan.omsetSales') }}",
                        type: 'GET',
                        data: {month: month}
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                        {data: 'tanggal', name: 'tanggal'},
                        {data: 'nama_pelanggan', name: 'nama_pelanggan'},
                        {data: 'nama_produk', name: 'nama_produk'},
                        {data: 'total_omset', name: 'total_omset'},
                        {data: 'status_pengerjaan', name: 'status_pengerjaan'},
                    ]
                });
            } else {
                $('#ringkasanSales').DataTable().ajax.reload();
            }
        });
        
    });
</script>
@endsection
