@extends('layouts.app')

@section('title', 'Tambah Aktivitas | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Aktivitas')

@section('breadcrumb', 'Tambah Aktivitas')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Tambah Aktivitas</h5>
                </div>
                <div class="card-body">
                    <form id="formTambahAktivitas" action="{{ route('task.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="nama_task">Nama Aktivitas <span class="text-danger">*</span></label>
                            <input type="text" name="nama_task" id="nama_task" class="form-control" placeholder="Follow Up PT. ABC" required>
                        </div>
                        <div class="form-group">
                            <label for="rincian">Rincian / Rencana</label>
                            <textarea name="rincian" id="rincian" class="form-control" rows="3" placeholder="Melakukan penawaran stempel beton"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="hasil">Hasil / Langkah Selanjutnya</label>
                            <textarea name="hasil" id="hasil" class="form-control" rows="3" placeholder="Closing order stempel beton dengan omset Rp. X.XXX.XXX"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="lampiran">Lampiran</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input name="lampiran[]" type="file" class="custom-file-input" id="lampiran" multiple="multiple">
                                    <label class="custom-file-label" for="lampiran">Pilih file</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="namaKontak">Nama Kontak</label>
                            <select id="customerId" name="customerId" class="form-control select2" style="width: 100%;">

                            </select>
                            <button id="btnTambahPelanggan" type="button" class="btn btn-primary btn-sm mt-3">Tambah Kontak</button>
                        </div>
                        <br>
                        <h5>Informasi Aktivitas</h5>
                        <hr>
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1">Belum Selesai</option>
                                <option value="2">Proses</option>
                                <option value="3">Selesai</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="batasWaktu">Batas Waktu</label>
                            <input type="datetime-local" name="batasWaktu" id="batasWaktu" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="akhirBatas">Akhir Batas Waktu</label>
                            <input type="datetime-local" name="akhirBatas" id="akhirBatas" class="form-control">
                        </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('task.index') }}" class="btn btn-secondary">Batal</a>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@includeIf('page.antrian-workshop.modal.modal-tambah-pelanggan')
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // function provinsi
        $('#provinsi').select2({
            ajax: {
                url: `{{ route('getProvinsi') }}`,
                dataType: 'json',
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });
        //fungsi submit form
        $('#formTambahAktivitas').submit(async function (event){
            event.preventDefault();
            
            //BUAT FORM DATA
            var formData = new FormData(this);

            //ajax untuk mengirim data
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "{{ route('task.index') }}";
                    });
                },
                error: function(data) {
                    alert('Data gagal disimpan');
                }
            });
        });

        //fungsi select2
        $('#customerId').select2({
            placeholder: 'Pilih kontak',
            ajax: {
                url: "/api/customer",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Authorization' : 'Bearer ' + localStorage.getItem('api-token')
                },
                data: function(params) {
                    return {
                        q: params.term,
                        sales: `{{ Auth::user()->sales->id }}`
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
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

        //fungsi modal tambah pelanggan
        $('#btnTambahPelanggan').click(function() {
            $('#modalTambahPelanggan').modal('show');
        });

        // function simpanPelanggan
        $('#pelanggan-form').on('submit', function(e){
            e.preventDefault();
            
            var data = $(this).serialize();

            $('#modalTambahPelanggan #subPelanggan').val('Menyimpan...').prop('disabled', true);

            $.ajax({
                url: "{{ route('task.simpanPelanggan') }}",
                method: "POST",
                data: data,
                success: function(data){

                    $('#modalTambahPelanggan').modal('hide');
                    $('#modalTambahPelanggan #subPelanggan').val('Simpan').prop('disabled', false);
                    $('#pelanggan-form')[0].reset();

                    //tampilkan toast sweet alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Pelanggan berhasil ditambahkan',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr, status, error){
                    var err = eval("(" + xhr.responseText + ")");
                    alert(err.Message);
                }
            });
        });

        // function kota
        $('#provinsi').on('change', function(){
            var provinsi = $(this).val();
            $('#groupKota').show();
            $('#kota').empty().append(`<option value="" selected disabled>Pilih Kota</option>`);
            $('#kota').select2({
                ajax: {
                    url: `{{ route('getKota') }}`,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            provinsi: provinsi
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });
        });
    });
</script>
@endsection