@extends('layouts.app')

@section('title', 'Kelola Akun | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Kelola Akun')

@section('breadcrumb', 'Social Media')

@section('content')
<style>
    .password-container {
        position: relative;
        display: inline-block;
    }
    .password-field {
        padding-right: 30px;
    }
    .toggle-password {
        position: absolute;
        top: 50%;
        right: 5px;
        transform: translateY(-50%);
        cursor: pointer;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Daftar Akun Social Media</h3>
                    <a href="{{ route('social.create') }}" class="btn btn-primary btn-sm float-right">Tambah Akun</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-social" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Platform</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Password</th>
                                    <th>Aksi</th>
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
</div>
@endsection

@section('script')
<script>
    // Datatables
    $(document).ready(function() {
        $('#table-social').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('social.indexJson') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'platform', name: 'platform' },
                { data: 'username', name: 'username' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'password', name: 'password' },
                { data: 'action', name: 'action'}
            ]
        });

        // Toggle Password
        $(document).on('click', '.toggle-password' ,function() {
            var passwordField = $(this).siblings('.password-field');
            var fieldType = passwordField.attr('type');

            if (fieldType === 'password') {
                passwordField.attr('type', 'text');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Delete
        $(document).on('click', '.delete', function() {
            var id = $(this).data('id');
            var url = "/social-account/" + id;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        data: {
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success == true) {
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    $('#table-social').DataTable().ajax.reload();
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
                }
            });
        });
    });
</script>
@endsection