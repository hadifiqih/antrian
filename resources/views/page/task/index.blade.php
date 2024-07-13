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
                    <h5>Daftar Aktivitas</h5>
                    <a href="{{ route('task.create') }}" class="btn btn-primary btn-sm float-right">Tambah Aktivitas</a>
                </div>
                <div class="card-body">
                    <table id="tableTask" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Aktivitas</th>
                                <th>Status</th>
                                <th>Keterangan</th>
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
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#tableTask').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('task.indexJson') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'nama_task', name: 'nama_task' },
                { data: 'status', name: 'status' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'deadline', name: 'deadline' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'sales', name: 'sales' },
                { data: 'action', name: 'action' }
            ]
        });
    });
</script>
@endsection