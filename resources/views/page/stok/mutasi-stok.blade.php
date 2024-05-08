@extends('layouts.app')

@section('title', 'Mutasi Stok | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Kelola Stok')

@section('breadcrumb', 'Mutasi Stok')

@section('content')
<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 38px !important;
    }
</style>

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Success!</h5>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-times"></i> Error!</h5>
    {{ session('error') }}
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
                    <form action="{{ route('simpanMutasi') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="produkId">SKU / Nama Produk<span class="text-danger">*</span></label>
                            <select id="produkId" class="form-control" name="produkId" required>
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipe">Kategori Mutasi<span class="text-danger">*</span></label>
                            <select class="form-control" id="kategori" name="kategori" required>
                                <option value="" default>Pilih Kategori Mutasi</option>
                                <option value="masuk">Mutasi Masuk</option>
                                <option value="keluar">Mutasi Keluar</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Jenis Mutasi<span class="text-danger">*</span></label>
                            <select class="form-control" id="jenis" name="jenis" required>
                                <option value="" default>Pilih Jenis Mutasi</option>

                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        //select2
        $('#produkId').select2({
            placeholder: 'Pilih SKU / Nama Produk',
            allowClear: true,
            ajax : {
                url : "{{ route('stok.showAllProducts') }}",
                dataType : 'json',
                delay : 250,
                processResults : function(data) {
                    return {
                        results : $.map(data, function(item) {
                            return {
                                text : item.kode_produk + ' - ' + item.nama_produk,
                                id : item.id
                            }
                        })
                    };
                },
                cache : true
            }
        });

        //option jenis mutasi
        $('#kategori').change(function() {
            var kategori = $(this).val();
            var jenis = $('#jenis');
            jenis.empty();
            if(kategori == 'masuk') {
                jenis.append('<option value="pembelian">Pembelian</option>');
                jenis.append('<option value="penerimaan barang">Penerimaan Barang</option>');
                jenis.append('<option value="retur pembelian">Retur Pembelian</option>');
                jenis.append('<option value="hibah">Hibah</option>');
                jenis.append('<option value="penyesuaian stok masuk">Penyesuaian Stok Masuk</option>');
            } else {
                jenis.append('<option value="penjualan">Penjualan</option>');
                jenis.append('<option value="pengiriman barang">Pengiriman Barang</option>');
                jenis.append('<option value="retur penjualan">Retur Penjualan</option>');
                jenis.append('<option value="penggunaan produksi">Penggunaan Produksi</option>');
                jenis.append('<option value="kerusakan barang">Kerusakan Barang</option>');
                jenis.append('<option value="penyesuaian stok keluar">Penyesuaian Stok Keluar</option>');
            }
        });
    });
</script>
@endsection