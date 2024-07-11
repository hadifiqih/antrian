@extends('layouts.app')

@section('title', 'Kelola Akun | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Kelola Akun')

@section('breadcrumb', 'Social Media')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Daftar Akun Social Media</h3>
                </div>
                <div class="card-tools">
                    <a href="{{ route('social-media.create') }}" class="btn btn-primary btn-sm">Tambah Akun</a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Platform</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection