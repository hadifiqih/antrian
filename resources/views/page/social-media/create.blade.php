@extends('layouts.app')

@section('title', 'Kelola Akun | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Kelola Akun')

@section('breadcrumb', 'Tambah Akun Social Media')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Tambah Akun Social Media</h3>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection