@extends('layouts.app')

@section('title', 'Dokumentasi Upload | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Dokumentasi')

@section('breadcrumb', 'Upload Dokumentasi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload Dokumentasi</h3>
                </div>
                <div class="card-body">
                        <!-- Formulir Dropzone untuk unggah file -->
                        <form action="{{ route('documentation.upload') }}" class="dropzone" id="myDropzoneDokumentasi">
                            @csrf
                            <input type="hidden" name="job_id" value="{{ $barang->job_id }}">
                            <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                        </form>
                </div>
                <div class="card-footer">
                    <button id="submitUploadDokumentasi" type="button" class="btn btn-primary">Unggah</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection