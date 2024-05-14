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
          <div class="card-body pb-0">
            <div>
              <table id="tableItems" class="table table-bordered display nowarp" style="width:100%">
                  <thead class="thead-dark">
                      <tr>
                          <th scope="col">{{ __('Nama Produk') }}</th>
                          <th scope="col">{{ __('Harga') }}</th>
                          <th scope="col">{{ __('Qty') }}</th>
                          <th scope="col">{{ __('Diskon') }}</th>
                          <th scope="col">{{ __('Total') }}</th>
                      </tr>
                  </thead>
                  <tbody>

                  </tbody>
              </table>
            </div>
              <h6 class="font-weight-bold mx-3 mt-2">Total<span id="totalItems" class="float-right">{{ $total }}</span></h6>
          </div>
          {{-- Checkbox untuk penggunaan pajak --}}
            
          <hr>
          <div class="card-body pt-0">
            <div class="form-group">
              <label for="customer">Pelanggan</label>
              <input type="hidden" id="customer_id" name="customer_id" value="{{ $customer_id }}">
              <input type="text" class="form-control" id="customer" name="customer" placeholder="Nama Pelanggan" value="{{ $nama_customer }}" disabled>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="keterangan">Keterangan</label>
                  <select class="form-control" id="keterangan" name="keterangan" required>
                    <option value="datang">Datang Ke Toko</option>
                    <option value="kirim">Dikirim</option>
                  </select>
                </div>
                <div class="form-check usePelanggan mb-2" style="display: none">
                  <input class="form-check-input" type="checkbox" value="" id="useCustomerData">
                  <label class="form-check-label" for="useCustomerData">
                    Gunakan data pelanggan
                  </label>
                </div>
              </div>
            </div>
            <div class="row telepon" style="display: none">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="telepon">Telepon</label>
                  <input type="text" class="form-control" id="telepon" name="telepon" placeholder="Telepon Pelanggan" required>
                </div>
              </div>
            </div>
            <div class="row alamat" style="display: none">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="alamat">Alamat</label>
                  <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Alamat Pelanggan"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="metode">Metode Pembayaran</label>
                  <select class="form-control" id="metode" name="metode" required>
                    <option value="ditempat">Bayar Ditempat</option>
                    <option value="transfer">Transfer</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="rekening">Rekening Tujuan</label>
                  <select class="form-control" id="rekening" name="rekening" required>
                    <option id="tunai" value="tunai">Tunai</option>
                    <option value="bca">Bank Central Asia (BCA)</option>
                    <option value="bri">Bank Rakyat Indonesia (BRI)</option>
                    <option value="mandiri">Bank Mandiri</option>
                    <option value="bni">Bank Negara Indonesia (BNI)</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="payment">Pembayaran</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">Rp</span>
                    </div>
                    <input type="text" value="0" class="form-control form-lg" name="total_bayar" id="totalBayar">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <h5 class="font-weight-bold mx-3">Kembalian : <span id="kembalian" class="float-right text-danger">Rp 0</span></h5>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button id="btnBuatPesanan" type="submit" class="btn btn-primary btn-sm float-right">Buat Pesanan</button>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')
<script src="{{ asset('adminlte/dist/js/maskMoney.min.js') }}"></script>
<script>

  function formatRupiah(angka) {
    var reverse = angka.toString().split('').reverse().join(''),
    ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return 'Rp ' + ribuan;
  }

    $(document).ready(function() {
      $('#totalBayar').maskMoney({thousands:'.', decimal:',', precision:0});

      //Datatable Items
      $('#tableItems').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: false,
        paging: false,
        info: false,
        scrollX: true,
        ajax: "/pos/checkout-json/{{ $cart_id }}",
        columns: [
            { data: 'nama_produk', name: 'nama_produk' },
            { data: 'harga', name: 'harga' },
            { data: 'qty', name: 'qty' },
            { data: 'diskon', name: 'diskon' },
            { data: 'total', name: 'total' }
        ]
      });

      //jika metode pembayaran transfer maka tampilkan rekening
      $('#metode').on('change', function() {
        if($(this).val() == 'ditempat') {
          //set rekening tunai
          $('#rekening').val('tunai');
          $('#rekening').prop('disabled', true);
        }else{
          $('#rekening').prop('disabled', false);
          $('#rekening').val('bca');
          $('#tunai').prop('disabled', true);
        }
      });

      //Jika dikirim maka tampikan alamat dan wajib diisi
      $('#keterangan').change(function() {
        if($(this).val() == 'kirim') {
          $('.alamat').show();
          $('.usePelanggan').show();
          $('.telepon').show();
          $('#alamat').prop('required', true);
          $('#alamat').prop('disabled', false);
        } else {
          $('.alamat').hide();
          $('.usePelanggan').hide();
          $('.telepon').hide();
          $('#alamat').prop('required', false);
          $('#alamat').prop('disabled', true);
        }
      });

      //Jika gunakan data pelanggan
      $('#useCustomerData').change(function() {
        if($(this).is(':checked')) {
          $.ajax({
            url: '/pelanggan/' + $('input[name="customer_id"]').val(),
            type: 'GET',
            success: function(data) {
              $('#telepon').val(data.telepon);
              $('#alamat').val(data.alamat);
            }
          });
        } else {
          $('#telepon').val('');
          $('#alamat').val('');
        }
      });

      $('#totalBayar').on('keyup', function() {
          var totalText = $('#totalItems').text().replace(/\./g, '').replace('Rp ', '');
          var bayarText = $(this).val().replace(/\./g, '');
          
          // Mengonversi teks menjadi angka, jika tidak valid akan menghasilkan NaN
          var total = parseInt(totalText);
          var bayar = parseInt(bayarText);
          
          // Memeriksa apakah kedua nilai adalah angka yang valid
          if (!isNaN(total) && !isNaN(bayar)) {
              var kembalian = bayar - total;
              if(kembalian < 0) {
                kembalian = 0;
              }else{
                kembalian = kembalian;
              }
              $('#kembalian').text(formatRupiah(kembalian));
          } else {
              // Jika salah satu atau kedua nilai bukan angka, atur kembalian menjadi kosong
              $('#kembalian').text('');
          }
      });

      $('#btnBuatPesanan').on('click', function(){
        var totalText = $('#totalItems').text().replace(/\./g, '').replace('Rp ', '');
        var bayarText = $('#totalBayar').val().replace(/\./g, '');
        var total = parseInt(totalText);
        var bayar = parseInt(bayarText);

        if(bayar < total) {
          Swal.fire({
            title: "Gagal!",
            text: "Pembayaran kurang dari total pesanan!",
            icon: "error"
          });
          return false;
        }

        $.ajax({
          url: '/pos/buat-pesanan',
          type: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            customer_id: $('#customer_id').val(),
            keterangan: $('#keterangan').val(),
            telepon: $('#telepon').val(),
            alamat: $('#alamat').val(),
            metode: $('#metode').val(),
            rekening: $('#rekening').val(),
            total_bayar: $('#totalBayar').val(),
            total: total
          },
          success: function(data) {
              Swal.fire({
              icon: "success",
              title: "Pesanan kamu berhasil dibuat !",
              showConfirmButton: false,
              timer: 1500
              });

              //redirect ke halaman add order setelah 1.5 detik
              setTimeout(function() {
                window.location.href = '/pos/add-order';
              }, 1500);
          }
        });
      });
      
    });
</script>
@endsection