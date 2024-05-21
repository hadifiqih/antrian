@extends('layouts.app')

@section('title', 'Perbarui Profil')

@section('username', Auth::user()->name)

@section('page', 'Profil')

@section('breadcrumb', 'Ubah Profil')

@section('style')
{{-- Link CSS Cropper from node_modules --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" integrity="sha512-cyzxRvewl+FOKTtpBzYjW6x6IAYUCZy3sGP40hn+DQkqeluGRCax7qztK2ImL64SA+C7kVWdLI6wvdlStawhyw==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
<style>
    .select2-selection__choice {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: #fff !important;
        padding: 0 10px !important;
    }
    .select2-selection__choice__remove {
        color: #fff !important;
        
    }
</style>
@endsection

@section('content')

{{-- Jika ada session success --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Sukses!</strong> {{ session('success') }}
</div>
@endif

{{-- Jika ada session error --}}
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Whoops!</strong> {{ session('error') }}
</div>
@endif

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <!-- Profile Image -->
            <div class="card">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img id="image" class="profile-user-img img-fluid img-circle" src="{{ Auth::user()->employee->photo == null ? asset('adminlte/dist/img/user-kosong.png') :  asset('storage/profile/'. Auth::user()->employee->photo)  }}" alt="User profile picture">
                    </div>
                    </img>
                        <div class="text-center mt-2">

                            <input type="file" id="photo" name="photo" style="display:none;">
                            <label for="photo" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Ubah Foto
                            </label>
                        </div>
                <h5 class="text-primary m-0">Informasi Akun</h5>
                <hr>
                <form action="{{ route('employee.update', Auth::user()->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap<span class="text-danger">*</span></label>
                    <input type="nama" class="form-control" id="nama" name="nama" value="{{ Auth::user()->name }}">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" autocomplete="on">
                </div>

                <div class="mb-3">
                    <label for="telepon" class="form-label">Telepon<span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="telepon" name="telepon" value="{{ Auth::user()->phone }}">
                </div>

                <div class="form-group">
                    <label for="divisi" class="form-label">Divisi<span class="text-danger">*</span></label>
                    <select id="divisi" class="custom-select rounded-1" name="divisi">
                        <option value='' {{ Auth::user()->employee->divisi == null ? 'selected disabled' : '' }}>Pilih Divisi</option>
                        <option value="produksi" {{ Auth::user()->employee->division == 'Produksi' ? 'selected' : '' }}>Produksi</option>
                        <option value="sales" {{ Auth::user()->employee->division == 'Sales' ? 'selected' : '' }}>Pemasaran & Penjualan</option>
                        <option value="desain" {{ Auth::user()->employee->division == 'Desain' ? 'selected' : '' }}>Desain Grafis</option>
                        <option value="keuangan" {{ Auth::user()->employee->division == 'Keuangan' ? 'selected' : '' }}>Keuangan & Administrasi</option>
                        <option value="logistik" {{ Auth::user()->employee->division == 'Logistik' ? 'selected' : '' }}>Logistik & Pengiriman</option>
                    </select>
                </div>
            </div>
        </div>
            <div class="card">
                <div class="card-body">
                <h5 class="text-primary m-0">Data Identitas</h5>
                <hr>

                <div class="mb-3">
                    <label for="nip" class="form-label">NIP (Nomor Induk Pegawai)<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nip" name="nip" value="{{ $employee->nip }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ $employee->where_born }}" placeholder="{{ $employee->where_born == null ? 'Contoh : Malang' : '' }}" {{ $employee->where_born ? 'readonly' : '' }}>
                </div>

                <div class="mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir<span class="text-danger">*</span></label>
                    @if($employee->date_of_birth != null)
                    <input type="text" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ $employee->date_of_birth }}" readonly>
                    @else
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                    @endif
                </div>

                <div class="mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin<span class="text-danger">*</span></label>
                    @if($employee->jenis_kelamin == null)
                    <select id="jenis_kelamin" class="custom-select rounded-1" name="jenis_kelamin">
                        <option value='' {{ $employee->jenis_kelamin == null ? 'selected disabled' : '' }}>Pilih Jenis Kelamin</option>
                        <option value="pria" {{ $employee->jenis_kelamin == 'pria' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="wanita" {{ $employee->jenis_kelamin == 'wanita' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @else
                    <input type="text" class="form-control" id="jenis_kelamin" name="jenis_kelamin" value="{{ ucwords($employee->jenis_kelamin) }}" readonly>
                    @endif
                </div>

                <div class="mb-3">
                    {{-- Alamat --}}
                    <label for="alamat" class="form-label">Alamat<span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="{{ $employee->address == null ? 'Contoh : Jl. Raya Tlogomas No. 246' : '' }}">{{ $employee->address }}</textarea>
                </div>

                <div class="mb-3">
                    {{-- Jabatan --}}
                    <label for="jabatan" class="form-label">Jabatan<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="jabatan" name="jabatan" value="{{ ucwords(Auth::user()->role) }}" placeholder="{{ Auth::user()->role == null ? 'Contoh : Supervisor' : '' }}" readonly>
                </div>

                <div class="mb-3">
                    {{-- Office --}}
                    <label for="office" class="form-label">Office<span class="text-danger">*</span></label>
                    <select id="office" class="custom-select rounded-1" name="office">
                        <option value="" selected disabled>Pilih Office</option>
                        <option value="Surabaya" {{ $employee->office == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                        <option value="Malang" {{ $employee->office == 'Malang' ? 'selected' : '' }}>Malang</option>
                        <option value="Kediri" {{ $employee->office == 'Kediri' ? 'selected' : '' }}>Kediri</option>
                    </select>
                </div>

                <div class="mb-3">
                    {{-- Tanggal Mulai Kerja --}}
                    <label for="tanggalMulaiKerja" class="form-label">Tanggal Mulai Kerja<span class="text-danger">*</span></label>
                    <input type="{{ $employee->joining_date == null ? 'date' : 'text' }}" class="form-control" id="tanggalMulaiKerja" value="{{ $employee->joining_date == null ? '' : $employee->joining_date }}" name="tanggalMulaiKerja" {{ $employee->joining_date == null ? '' : 'readonly' }}>
                </div>
            </div>
            </div>

            <div class="card">
                <div class="card-body">
                <h5 class="text-primary m-0">Informasi Bank</h5>
                <hr>

                <div class="mb-3">
                    {{-- Nama Bank --}}
                    <label for="namaBank" class="form-label">Nama Bank<span class="text-danger">*</span></label>
                    <select id="namaBank" class="custom-select rounded-1" name="bank_name">
                        <option value="bca" {{ $employee->bank_name == 'bca' ? 'selected' : '' }}>BCA</option>
                        <option value="bni" {{ $employee->bank_name == 'bni' ? 'selected' : '' }}>BNI</option>
                        <option value="bri" {{ $employee->bank_name == 'bri' ? 'selected' : '' }}>BRI</option>
                        <option value="mandiri" {{ $employee->bank_name == 'mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="bsi" {{ $employee->bank_name == 'bsi' ? 'selected' : '' }}>BSI</option>
                        <option value="cimb" {{ $employee->bank_name == 'cimb' ? 'selected' : '' }}>CIMB</option>
                        <option value="btpn" {{ $employee->bank_name == 'btpn' ? 'selected' : '' }}>BTPN</option>
                        <option value="jago" {{ $employee->bank_name == 'jago' ? 'selected' : '' }}>Bank Jago</option>
                        <option value="jenius" {{ $employee->bank_name == 'jenius' ? 'selected' : '' }}>Jenius</option>
                        <option value="permata" {{ $employee->bank_name == 'permata' ? 'selected' : '' }}>Permata</option>
                        <option value="blu" {{ $employee->bank_name == 'blu' ? 'selected' : '' }}>blu by BCA Digital</option>
                    </select>
                </div>

                <div class="mb-3">
                    {{-- Nomer Rekening --}}
                    <label for="nomerRekening" class="form-label">Nomer Rekening<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="nomerRekening" name="nomerRekening" value="{{ $employee->bank_account }}" placeholder="{{ $employee->bank_account == null ? 'Contoh : 1234567890' : '' }}">
                </div>
                </div>
                </div>
                <input type="submit" class="btn btn-primary btn-block" value="Simpan"></input>
            </form>
        </div>
    </div>
</div>
</div>
@if(Auth::user()->role_id == 16 || Auth::user()->role_id == 17)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary">Kemampuan Desain</h5>
                    <hr>

                    <div class="form-group mb-3">
                        <label for="skill" class="form-label">Produk apa saja yang dapat Anda desain ?<span class="text-danger">*</span></label>
                        <select id="skill" class="custom-select rounded-1 select2" name="skill" style="width: 100%" multiple="multiple" >
                            
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-sm btn-primary" id="btnSubmitSkill">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
@includeIf('auth.modal.modal-upload-photo')
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" integrity="sha512-6lplKUSl86rUVprDIjiW8DuOniNX8UDoRATqZSds/7t6zCQZfaCe3e5zcGaQwxa8Kpn5RTM9Fvl3X2lLV4grPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function readURL(input){
        if(input.files && input.files[0]){
            var reader = new FileReader();

            reader.onload = function(e){
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#photo").change(function(){
        readURL(this);
    });
    
    $(function () {
        bsCustomFileInput.init();
    });

    $(document).ready(function(){
        var userLogin = {{ Auth::user()->id }};

        $('.select2').select2({
            placeholder: 'Pilih produk',
            allowClear: true,
            ajax: {
                url: '{{ route("getAllJobs") }}',
                dataType: 'json',
                delay: 250,
                processResults: function(data){
                    return {
                        results: $.map(data, function(item){
                            return {
                                text: item.job_name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        $.ajax({
            type: 'GET',
            url: '/design/get-skill-by-id/' + userLogin,
            success: function(data){
                data.forEach(function(job){
                    var option = new Option(job.job_name, job.job_id, true, true);
                    $('#skill').append(option).trigger('change');

                    $('#skill').trigger({
                        type: 'select2:select',
                        params: {
                            data: data
                        }
                    });
                });
            }
        });

        $('#btnSubmitSkill').on('click', function(){
            var skill = $('#skill').val();
            $('#btnSubmitSkill').attr('disabled', true);

            $.ajax({
                type: 'POST',
                url: '/design/add-skill',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'skill': skill,
                    'user_id': userLogin
                },
                success: function(data){
                    var Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Kemampuan desain berhasil diperbarui!'
                    });
                },
                error: function(data){
                    var Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                    });
                    Toast.fire({
                        icon: 'error',
                        title: 'Kemampuan desain gagal diperbarui!'
                    });

                    $('#btnSubmitSkill').attr('disabled', false);
                }
            });
        });
    });
</script>
<script>
    var $modal = $('#modal-photo');
    var image = document.getElementById('img-preview');

        $('body').on('change', '#photo', function (e) {
            var files = e.target.files;
            var done = function (url) {
                image.src = url;
                $modal.modal('show');
            };
            var reader;
            var file;
            var url;

            if (files && files.length > 0) {
                file = files[0];

                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function (e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        $modal.on('shown.bs.modal', function () {
            cropper = new Cropper(image, {
                aspectRatio: 1 / 1,
                viewMode: 1,
                zoomable: true,
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
            cropper = null;
        });

        $('body').on('click', '#crop', function () {
            canvas = cropper.getCroppedCanvas({
                maxWidth: 4096,
                maxHeight: 4096,
            });

            canvas.toBlob(function (blob) {
                url = URL.createObjectURL(blob);
                var reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function () {
                    var base64data = reader.result;

                    $.ajax({
                        type: "POST",
                        url: "{{ route('employee.uploadFoto') }}",
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'photo': base64data,
                        },
                        success: function (data) {
                            $modal.modal('hide');
                            var Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'Foto berhasil diperbarui!'
                            });
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        },
                        error: function (data) {
                            $modal.modal('hide');
                            var Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000
                            });
                            Toast.fire({
                                icon: 'error',
                                title: 'Foto gagal diperbarui!'
                            });
                        },
                    });
                }
            });
        });
</script>

@endsection
