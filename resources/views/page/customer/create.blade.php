@extends('layouts.app')

@section('title', 'Tambah Data Pelanggan')

@section('breadcrumb', 'Tambah Data Pelanggan')

@section('page', 'Kontak')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tambah Data Pelanggan</h5>
                </div>
                <div class="card-body">
                    <form id="pelanggan-form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="nama">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modalNama" placeholder="Nama Pelanggan" name="namaPelanggan" required>
                        </div>
            
                        <div class="form-group">
                            <label for="noHp">No. HP / WA <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="modalTelepon" placeholder="Nomor Telepon" name="telepon" required>
                        </div>
            
                        <div class="form-group">
                            <label for="alamat">Alamat <span class="text-danger">*</span></label>
                            <textarea rows="3" class="form-control" id="modalAlamat" placeholder="Alamat Pelanggan" name="alamat" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="instansi">Instansi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modalInstansi" placeholder="Instansi Pelanggan" name="instansi" required>
                        </div>
                        <div class="form-group">
                            <label for="infoPelanggan">Sumber Pelanggan <span class="text-danger">*</span></label>
                            <select class="custom-select select2" id="infoPelanggan" name="infoPelanggan" required>
                                <option value="" selected>Pilih Sumber Pelanggan</option>
                                @foreach($sumberAll as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="provinsi">Provinsi <span class="text-danger">*</span></label>
                            <select class="custom-select" name="provinsi" id="modalProvinsi" required>
                                <option value="" selected>Pilih Provinsi</option>
                            </select>
                        </div>
                        <div class="form-group" id="groupKota">
                            <label for="kota">Kabupaten/Kota <span class="text-danger">*</span></label>
                            <select class="custom-select " name="kota" id="modalKota" required>
                                <option value="" selected>Pilih Kota</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="history.back()">Kembali</button>
                        <input type="submit" class="btn btn-primary" id="submitPelanggan" value="Tambah"><span id="loader" class="loader" style="display: none;"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    var provinsi = $('#modalProvinsi').val();
    //function provinsi
    $.ajax({
        url: "{{ route('getProvinsi') }}",
        method: "GET",
        success: function(data){
            //foreach provinsi
            $.each(data, function(key, value){
                $('#modalProvinsi').append(`
                    <option value="${key}">${value}</option>
                `);
            });
        }
    });

    //function kota
    $.ajax({
        url: "{{ route('getKota') }}",
        method: "GET",
        data: {
            provinsi: provinsi
        },
        success: function(data){
            //foreach kota
            $.each(data, function(key, value){
                $('#modalKota').append(`
                    <option value="${key}">${value}</option>
                `);
            });
        }
    });

    // function kota
    $('#modalProvinsi').on('change', function(){
        provinsi = $(this).val();

        $('#modalKota').empty();
        $('#modalKota').append(`<option value="" selected disabled>Pilih Kota</option>`);
        $.ajax({
            url: "{{ route('getKota') }}",
            method: "GET",
            delay: 250,
            data: {
                provinsi: provinsi
            },
            success: function(data){
                //foreach kota
                $.each(data, function(key, value){
                    $('#modalKota').append(`
                        <option value="${key}">${value}</option>
                    `);
                });
            }
        });
    });
    // function submit
    $('#pelanggan-form').on('submit', function(e){
        e.preventDefault();
        //disabled button
        $('#submitPelanggan').attr('disabled', 'disabled');

        var nama = $('#modalNama').val();
        var telepon = $('#modalTelepon').val();
        var alamat = $('#modalAlamat').val();
        var instansi = $('#modalInstansi').val();
        var infoPelanggan = $('#infoPelanggan').val();
        var provinsi = $('#modalProvinsi').val();
        var kota = $('#modalKota').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('customer.store') }}",
            method: "POST",
            data: {
                nama: nama,
                telepon: telepon,
                alamat: alamat,
                instansi: instansi,
                infoPelanggan: infoPelanggan,
                provinsi: provinsi,
                kota: kota,
                _token: _token
            },
            success: function(response){
                Swal.fire({
                    title: 'Berhasil',
                    text: 'Data pelanggan berhasil diperbarui',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(function(){
                    window.location.href = "{{ route('customer.index') }}";
                });
            }
        });
    });
</script>
@endsection