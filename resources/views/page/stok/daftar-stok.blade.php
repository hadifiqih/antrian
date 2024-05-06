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
                    <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMutasiStok">
                        <i class="fas fa-plus-circle"></i> Mutasi Stok
                    </button>
                </div>
                <div class="card-body">
                    <table id="tableStok" class="table table-borderless table-striped">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Nama</th>
                                <th>Masuk</th>
                                <th>Terjual</th>
                                <th>Akhir</th>
                                <th>Satuan</th>
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
        $('#tableStok').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('daftarStokJson') }}",
            columns: [
                { data: 'sku', name: 'sku' },
                { data: 'nama', name: 'nama' },
                { data: 'masuk', name: 'masuk' },
                { data: 'terjual', name: 'terjual' },
                { data: 'stok', name: 'stok' },
                { data: 'satuan', name: 'satuan' }
            ]
        });
    });
</script>

@endsection