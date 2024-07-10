@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('breadcrumb', 'Pelanggan')

@section('page', 'Data Pelanggan')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="mr-3">
                                <div class="bg-secondary rounded-circle" style="width: 48px; height: 48px;">
                                    <!-- Placeholder untuk foto profil -->
                                </div>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $customer->nama }}</h5>
                                <p class="card-text text-muted text-sm">{{ $customer->instansi }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-user"></i> <strong>Informasi Pelanggan</strong></h5>
                            <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-edit"></i> Edit</a>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" id="nama" class="form-control" value="{{ $customer->nama }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="nama">Telepon</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="{{ $customer->telepon }}" readonly>
                                    <div class="input-group-append">
                                        <a class="input-group-text bg-success" href="https://wa.me/{{ $telp }}"><i class="fab fa-whatsapp"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="instansi">Instansi</label>
                                <input type="text" id="instansi" class="form-control" value="{{ $customer->instansi }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea id="alamat" class="form-control" rows="3" readonly>{{ $customer->alamat }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="kota">Kota</label>
                                <input type="text" id="kota" class="form-control" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="provinsi">Provinsi</label>
                                <input type="text" id="provinsi" class="form-control" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="infoPelanggan">Info Pelanggan</label>
                                <input type="text" id="infoPelanggan" class="form-control" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" class="form-control" value="{{ $customer->email ?? '-' }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <input type="text" id="status" class="form-control" value="{{ $status }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="created_at">Dibuat pada</label>
                                <input type="text" id="created_at" class="form-control" value="{{ $customer->created_at }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="updated_at">Diperbarui pada</label>
                                <input type="text" id="updated_at" class="form-control" value="{{ $customer->updated_at }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-history"></i> <strong>Riwayat Pelanggan</strong></h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="timeline">
                                        @foreach($orders as $order)
                                            <!-- Timeline item -->
                                            <div>
                                                <i class="fas fa-shopping-cart bg-blue"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fas fa-clock"></i> {{ $order->created_at->diffForHumans() }}</span>
                                                    <h3 class="timeline-header"><strong class="text-primary">{{ $order->customer->nama }}</strong> melakukan order #{{ $order->ticket_order }}</h3>
                                                    <div class="timeline-body">
                                                        Melakukan order jasa dengan total harga <strong>Rp {{ number_format($order->pembayaran->total_harga, 0, ',', '.') }}</strong>
                                                    </div>
                                                    <div class="timeline-footer">
                                                        <a href="/antrian/show/{{ $order->ticket_order }}" class="btn btn-primary btn-sm">Lihat Order</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div>
                                            <i class="fas fa-clock bg-gray"></i>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function editPelanggan(id) {
        $.ajax({
            url: '/pelanggan/' + id,
            type: 'GET',
            success: function(response) {
                $('#modalEditPelanggan #modalNama').val(response.nama);
                $('#modalEditPelanggan #modalTelepon').val(response.telepon);
                $('#modalEditPelanggan #modalAlamat').text(response.alamat);
                $('#modalEditPelanggan #modalInstansi').val(response.instansi);

                // Memilih option yang sesuai untuk infoPelanggan berdasarkan nama_sumber
                var namaSumber = response.infoPelanggan;
                $('#modalEditPelanggan #infoPelanggan option').each(function() {
                    if ($(this).text() === namaSumber) {
                        $(this).prop('selected', true);
                        return false; // Keluar dari loop setelah menemukan yang sesuai
                    }
                });
                $('#modalEditPelanggan #provinsi').val(response.provinsi);
                $('#modalEditPelanggan #kota').val(response.kota);
                $('#modalEditPelanggan').modal('show');
            }
        });
    }
    $(document).ready(function() {
        //tampilkan nama provinsi dan kota berdasarkan id
        var provinsi = "{{ $provdankota['provinsi'] }}";
        var kota = "{{ $provdankota['kota'] }}";
        var infoPelanggan = "{{ $sumberPelanggan->nama_sumber }}";
        var selectedProvinsi = "";
        $('#provinsi').val(provinsi);
        $('#kota').val(kota);
        $('#infoPelanggan').val(infoPelanggan);
    });
</script>

@endsection