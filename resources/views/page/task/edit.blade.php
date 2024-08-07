@extends('layouts.app')

@section('title', 'Edit Aktivitas | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Aktivitas')

@section('breadcrumb', 'Edit Aktivitas')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<style>
    .attachment-wrapper {
        position: relative;
        display: inline-block;
    }
    .attachment-wrapper img {
        width: 100%;
        height: auto;
        display: block;
    }
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .attachment-wrapper:hover .overlay {
        opacity: 1;
    }
    .overlay i {
        font-size: 2rem;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Edit Aktivitas</h5>
                </div>
                <div class="card-body">
                    <form id="editAktivitas" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <input type="hidden" id="taskId" name="id" value="{{ $task->id }}">
                        <div class="form-group">
                            <label for="nama_task">Nama Aktivitas <span class="text-danger">*</span></label>
                            <input type="text" name="nama_task" id="nama_task" class="form-control" value="{{ $task->nama_task }}" placeholder="Follow Up PT. ABC" required>
                        </div>
                        <div class="form-group">
                            <label for="rincian">Rincian / Rencana</label>
                            <textarea name="rincian" id="rincian" class="form-control" rows="3" placeholder="Melakukan penawaran stempel beton">{{ $task->rincian }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="hasil">Hasil / Langkah Selanjutnya</label>
                            <textarea name="hasil" id="hasil" class="form-control" rows="3" placeholder="Closing order stempel beton dengan omset Rp. X.XXX.XXX">{{ $task->hasil }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="namaKontak">Nama Kontak</label>
                            <select id="customerId" name="customerId" class="form-control select2" style="width: 100%;">
                                <option value="{{ $task->customer_id }}">{{ $task->customer->nama ?? 'Pilih Kontak' }}</option>
                            </select>
                            <button id="btnTambahPelanggan" type="button" class="btn btn-primary btn-sm mt-3">Tambah Kontak</button>
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
                        {{-- display attachment image --}}
                        <div class="card">
                            <div class="card-header">
                                <h5>Lampiran</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($task->attachments as $attachment)
                                    <div class="col-md-2">
                                        <div class="attachment-wrapper">
                                            <img src="{{ asset('storage/lampiran/' . $attachment->file_name) }}" class="img-fluid" alt="attachment">
                                            <a onclick="deleteLampiran({{ $attachment->id }})">
                                                <div class="overlay">
                                                    <i class="fas fa-trash-alt"></i>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    
                        <br>
                        <h5>Informasi Aktivitas</h5>
                        <hr>
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1" {{ $task->status == 1 ? 'selected' : '' }}>Belum Selesai</option>
                                <option value="2" {{ $task->status == 2 ? 'selected' : '' }}>Proses</option>
                                <option value="3" {{ $task->status == 3 ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="batasWaktu">Batas Waktu</label>
                            <input type="datetime-local" name="batasWaktu" id="batasWaktu" class="form-control" value="{{ old('batasWaktu', $task->batas_waktu) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="akhirBatas">Akhir Batas Waktu</label>
                            <input type="datetime-local" name="akhirBatas" id="akhirBatas" class="form-control" value="{{ old('akhirBatas', $task->akhir_batas_waktu) }}">
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
    function deleteLampiran(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/attachment/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        location.reload();
                    }
                });
            }
        });
    }

$(document).ready(function(){
    // preselected customer select2
    $('#customerId').select2({
        placeholder: 'Pilih Kontak',
        ajax: {
            url: '/api/customer',
            headers: {
                'Authorization' : 'Bearer ' + localStorage.getItem('api-token')
            },
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term,
                    sales: `{{ Auth::user()->sales->id }}`
                };
            },
            delay: 250,
            processResults: function (data) {
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

    // Pre-select value
    var preselectedCustomerId = '{{ $task->customer_id }}';
    if (preselectedCustomerId) {
        $.ajax({
            type: 'GET',
            url: '/api/customer/' + preselectedCustomerId, // assuming you have an endpoint that returns a single customer
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api-token')
            },
            dataType: 'json'
        }).then(function (data) {
            // Create the pre-selected option and update the Select2 control
            var option = new Option(data.nama, data.id, true, true);
            $('#customerId').append(option).trigger('change');
        });
    }

    //fungsi submit form
    $('#editAktivitas').submit(function(e) {
        e.preventDefault();

        let taskId = $('#taskId').val();

        var formData = new FormData(this);

        //ajax untuk mengirim data
        $.ajax({
            url: `/task/${taskId}`,
            type: 'POST',
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
});
</script>
@endsection