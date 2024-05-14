@extends('layouts.app')

@section('title', 'Detail Penjualan | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Detail Penjualan')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <a href="javascript:void()" onclick="goBack()"><i class="fas fa-arrow-left"></i></a> <span class=" ml-3 h5 font-weight-bold"> Detail Transaksi</span>
            <a href="{{ route('pos.faktur', $penjualan->id) }}" class="btn btn-danger btn-sm float-right"><i class="fas fa-print"></i> Faktur</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                    <table id="tableDetail" class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Barang</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->produk->nama_produk }}</td>
                                <td>Rp. {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td class="text-right">Rp. {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-borderless">
                        <tbody class="font-weight-bold">
                            <tr>
                                <td class="text-danger h5 py-0"><strong>Total Harga</strong></td>
                                <td class="text-right text-danger h5 py-0"><strong>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</strong></td>
                            </tr>
                            @if($penjualan->ppn > 0)
                            <tr>
                                <td class="py-0"><strong>PPN</strong></td>
                                <td class="text-right py-0"><strong>Rp {{ number_format($penjualan->ppn, 0, ',', '.') }}</strong></td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-success py-0"><strong>Grand Total</strong></td>
                                <td class="text-right text-success py-0"><strong>Rp {{ number_format($penjualan->total + $penjualan->ppn, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="py-0">Bayar</td>
                                <td class="text-right py-0"">Rp {{ number_format($penjualan->dibayarkan, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-0">Kembalian</td>
                                <td class="text-right py-0"">Rp {{ number_format($penjualan->dibayarkan - $penjualan->total < 0 ? '0' : $penjualan->dibayarkan - $penjualan->total , 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <h6 class="font-weight-bold">Metode Pembayaran</h6>
                    <p>{{ ucwords($penjualan->metode_pembayaran) }} - {{ $penjualan->rekening == 'tunai' ? strtoupper($penjualan->rekening) : 'Transfer ke Bank ' . strtoupper($penjualan->rekening) }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="font-weight-bold">Tanggal Transaksi</h6>
                    <p>{{ $penjualan->created_at->format('d F Y H:i:s') }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="font-weight-bold">Keterangan</h6>
                    <h6>{{ $penjualan->keterangan == 'datang' ? 'Datang ke Toko' : 'Dikirim' }}</h6>
                    @if($penjualan->keterangan == 'dikirim')
                    <h6>{{ $penjualan->customer->nama }} - {{ $penjualan->telepon }}</h6>
                    <h6>{{ $penjualan->alamat }}</h6>
                    @endif
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