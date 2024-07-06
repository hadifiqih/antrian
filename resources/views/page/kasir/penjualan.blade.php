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
                            <button class="btn btn-success btn-sm disabled"><i class="fas fa-download"></i> Laporan Excel</button>
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
    const baseUrl = '/pos/'; // Ganti dengan URL dasar yang sesuai

    function printStruk(id) {
        $.ajax({
            url: baseUrl + "nota-print/" + id,
            type: "GET",
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Struk berhasil dicetak',
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mencetak struk',
                });
            }
        });
    }

    function updatePenjualanData(bulan) {
        $.ajax({
            url: baseUrl + "penjualan-data/" + bulan,
            type: "GET",
            dataType: 'json',
            success: function(data) {
                $('#omsetToday').html(data.today);
                $('#omsetMonth').html(data.monthly);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching penjualan data:", error);
            }
        });
    }

    $(document).ready(function() {
        // Fungsi untuk menyesuaikan select option berdasarkan bulan saat ini
        function setCurrentMonth() {
            var currentDate = new Date();
            var currentMonth = (currentDate.getMonth() + 1).toString().padStart(2, '0'); // Bulan dalam format 2 digit (01-12)
            $('#bulan').val(currentMonth);
        }

        // Panggil fungsi setCurrentMonth saat dokumen siap
        setCurrentMonth();

        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: baseUrl + "laporan-bahan-json",
                data: function(d) {
                    d.bulan = $('#bulan').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'no_invoice', name: 'no_invoice'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'customer', name: 'customer'},
                {data: 'total', name: 'total'},
                {data: 'action', name: 'action'},
            ]
        });

        updatePenjualanData($('#bulan').val());

        $('#bulan').on('change', function() {
            table.ajax.reload();
            updatePenjualanData($(this).val());
        });
    });
</script>
@endsection