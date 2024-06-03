@extends('layouts.app')

@section('title', 'Mutasi Stok | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Kelola Stok')

@section('breadcrumb', 'Mutasi Stok')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Success!</h5>
    {{ session('success') }}
</div>
@endif

<div class="container">
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="periode">Periode Bulan</label>
                <select id="periode" class="form-control select2" name="periode" id="periode">
                    <option value="">Semua</option>
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
                    <h3 class="card-title">Mutasi Stok</h3>
                </div>
                <div class="card-body">
                    <table id="tableMutasi" class="table table-borderless table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>SKU</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
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
        $('#tableMutasi').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('mutasiStokJson') }}",
            columns: [
                { data: 'tanggal', name: 'tanggal' },
                { data: 'sku', name: 'sku' },
                { data: 'nama', name: 'nama' },
                { data: 'kategori', name: 'kategori' },
                { data: 'jenis', name: 'jenis' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'keterangan', name: 'keterangan' }
            ]
        });

        $('#periode').change(function() {
            var periode = $('#periode').val();
            $('#tableMutasi').DataTable().ajax.url("{{ route('mutasiStokJson') }}?periode=" + periode).load();
        });
    });
</script>

@endsection