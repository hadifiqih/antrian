@extends('layouts.app')

@section('title', 'POS Kasir | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Ubah Produk')

@section('content')

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Whoops!</strong> Terdapat kesalahan dalam input data:
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Ubah Produk</h3>
                </div>
                <div class="card-body">
                    <form id="formUbahProduk">
                        <input type="hidden" id="produkId" name="produkId" value="{{ $produk->id }}">
                        <div class="form-group">
                            <label for="kode_produk">Kode Produk</label>
                            <input type="text" class="form-control" id="kode_produk" name="kode_produk" value="{{ $produk->kode_produk }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="nama_produk">Nama Produk</label>
                            <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="{{ $produk->nama_produk }}">
                        </div>
                        <div class="form-group">
                            <label for="harga_kulak">Harga Kulak</label>
                            <input type="text" class="form-control maskMoney" id="harga_kulak" name="harga_kulak" value="{{ isset($harga->harga_kulak) ? $harga->harga_kulak : '' }}">
                        </div>
                        <div class="form-group">
                            <label for="harga_jual">Harga Jual</label>
                            <input type="text" class="form-control maskMoney" id="harga_jual" name="harga_jual" value="{{ isset($harga->harga_jual) ? $harga->harga_jual : '' }}">
                        </div>
                        @if(!isset($stok))
                        <div class="form-group">
                            <label for="stok">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" value="{{ isset($stok->jumlah_stok) ? $stok->jumlah_stok : '' }}">
                        </div>
                        @else
                        <input type="hidden" id="stok" name="stok" value="{{ $stok->jumlah_stok }}">
                        @endif
                        <div class="form-group">
                            <label for="grosir">Grosir</label>
                            <button id="btnTambahGrosir" type="button" class="btn btn-sm btn-outline-primary ml-2"><i class="fas fa-plus"></i> Tambah Harga Grosir</button>
                        </div>
                        <div class="table-responsive">
                            <table id="tabelGrosir" class="table table-bordered" @if(!isset($grosir)) style="display: none;" @endif>
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Jumlah Min.</th>
                                        <th>Jumlah Max.</th>
                                        <th>Harga Satuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($grosir != null)
                                        @foreach($grosir as $g)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><input type="number" class="form-control" name="min[]" value="{{ $g->min_qty }}"></td>
                                            <td><input type="number" class="form-control" name="max[]" value="{{ $g->max_qty }}"></td>
                                            <td><input type="text" class="form-control maskMoney" name="harga[]" value="{{ $g->harga_grosir }}"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <button id="btnTambahProduk" type="submit" class="btn btn-sm btn-primary float-right">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('adminlte/dist/js/maskMoney.min.js') }}"></script>
<script>
    function removeCurrency(value) {
        return value.replace('Rp ', '').replace(/\./g, '').replace(/,/g, '');
    }

    $(document).ready(function() {
        // Mask Money
        $('.maskMoney').maskMoney({prefix:'Rp ', thousands:'.', decimal:',', precision:0});

        // Tambah Grosir
        $('#btnTambahGrosir').click(function() {
            $('#tabelGrosir').show();
            $('#tabelGrosir tbody').append(`
                <tr>
                    <td></td>
                    <td><input type="number" class="form-control" name="min[]" placeholder="Contoh : 5"></td>
                    <td><input type="number" class="form-control" name="max[]" placeholder="Contoh : 10"></td>
                    <td><input type="text" class="form-control maskMoney" name="harga[]" placeholder="Contoh : Rp 10.000"></td>
                    <td><button type="button" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                </tr>
            `);
            $('#tabelGrosir tbody tr').each(function(index) {
                $(this).find('td').eq(0).text(index + 1);
            });
            $('.maskMoney').maskMoney({prefix:'Rp ', thousands:'.', decimal:',', precision:0});
        });

        // Hapus Grosir
        $('#tabelGrosir').on('click', 'tbody tr td button', function() {
            $(this).closest('tr').remove();
            $('#tabelGrosir tbody tr').each(function(index) {
                $(this).find('td').eq(0).text(index + 1);
            });
        });

        // Tambah Produk
        $('#formUbahProduk').submit(function(e) {
            e.preventDefault();
            var token = $('meta[name="csrf-token"]').attr('content');
            var id_produk = $('#produkId').val();
            var kode_produk = $('#kode_produk').val();
            var nama_produk = $('#nama_produk').val();
            var kulak = $('#harga_kulak').val();
            var jual = $('#harga_jual').val();
            var stok = $('#stok').val();
            var min = [];
            var max = [];
            var harga = [];
            $('#tabelGrosir tbody tr').each(function() {
                min.push($(this).find('td').eq(1).find('input').val());
                max.push($(this).find('td').eq(2).find('input').val());
                harga.push(removeCurrency($(this).find('td').eq(3).find('input').val()));
            });

            $.ajax({
                url: "{{ route('pos.updateProduct', $produk->id) }}",
                type: "PUT",
                data: {
                    _token: token,
                    id_produk: id_produk,
                    kode_produk: kode_produk,
                    nama_produk: nama_produk,
                    harga_kulak: kulak,
                    harga_jual: jual,
                    stok: stok,
                    min: min,
                    max: max,
                    harga: harga
                },
                success: function(response) {
                    console.log(response);
                    if(response.status == 200) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if(result.isConfirmed) {
                                window.location.href = "{{ route('pos.manageProduct') }}";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
                });
            });
        });
</script>
@endsection