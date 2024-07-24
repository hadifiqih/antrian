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
            <div class="row mb-3">
                <div class="col-md-4">
                    <h6 class="font-weight-bold">Metode Pembayaran</h6>
                    <h6 class="text-secondary">{{ ucwords($penjualan->metode_pembayaran) }} - {{ $penjualan->rekening == 'tunai' ? strtoupper($penjualan->rekening) : 'Transfer ke Bank ' . strtoupper($penjualan->rekening) }}</h6>
                </div>
                <div class="col-md-4">
                    <h6 class="font-weight-bold">Tanggal Transaksi</h6>
                    <h6 class="text-secondary">{{ $penjualan->created_at->format('d F Y H:i:s') }}</h6>
                </div>
                <div class="col-md-4">
                    <h6 class="font-weight-bold">Keterangan</h6>
                    <h6 class="text-secondary">{{ $penjualan->keterangan == 'datang' ? 'Datang ke Toko' : 'Dikirim' }}</h6>
                </div>
                <div class="col-md-4">
                    <h6 class="font-weight-bold">Dikirim Dari</h6>
                    <h6 class="text-secondary">{{ $penjualan->cabang->nama_cabang }}</h6>
                </div>
                <div class="col-md-4">
                    <h6 class="font-weight-bold">Nama Pelanggan</h6>
                    <h6 class="text-secondary">{{ $penjualan->pelanggan == null ? 'Umum' : $penjualan->pelanggan->nama_pelanggan }}</h6>
                </div>
                <div class="col-md-4">
                    <h6 class="font-weight-bold">No. Telepon</h6>
                    <h6 class="text-secondary">{{ $penjualan->pelanggan == null ? '-' : $penjualan->pelanggan->no_telp }}</h6>
                </div>
            </div>
            <hr>
            <div class="row mt-3">
                @foreach ($items as $item)
                <div class="col-lg-4 col-sm-12">
                    <div class="card mb-3">
                        <div class="row g-0">
                            <div class="col-md-4 d-flex align-items-center justify-content-center p-3">
                                <img src="{{ $item->produk->gambar_produk == null ? asset('adminlte/dist/img/placeholder.svg') : asset('storage/gambar-produk/'. $item->produk->gambar_produk) }}" class="img-fluid rounded-start lazyload" alt="Logitech M170 Mouse">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h6 class="card-title font-weight-bold">{{ $item->produk->nama_produk }}</h6>
                                    <p class="card-text"><small class="text-muted">{{ $item->jumlah ." x Rp ". number_format($item->harga, 0, ',', '.') }}</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-borderless">
                        <tbody class="font-weight-bold">
                            <tr>
                                <td class="text-danger h6 py-0"><strong>Total Harga</strong></td>
                                <td class="text-right text-danger h6 py-0"><strong>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</strong></td>
                            </tr>
                            @if($penjualan->ppn > 0)
                            <tr>
                                <td class="py-0"><strong>PPN</strong></td>
                                <td class="text-right py-0"><strong>Rp {{ number_format($penjualan->ppn, 0, ',', '.') }}</strong></td>
                            </tr>
                            @endif
                            <tr>
                                <td class="py-0">Diskon</td>
                                <td class="text-right py-0">Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-success h6 py-0"><strong>Grand Total</strong></td>
                                <td class="text-right h6 text-success py-0"><strong>Rp {{ number_format($penjualan->total + $penjualan->ppn, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="py-0">Bayar</td>
                                <td class="text-right py-0"">Rp {{ number_format($penjualan->diterima, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-0">Kembalian</td>
                                <td class="text-right py-0"">Rp {{ number_format($penjualan->diterima - $penjualan->total < 0 ? '0' : $penjualan->diterima - $penjualan->total , 0, ',', '.') }}</td>
                            </tr>
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
    function goBack() {
        window.history.back();
    }
</script>
@endsection
