<!-- resources/views/cashier/index.blade.php -->

@extends('layouts.app')

@section('title', 'POS Kasir | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Tambah Transaksi')

@section('content')
<style>
    .select2-selection {
        height: 34px !important;
    }
</style>
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-primary shadow">
                <div class="card-header bg-primary text-white">
                    {{ __('Kasir POS Bahan') }}
                </div>
            <div class="card-body">
                <input type="hidden" id="keranjang_id" value="">

                <button class="btn btn-sm btn-warning mb-2">Tambah Pelanggan</button>

                <div class="form-group mb-3">
                    <label for="product" class="form-label">{{ __('Nama Pelanggan') }}</label>
                    <select class="custom-select select2" id="nama_pelanggan">

                    </select>
                </div>

            <button id="btnTambahProduk" onclick="modalPilihProduk()" class="btn btn-primary btn-sm mt-3">{{ __('Tambah Produk') }}</button>

            <div class="table-responsive">
                <table id="tableItems" class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Nama Produk') }}</th>
                            <th scope="col">{{ __('Harga') }}</th>
                            <th scope="col">{{ __('Qty') }}</th>
                            <th scope="col">{{ __('Diskon') }}</th>
                            <th scope="col">{{ __('Total') }}</th>
                            <th scope="col">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>


            <div class="row mt-3">
                <div class="col-md-12">
                    <h5 class="font-weight-bold bg-dark p-2 rounded">{{ __('Total: Rp ') }}<span id="total">0</span></h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mt-2">
                    <button class="btn btn-success btn-sm"><i class="fas fa-cart-plus"></i> {{ __('Checkout') }}</button>
                </div>
            </div>
            </div>
            </div>
        </div>
    </div>
</div>
@includeIf('page.kasir.modal.pilih-produk')
@endsection

@section('script')
{{-- maskmoney --}}
<script src="{{ asset('adminlte/dist/js/maskMoney.min.js') }}"></script>
<script>
    //modal pilih produk
    function modalPilihProduk() {
        if($('#nama_pelanggan').val() == null){
            Swal.fire('Peringatan', 'Pilih pelanggan terlebih dahulu', 'warning')
        }else{
            $('#pilihProduk').modal('show');
        }
    }

    //tambah keranjang
    function tambahItem(id){
        $.ajax({
            url: '/pos/tambah-item/' + id,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                keranjang_id: $('#keranjang_id').val()
            },
            success: function(data){
                $('#tableItems').DataTable().ajax.reload();
            }
        });
    }

    $(document).ready(function() {
        //mask money
        $('#diterima').maskMoney({
            prefix: 'Rp ',
            thousands: '.',
            decimal: ',',
            precision: 0
        });

        //menampilkan data pelanggan
        $('#nama_pelanggan').select2({
            placeholder: 'Pilih Pelanggan',
            ajax: {
                url: "/pelanggan-all/{{ auth()->user()->sales->id }}",
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.nama,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        //membuat keranjang baru
        $('#nama_pelanggan').on('change', function(){
            var customer = $('#nama_pelanggan').val();

            $.ajax({
                url: '/pos/setup-keranjang',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    customer_id: customer
                },
                success: function(data){
                    console.log(data);
                    $('#keranjang_id').val(data.id);

                    $('#tableItems').DataTable({
                        processing: true,
                        serverSide: true,
                        searching: false,
                        ordering: false,
                        paging: false,
                        info: false,
                        ajax: "/pos/keranjang-item/" + $('#keranjang_id').val(),
                        columns: [
                            { data: 'nama_produk', name: 'nama_produk' },
                            { data: 'harga', name: 'harga' },
                            { data: 'qty', name: 'qty' },
                            { data: 'diskon', name: 'diskon' },
                            { data: 'total', name: 'total' },
                            { data: 'action', name: 'action' }
                        ]
                    });
                }
            });
        });

        //menampilkan data produk
        $('#tableProduct').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: "{{ route('pos.pilihProduk') }}",
            columns: [
                {data: 'nama_produk', name: 'nama_produk'},
                {data: 'harga_jual', name: 'harga_jual'},
                {data: 'stok', name: 'stok'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
        });
    });
</script>

@endsection
