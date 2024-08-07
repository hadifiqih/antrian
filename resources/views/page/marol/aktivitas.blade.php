@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('breadcrumb', 'Pelanggan')

@section('page', 'Data Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end">
            <a href="#" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>
                Tambah Aktivitas
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aktivitas Harian</h3>
                </div>
                <div class="card-body">
                    <table id="tableAktivitas" class="table table-borderless">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Sales</th>
                                <th>Platform</th>
                                <th>Jenis Konten</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
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
        $('#tableAktivitas').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('daily-activity.indexJson') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'sales', name: 'sales' },
                { data: 'platform', name: 'platform' },
                { data: 'jenis_konten', name: 'jenis_konten' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'action', name: 'action' }
            ]
        });
    });
</script>
@endsection