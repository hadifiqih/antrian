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
                <span class="info-box-number"><h5 class="font-weight-bold text-danger" id="omsetToday"></h5></span>
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
                <span class="info-box-number"><h5 class="font-weight-bold text-success" id="omsetMonth"></h5></span>
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
                    {{-- Memilih Bulan --}}
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="form-group">
                            <select name="bulan" id="bulan" class="form-control">
                                <option value="01" {{ $bulan == '01' ? 'selected' : '' }}>Januari</option>
                                <option value="02" {{ $bulan == '02' ? 'selected' : '' }}>Februari</option>
                                <option value="03" {{ $bulan == '03' ? 'selected' : '' }}>Maret</option>
                                <option value="04" {{ $bulan == '04' ? 'selected' : '' }}>April</option>
                                <option value="05" {{ $bulan == '05' ? 'selected' : '' }}>Mei</option>
                                <option value="06" {{ $bulan == '06' ? 'selected' : '' }}>Juni</option>
                                <option value="07" {{ $bulan == '07' ? 'selected' : '' }}>Juli</option>
                                <option value="08" {{ $bulan == '08' ? 'selected' : '' }}>Agustus</option>
                                <option value="09" {{ $bulan == '09' ? 'selected' : '' }}>September</option>
                                <option value="10" {{ $bulan == '10' ? 'selected' : '' }}>Oktober</option>
                                <option value="11" {{ $bulan == '11' ? 'selected' : '' }}>November</option>
                                <option value="12" {{ $bulan == '12' ? 'selected' : '' }}>Desember</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-9 col-sm-6 text-right">
                        <div class="form-group">
                            <button class="btn btn-success btn-sm"><i class="fas fa-download"></i> Laporan Excel</button>
                        </div>
                    </div>
                </div>

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
    function printStruk($id) {
        //menggunakan ajax untuk mengirim data ke server
        $.ajax({
          url: "/pos/nota-print/" + $id,
          type: "GET",
          success: function(response) {
            //sweetalert2 untuk menampilkan pesan berhasil
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: 'Struk berhasil dicetak',
              showConfirmButton: false,
              timer: 1500
            });
          }
        });
      }

    $(document).ready(function() {
        $.get('/pos/omset-today/' + $('#bulan').val(), function(data) {
            $('#omsetToday').html(data);
        });

        $.get('/pos/omset-bulanan/' + $('#bulan').val(), function(data) {
            $('#omsetMonth').html(data);
        });

        $('#table').DataTable({
            processing: true,
            serverSide: true,
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

        $('#bulan').on('change', function() {
            var bulan = $(this).val();
            // change data in datatable with selected month
            $('#table').DataTable().ajax.url("/pos/laporan-bahan-json?bulan=" + bulan).load();
            //reload url
            $.get('/pos/omset-today/' + $('#bulan').val(), function(data) {
                $('#omsetToday').html(data);
            });

            $.get('/pos/omset-bulanan/' + $('#bulan').val(), function(data) {
                $('#omsetMonth').html(data);
            });
        });
    });
</script>
@endsection