@extends('layouts.app')

@section('title', 'Invoice Penjualan Jasa| CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Invoice Penjualan Jasa')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <a onclick="goBack()" class="btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
            <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-print"></i> Cetak PDF</a>
        </div>
    </div>
    <div class="card p-3">
        <div class="card-body">
            <div class="row mb-5">
                <h4 class="col-md-6 font-weight-bold">Faktur Penjualan</h4>
                <div class="col-md-6 text-right">
                    <p class="font-weight-bold">No Invoice : {{ $antrian->ticket_order }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <address>
                        <strong>DITERBITKAN ATAS NAMA</strong><br>
                        Penjual :
                        <strong>{{ $sales->sales_name }}</strong><br>
                        {{ $sales->address }}<br>
                        Telp: {{ $sales->sales_phone }}
                    </address>
                </div>
                <div class="col-md-6">
                    <address>
                        <strong>DITERBITKAN KEPADA</strong><br>
                        Pembeli :
                        <strong>{{ $antrian->customer->nama }}</strong><br>
                        Tanggal Pembelian : 
                        <strong>{{ date_format($antrian->created_at, 'd F Y') }}</strong><br>
                        Alamat : {{ $antrian->customer->alamat ?? '-' }}<br>
                        Telp: {{ $antrian->customer->telepon ?? '-'}}
                    </address>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->job->job_name }}</td>
                                <td class="text-center">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-center font-weight-bold">Rp {{ number_format($item->qty * $item->price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right font-weight-bold">Total</td>
                                <td class="text-center font-weight-bold">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6">

                    @if($antrian->cekApakahAdaPengiriman() == true)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right">Biaya Ongkir : </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Rp {{ number_format($antrian->pengiriman->ongkir, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                    @endif

                    @if($antrian->cekApakahAdaBiayaPasang() == true)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right">Biaya Pasang : </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Rp {{ number_format($antrian->pembayaran->biaya_pasang, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                    @endif
                    
                    @if($antrian->cekApakahAdaBiayaPacking() === true)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right">Biaya Packing : </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Rp {{ number_format($antrian->pembayaran->biaya_packing, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                    @endif

                    @if($antrian->ppn > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right">Pajak PPN(11%) : </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Rp {{ number_format($antrian->ppn, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                    @endif

                    @if($antrian->pph > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right">Pajak PPh(2,5%) : </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Rp {{ number_format($antrian->pph, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right text-danger">Diskon : </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center text-danger">Rp {{ number_format($diskon, 0, ',', '.') }}</h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right">Grand Total : </h6>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-center font-weight-bold text-success">Rp {{ number_format($grandTotal, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6>Keterangan : </h6>
                    @if($antrian->pembayaran->status_pembayaran == 1)
                    <h6 class="font-weight-bold">Down Payment (DP) : Rp {{ number_format($antrian->pembayaran->dibayarkan,0,',','.') }}</h6>
                    @endif
                    <h6 class="font-weight-bold">{{ $antrian->is_priority == 1 ? '⭐ PRIORITAS' : 'Tidak Prioritas' }}</h6>
                </div>
                <div class="col-md-6">
                    <h6>Metode Pembayaran : </h6>
                    <h6 class="font-weight-bold">{{ ucwords($antrian->pembayaran->metode_pembayaran) }} - {{ $antrian->pembayaran->status_pembayaran == 2 ? 'Lunas' : 'Belum Lunas' }}</h6>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    function goBack() {
        window.history.back();
    }
</script>
@endsection