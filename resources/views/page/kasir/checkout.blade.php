@extends('layouts.app')

@section('title', 'Checkout Keranjang | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'POS')

@section('breadcrumb', 'Checkout')

@section('content')

<div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Konfirmasi Order</h3>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tableItems" class="table table-striped mt-3">
                  <thead>
                      <tr>
                          <th scope="col">{{ __('Nama Produk') }}</th>
                          <th scope="col">{{ __('Harga') }}</th>
                          <th scope="col">{{ __('Qty') }}</th>
                          <th scope="col">{{ __('Diskon') }}</th>
                          <th scope="col">{{ __('Total') }}</th>
                          <th scope="col">{{ __('Aksi') }}</th>
                      </tr>
                  </thead>
                  <tbody>

                  </tbody>
              </table>
          </div>
          </div>
          <div class="card-body">
            <form>
              <div class="form-group">
                <label for="billingName">Billing Name</label>
                <input type="text" class="form-control" id="billingName" placeholder="Enter your name">
              </div>
              <div class="form-group">
                <label for="billingEmail">Email</label>
                <input type="email" class="form-control" id="billingEmail" placeholder="Enter your email">
              </div>
              <div class="form-group">
                <label for="billingAddress">Billing Address</label>
                <textarea class="form-control" id="billingAddress" rows="3" placeholder="Enter your address"></textarea>
              </div>
              <button type="submit" class="btn btn-primary float-right">Buat Pesanan</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
      $('#tableItems').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: false,
        paging: false,
        info: false,
        ajax: "/pos/checkout-json/{{ $cart_id }}",
        columns: [
            { data: 'nama_produk', name: 'nama_produk' },
            { data: 'harga', name: 'harga' },
            { data: 'qty', name: 'qty' },
            { data: 'diskon', name: 'diskon' },
            { data: 'total', name: 'total' },
            { data: 'action', name: 'action' }
        ]
      });
    });
</script>
@endsection