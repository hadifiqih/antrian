@extends('layouts.app')

@section('title', 'Upload File Cetak | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Antrian')

@section('breadcrumb', 'Detail Desain')

@section('style')
{{-- Dropzone --}}
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
@endsection

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

@if(session('message'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('message')}}
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
                    <h5>Upload File Cetak</h5>
                </div>
                <div class="card-body">
                    <form id="simpanFile" action="{{ route('design.simpanFile', $design->id) }}" method="POST" enctype="multipart/form-data" id="simpanFile">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="aktifLink">
                                <label class="custom-control-label" for="aktifLink">File Berukuran Besar?</label>
                            </div>
                        </div>

                        <div class="form-group" style="display: none">
                            <label for="note">Link File</label>
                            <input type="text" class="form-control" id="linkFile" name="linkFile" value="">
                            @if($errors->has('linkFile'))
                            <small class="text-danger">{{ $errors->first('linkFile') }}</small>
                            @endif
                        </div>

                        <div id="inputFileForm" class="form-group">
                            <label for="fileCetak">File Cetak</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="fileCetak" class="custom-file-input {{ $errors->has('fileCetak') ? 'is-invalid' : '' }}" id="fileCetak" accept="image/jpg, image/jpeg, image/png, application/pdf, .cdr, .ai" required>
                                    <label class="custom-file-label" for="fileCetak">Pilih file</label>
                                </div>
                            </div>
                            @if($errors->has('fileCetak'))
                            <small class="text-danger">{{ $errors->first('fileCetak') }}</small>
                            @endif
                        </div>
                        <button id="submitUnggahCetak" type="submit" class="btn btn-sm btn-outline-primary">Unggah</button>
                    </form>
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6><strong><i class="fas fa-info-circle"></i> Informasi</strong></h6>
                            <ul>
                                <li>File cetak yang diunggah harus berformat .pdf, .cdr, .ai, atau .jpg</li>
                                <li>Resolusi gambar minimal 300 dpi</li>
                                <li>Mode warna yang digunakan harus CMYK (Untuk Cetak Rekanan)</li>
                                <li>Ukuran file maksimal 50 MB</li>
                                <li>Jika ukuran file > 50MB, silahkan upload di Google Drive, lalu bagikan, dan tempel link file pada form diatas</li>
                                <li>Pastikan font sudah di <strong class="text-danger">Convert To Curves</strong> untuk menghindari missing font</li>
                                <li>Jika ada kesalahan cetak yang berasal dari file cetak, biaya kerugian cetak akan ditangguhkan kepada pihak yang terlibat</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        const $aktifLink = $('#aktifLink');
        const $linkFile = $('#linkFile');
        const $fileCetak = $('#fileCetak');
        const $inputFileForm = $('#inputFileForm');
        const $submitUnggahCetak = $('#submitUnggahCetak');
        const $customFileLabel = $('.custom-file-label');
        const $simpanFile = $('#simpanFile');

        //fungsi untuk menampilkan dan menyembunyikan form
        function toggleForms() {
            const isChecked = $aktifLink.is(':checked');
            $linkFile.attr('required', isChecked);
            $fileCetak.attr('required', !isChecked);
            $fileCetak.parent().parent().toggle(!isChecked);
            $linkFile.parent().toggle(isChecked);
            $inputFileForm.toggle(!isChecked);
            $linkFile.val('');
            $fileCetak.val('');
            $customFileLabel.text('Pilih file');
        }

        //initial setup
        toggleForms();

        //jika aktif link maka tampilkan form link file dan sembunyikan upload file dan sebaliknya
        $aktifLink.on('click', toggleForms);

        //event handler untuk submit button
        $submitUnggahCetak.on('click', function(e) {
            e.preventDefault();

            if (!$aktifLink.is(':checked')) {
                if ($fileCetak.val() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'File cetak tidak boleh kosong !'
                    });
                    return;
                }
            }else{
                if ($linkFile.val() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Link file tidak boleh kosong !'
                    });
                    return;
                }
            }

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "File cetak yang diupload sudah benar ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Unggah !'
            }).then((result) => {
                if (result.isConfirmed) {
                    //proses submit form
                    $simpanFile.submit();  
                }
            });
        });
    });
</script>
@endsection