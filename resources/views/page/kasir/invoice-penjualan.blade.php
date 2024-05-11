@extends('layouts.app')

@section('title', 'Invoice Penjualan | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Invoice Penjualan')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <a onclick="goBack()" class="btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
            <a href="{{ route('pos.printFaktur', $penjualan->id) }}" class="btn btn-danger btn-sm"><i class="fas fa-print"></i> Cetak PDF</a>
        </div>
    </div>
    <div class="card p-3">
        <div class="card-body">
            <div class="row mb-5">
                <h4 class="col-md-6 font-weight-bold">Faktur Penjualan</h4>
                <div class="col-md-6 text-right">
                    <p class="font-weight-bold">No Invoice : {{ $penjualan->no_invoice }}</p>
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
                    <div class="row">
                        <div class="col-md-6">
                            <address>
                                <strong>Pembeli</strong><br>
                                <strong>{{ $penjualan->customer->nama }}</strong><br>
                                {{ ucwords($penjualan->customer->alamat) }}<br>
                                Telp: {{ $penjualan->customer->telepon }}
                            </address>
                        </div>
                        <div class="col-md-6">
                            <address>
                                <strong>Tanggal Penjualan</strong><br>
                                {{ date_format($penjualan->created_at, 'd F Y') }}<br>
                            </address>
                        </div>
                    </div>
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
                                <td>{{ $item->produk->nama_produk }}</td>
                                <td class="text-center">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->jumlah }}</td>
                                <td class="text-center font-weight-bold">Rp {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right font-weight-bold">Total</td>
                                <td class="text-center font-weight-bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right">Diskon : </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Rp {{ number_format($diskon, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                    @if($penjualan->ppn > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right">Pajak PPN(11%) : </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Rp {{ number_format($penjualan->ppn,0,',','.') }}</h6>
                        </div>
                    </div>
                    @endif

                    @if($penjualan->pph > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right">Pajak PPh(2,5%) : </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Rp {{ number_format($penjualan->pph,0,',','.') }}</h6>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-right">Grand Total : </h6>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-center font-weight-bold text-success">Rp {{ number_format($total, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6>Keterangan : </h6>
                    <h6 class="font-weight-bold">{{ $penjualan->keterangan == 'datang' ? 'Datang ke Toko' : 'Dikirim' }}</h6>
                    <h6>{{ $penjualan->customer->nama }} - {{ $penjualan->telepon }}</h6>
                    <h6>{{ $penjualan->alamat }}</h6>
                </div>
                <div class="col-md-6">
                    <h6>Metode Pembayaran : </h6>
                    <h6 class="font-weight-bold">{{ ucwords($penjualan->metode_pembayaran) }} - {{ $rekening }}</h6>
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