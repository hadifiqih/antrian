@extends('layouts.app')

@section('title', 'Antrian | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Laporan')

@section('breadcrumb', 'Laporan Penugasan')

@section('content')
<link href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.dataTables.css" rel="stylesheet">
<div class="container">
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="periode">Periode Bulan</label>
            <select id="periode" class="form-control select2" name="periode" id="periode">
                <option value="01" {{ date('m') == '01' ? 'selected' : '' }}>Januari</option>
                <option value="02" {{ date('m') == '02' ? 'selected' : '' }}>Februari</option>
                <option value="03" {{ date('m') == '03' ? 'selected' : '' }}>Maret</option>
                <option value="04" {{ date('m') == '04' ? 'selected' : '' }}>April</option>
                <option value="05" {{ date('m') == '05' ? 'selected' : '' }}>Mei</option>
                <option value="06" {{ date('m') == '06' ? 'selected' : '' }}>Juni</option>
                <option value="07" {{ date('m') == '07' ? 'selected' : '' }}>Juli</option>
                <option value="08" {{ date('m') == '08' ? 'selected' : '' }}>Agustus</option>
                <option value="09" {{ date('m') == '09' ? 'selected' : '' }}>September</option>
                <option value="10" {{ date('m') == '10' ? 'selected' : '' }}>Oktober</option>
                <option value="11" {{ date('m') == '11' ? 'selected' : '' }}>November</option>
                <option value="12" {{ date('m') == '12' ? 'selected' : '' }}>Desember</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Laporan SPK Bulanan</span></h3>
                    <a class="btn btn-sm btn-success float-right" href="{{ route('estimator.laporanWorkshopExcel') }}"><i class="fas fa-file-excel"></i> Unduh Excel</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomer Tiket</th>
                                    <th>Sales</th>
                                    <th>Kategori</th>
                                    <th>Nama Produk</th>
                                    <th>Qty</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>Desainer</th>
                                    <th>Operator</th>
                                    <th>Finishing</th>
                                    <th>QC</th>
                                    <th>Omset</th>
                                    <th>Action</th>
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
</div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.5.0/js/rowGroup.dataTables.js"></script>
<script>
    function filter(){
        var periode = $('#periode').val();
        $.ajax({
            url: "{{ route('estimator.laporanPenugasanJson') }}",
            type: "GET",
            data: {
                periode: periode
            },
            success: function(data){
                $('#table').DataTable().ajax.url("{{ route('estimator.laporanPenugasanJson') }}?periode="+periode).load();
                $('#')
            }
        });
    }

    $('#periode').on('change', function(){
        filter();
    });

    //DATA TABLE
    $(document).ready(function() {
        var bulan = $('#periode').val();
        var currentDate = new Date();
        var currentMonth = currentDate.getMonth();

        if(bulan == currentMonth){
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('estimator.laporanPenugasanJson') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'ticket_order', name: 'ticket_order'},
                {data: 'sales', name: 'sales'},
                {data: 'kategori', name: 'kategori'},
                {data: 'nama_produk', name: 'nama_produk'},
                {data: 'qty', name: 'qty'},
                {data: 'tgl_mulai', name: 'tgl_mulai'},
                {data: 'tgl_selesai', name: 'tgl_selesai'},
                {data: 'desainer', name: 'desainer'},
                {data: 'operator', name: 'operator'},
                {data: 'finishing', name: 'finishing'},
                {data: 'qc', name: 'qc'},
                {data: 'omset', name: 'omset'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
        });
        } else {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('estimator.laporanPenugasanJson') }}?periode="+bulan,
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'ticket_order', name: 'ticket_order'},
                {data: 'sales', name: 'sales'},
                {data: 'kategori', name: 'kategori'},
                {data: 'nama_produk', name: 'nama_produk'},
                {data: 'qty', name: 'qty'},
                {data: 'tgl_mulai', name: 'tgl_mulai'},
                {data: 'tgl_selesai', name: 'tgl_selesai'},
                {data: 'desainer', name: 'desainer'},
                {data: 'operator', name: 'operator'},
                {data: 'finishing', name: 'finishing'},
                {data: 'qc', name: 'qc'},
                {data: 'omset', name: 'omset'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
        });
        }
    });
</script>
@endsection
