@extends('layouts.app')

@section('title', 'Kelola Akun | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Kelola Akun')

@section('breadcrumb', 'Edit Social Media')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Edit Social Media</h3>
                </div>
                <div class="card-body">
                    <form id="form-sosmed" action="{{ route('social.update', $sosmed->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        {{-- Sales --}}
                        <div class="form-group">
                            <label for="sales">Sales <span class="text-danger">*</span></label>
                            <select name="sales" id="sales" class="form-control">
                                @foreach ($sales as $sale)
                                <option value="{{ $sale->id }}" {{ $sosmed->sales_id == $sale->id ? 'selected' : ''}}>{{ $sale->sales_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Platform --}}
                        <div class="form-group">
                            <label for="platform">Platform <span class="text-danger">*</span></label>
                            <select name="platform" id="platform" class="form-control">
                                <option value="Facebook" {{ $sosmed->platform == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                <option value="Instagram" {{ $sosmed->platform == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="YouTube" {{ $sosmed->platform == 'Youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="TikTok" {{ $sosmed->platform == 'Tiktok' ? 'selected' : '' }}>TikTok</option>
                                <option value="Shopee" {{ $sosmed->platform == 'Shopee' ? 'selected' : '' }}>Shopee</option>
                                <option value="Tokopedia" {{ $sosmed->platform == 'Tokopedia' ? 'selected' : '' }}>Tokopedia</option>
                            </select>
                        </div>
                        {{-- Username --}}
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="{{ $sosmed->username }}" autocomplete="off" required >
                        </div>
                        {{-- Email --}}
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ $sosmed->email }}" required>
                        </div>
                        {{-- Telepon --}}
                        <div class="form-group">
                            <label for="phone">Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ $sosmed->phone }}" required>
                        </div>
                        {{-- Password --}}
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control">
                            <p class="text-sm text-danger">* Kosongkan jika <strong>tidak ingin mengganti</strong> password</p>
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
            let data = form.serialize();

            $.ajax({
                url: url,
                method: 'PUT',
                data: data,
                success: function(response) {
                    if (response.success == true) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = "{{ route('social.index') }}";
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>
@endsection