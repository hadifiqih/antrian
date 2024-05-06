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
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Mutasi Stok</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('simpanMutasi') }}" method="POST" enctype="text/plain">
                        @csrf
                        <div class="form-group">
                            <label for="skuNama">SKU / Nama Produk</label>
                            <select id="skuNama" class="form-control" name="skuNama">
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                        </div>
                        <div class="form-group">
                            <label for="tipe">Tipe</label>
                            <select class="form-control" id="tipe" name="tipe" required>
                                <option value="1">Stok Masuk</option>
                                <option value="2">Stok Keluar</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" required></textarea>
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
        $('#skuNama').select2({
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
    });
</script>
@endsection