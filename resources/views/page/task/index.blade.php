@extends('layouts.app')

@section('title', 'Daftar Aktivitas | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Aktivitas')

@section('breadcrumb', 'Daftar Aktivitas')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Daftar Aktivitas<a href="{{ route('task.create') }}" class="btn btn-primary btn-sm float-right">Tambah Aktivitas</a></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tableTask" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Aktivitas</th>
                                    <th>Pelanggan</th>
                                    <th>Status</th>
                                    <th>Deadline</th>
                                    <th>Update</th>
                                    <th>Sales</th>
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
    function deleteTask(id) {
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
                    url: `/task/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        Swal.fire(
                            'Terhapus!',
                            'Data berhasil dihapus.',
                            'success',
                        ).then(function() {
                            window.location.reload();
                        });
                    },
                    error: function(data) {
                        Swal.fire(
                            'Gagal!',
                            'Data gagal dihapus.',
                            'error'
                        );
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        $('#tableTask').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('task.indexJson') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'nama_task', name: 'nama_task' },
                { data: 'customer', name: 'customer' },
                { data: 'status', name: 'status' },
                { data: 'deadline', name: 'deadline' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'sales', name: 'sales' },
                { data: 'action', name: 'action' }
            ]
        });
    });
</script>
@endsection