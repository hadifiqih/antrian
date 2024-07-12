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
                <div class="card-body">
                    <form id="form-sosmed" action="{{ route('social.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- Sales --}}
                        <div class="form-group">
                            <label for="sales">Sales <span class="text-danger">*</span></label>
                            <select name="sales" id="sales" class="form-control">
                                @foreach ($sales as $sale)
                                <option value="{{ $sale->id }}">{{ $sale->sales_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Platform --}}
                        <div class="form-group">
                            <label for="platform">Platform <span class="text-danger">*</span></label>
                            <select name="platform" id="platform" class="form-control">
                                <option value="Facebook">Facebook</option>
                                <option value="Instagram">Instagram</option>
                                <option value="YouTube">YouTube</option>
                                <option value="TikTok">TikTok</option>
                                <option value="Shopee">Shopee</option>
                                <option value="Tokopedia">Tokopedia</option>
                            </select>
                        </div>
                        {{-- Username --}}
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        {{-- Email --}}
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        {{-- Telepon --}}
                        <div class="form-group">
                            <label for="phone">Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control" required>
                        </div>
                        {{-- Password --}}
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#form-sosmed').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let url = form.attr('action');
            let method = form.attr('method');
            let data = form.serialize();

            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function(response) {
                    if (response.status == 'success') {
                        alert('Data berhasil disimpan');
                        window.location.href = "{{ route('social-media.index') }}";
                    } else {
                        alert('Data gagal disimpan');
                    }
                }
            });
        });
    });
</script>
@endsection