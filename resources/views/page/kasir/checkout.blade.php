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
            <h3 class="card-title">Review your order</h3>
          </div>
          <div class="card-body">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Digital Product</td>
                  <td>$10.00</td>
                </tr>
              </tbody>
            </table>
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
              <button type="submit" class="btn btn-primary float-right">Confirm Order</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')

@endsection