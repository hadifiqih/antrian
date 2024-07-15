@extends('layouts.app')

@section('title', 'Tambah Aktivitas | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Aktivitas')

@section('breadcrumb', 'Tambah Aktivitas')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Tambah Aktivitas</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('task.store') }}" method="POST" enctype="multipart/form-data">
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
                                    <input type="file" class="custom-file-input" id="lampiran">
                                    <label class="custom-file-label" for="lampiran">Pilih file</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <h5>Informasi Aktivitas</h5>
                        <hr>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="Belum Selesai">Belum Selesai</option>
                                <option value="Proses">Proses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="batasWaktu">Batas Waktu</label>
                            <input type="datetime-local" name="batasWaktu" id="batasWaktu" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="akhirBatas">Akhir Batas Waktu</label>
                            <input type="datetime-local" name="akhirBatas" id="akhirBatas" class="form-control" required>
                        </div>

                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('task.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        let latitude = position.coords.latitude;
        let longitude = position.coords.longitude;
        console.log(latitude, longitude);
    });
    } else {
        console.log("Geolocation is not supported by this browser.");
    }

    $(document).ready(function() {
        
    });
</script>
@endsection