@extends('layouts.app')

@section('title', 'Daftar Stok | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Kelola Stok')

@section('breadcrumb', 'Daftar Stok')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Daftar Stok</h3>
                </div>
                <div class="card-body">
                    <table id="tableStok" class="table table-borderless table-striped">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>NAMA</th>
                                <th>JENIS</th>
                                <th>AWAL</th>
                                <th>MASUK</th>
                                <th>TERJUAL</th>
                                <th>AKHIR</th>
                                <th>SATUAN</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#tableStok').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('daftarStokJson') }}",
            columns: [
                { data: 'sku', name: 'sku' },
                { data: 'nama', name: 'nama' },
                { data: 'jenis', name: 'jenis' },
                { data: 'awal', name: 'awal' },
                { data: 'masuk', name: 'masuk' },
                { data: 'terjual', name: 'terjual' },
                { data: 'akhir', name: 'akhir' },
                { data: 'satuan', name: 'satuan' },
                { data: 'aksi', name: 'aksi' }
            ]
        });
    });
</script>

@endsection