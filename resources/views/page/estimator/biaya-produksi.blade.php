@extends('layouts.app')

@section('title', 'Antrian | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Laporan')

@section('breadcrumb', 'Laporan Penugasan')

@section('content')

<div class="container">
    {{-- Tabel Bahan --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Biaya Produksi</h3>
                    <button class="btn btn-sm btn-primary float-right" onclick="modalTambahBahan()">Tambah Bahan Produksi</button>
                </div>
                <div class="card-body">
                    <a href="{{ route('estimator.unduhBPExcel', $barang->id) }}" class="btn btn-sm btn-success mb-3"><i class="fas fa-print"></i> Unduh Excel</a>
                    <div class="table-responsive">
                        <table id="tableNamaBahanProduksi" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Bahan</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bahan as $key => $b)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $b->nama_bahan }}</td>
                                    <td>Rp{{ number_format($b->harga,0,',','.') }}</td>
                                    <td>{{ $b->qty }}</td>
                                    <td>Rp{{ number_format($b->qty * $b->harga,0,',','.') }}</td>
                                    <td>{{ $b->note }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Total</th>
                                    @php
                                    $totalProduksi = 0;
                                    foreach($bahan as $b){
                                        $totalProduksi += $b->qty * $b->harga;
                                    }
                                    @endphp
                                    <th class="font-weight-bold" colspan="2">Rp{{ number_format($totalProduksi,0,',','.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Biaya Tambahan Lainnya</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tableBiayaLainnya" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Biaya</th>
                                    <th>Persentase</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($biayaLainnya as $key => $b)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $b->nama_biaya }}</td>
                                    <td>{{ $b->persentase }}%</td>
                                    <td>Rp{{ number_format(($b->persentase / 100) * $barang->price,0,',','.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    @php
                                    $totalBiayaLainnya = 0;
                                    foreach($biayaLainnya as $b){
                                        $totalBiayaLainnya += ($b->persentase / 100) * $barang->price;
                                    }
                                    @endphp
                                    <th class="font-weight-bold">Rp{{ number_format($totalBiayaLainnya,0,',','.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Total Biaya Produksi</h3>
                    <h3 class="card-title font-weight-bold text-danger float-right">Rp {{ number_format($totalProduksi + $totalBiayaLainnya, 0, ',','.') }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@include('page.estimator.modal.tambah-bahan-produksi')
@endsection

@section('script')
<script>
    function modalTambahBahan(){
        $('#modalTambahBahan').modal('show');
    }

    $(document).ready(function(){
        //maskmoney
        $('#modalTambahBahan #harga').maskMoney({thousands:'.', decimal:',', precision:0});

        $('#formTambahBahan').submit(function(e){
            e.preventDefault();
            $.ajax({
                url: "{{ route('tambahBahanProduksi') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(data){
                    $('#tableNamaBahanProduksi').DataTable().ajax.reload();
                    $('#modalTambahBahan').modal('hide');
                }
            });
        });
    });
</script>
@endsection