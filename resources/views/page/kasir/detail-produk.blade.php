@extends('layouts.app')

@section('title', 'Detail Produk | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Detail Produk')

@section('content')
 <!-- Main content -->
 <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">
          <!-- Gambar Produk -->
          <img src="dist/img/gagang-stempel-flash-1340.jpg" alt="Gagang Stempel Flash 1340" class="product-image">
        </div>
        <div class="col-md-6">
          <!-- Detail Produk -->
          <div class="product-detail">
            <h2>Gagang Stempel Flash 1340</h2>
            <p><strong>Deskripsi:</strong> Ukuran 13x40cm</p>
            <p><strong>Harga:</strong> Rp 6.100</p>
            <p><strong>Kategori:</strong> Stempel</p>
            <p><strong>Stok:</strong> 200</p>
            <button class="btn btn-edit"><i class="fas fa-edit"></i> Edit Produk</button>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection