@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('breadcrumb', 'Pelanggan')

@section('page', 'Data Pelanggan')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('customer.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Pelanggan
                </a>
                <a href="{{ route('customer.export') }}" class="btn btn-success btn-sm ml-2">
                    <i class="fas fa-file-excel mr-1"></i>
                    Export Excel
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            @includeIf('page.customer.partials.table')
                        </div>
                    </div>
                    @includeIf('page.customer.partials.modal-edit')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    let table;

    var toastr = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    function editForm(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Edit Data Pelanggan');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('put');
            $('#modal-form [name=namaPelanggan]').focus();
            
            $.get(url)
                .done((response) => {
                    $('#modal-form [name=sales]').val(response.sales_id);
                    $('#modal-form [name=namaPelanggan]').val(response.nama);
                    $('#modal-form [name=telepon]').val(response.telepon);
                    $('#modal-form [name=alamat]').val(response.alamat);
                    $('#modal-form [name=infoPelanggan]').val(response.infoPelanggan);
                    $('#modal-form [name=instansi]').val(response.instansi);
                    $('#modal-form [name=wilayah]').val(response.wilayah);
                })
                .fail((errors) => {
                    alert('Tidak dapat menampilkan data');
                    return;
                });
        }

    function deleteForm(url){
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        // Memberi notifikasi sukses dengan toastr
                        toastr.fire({
                            icon: 'success',
                            title: 'Berhasil dihapus !'
                        })

                        // Menghapus data pada datatable
                        table.ajax.reload();
                    },
                    error: function (xhr) {
                        // Memberi notifikasi error dengan toastr
                        toastr.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan !'
                        })

                        // Menghapus data pada datatable
                        table.ajax.reload();
                    }
                });
            }
        })
    }

    $(function () {

        table = $('#customer-table').DataTable({
            responsive: true,
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('customer.indexJson') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false},
                {data: 'nama', name: 'Nama'},
                {data: 'telepon', name: 'Telepon'},
                {data: 'infoPelanggan', name: 'Info Pelanggan'},
                {data: 'sales', name: 'Sales'},
                {data: 'status', name: 'Status'},
                {data: 'action', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').on('submit', function (e) {
            // Menghilangkan fungsi default form submit
            e.preventDefault();
            
            // Mengambil url action pada form
            let url = $('#modal-form form').attr('action');

            // Mengambil data pada form
            let data = $('#modal-form form').serialize();

            // Mengirim data ke url action dengan method PUT
            $.ajax({
                url: url,
                method: 'PUT',
                data: data,
                success: function (response) {
                    // Menutup modal
                    $('#modal-form').modal('hide');

                    // Memberi notifikasi sukses dengan toastr
                    toastr.fire({
                        icon: 'success',
                        title: 'Berhasil diubah !'
                    })

                    // Menghapus data pada datatable
                    table.ajax.reload();
                },
                error: function (xhr) {
                    // Menutup modal
                    $('#modal-form').modal('hide');

                    // Memberi notifikasi error dengan toastr
                    toastr.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan !'
                    })

                    // Menghapus data pada datatable
                    table.ajax.reload();
                }
            });
        });
    });
</script>
@endsection
