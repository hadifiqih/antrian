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
    });
</script>

@endsection