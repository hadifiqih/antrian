@extends('layouts.app')

@section('title', 'Antrian | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Tambah Antrian')

@section('breadcrumb', 'Tambah Antrian')

@section('content')
<div class="container-fluid">
    <form action="{{ route('antrian.simpanAntrian') }}" method="POST" enctype="multipart/form-data">
        @csrf
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Data Pelanggan</h2>
                    <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal" data-target="#modalTambahPelanggan">
                        <i class="fas fa-user"></i> Tambah Pelanggan
                    </button>
                </div>
                <div class="card-body">
                    {{-- Tambah Pelanggan Baru --}}
                    <div class="form-group">
                        <label for="customer_id">Nama Pelanggan <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="customer_id" name="customer_id" style="width: 100%">
                            <option value="" selected>Pilih Pelanggan</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Button Tambah Produk --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Informasi Order</h2>
                    <button type="button" class="btn btn-sm btn-primary float-right" onclick="tambahProduk()"><i class="fas fa-plus"></i> Tambah Produk</button>
                </div>
                <div class="card-body">
                        <table id="tableProduk" class="table table-responsive table-bordered" style="width: 100%">
                            <thead>
                                <th>#</th>
                                <th>Kategori Produk</th>
                                <th>Nama Produk</th>
                                <th>Qty</th>
                                <th>Harga (satuan)</th>
                                <th>Harga Total</th>
                                <th>Keterangan Spesifikasi</th>
                                <th>Acc Desain</th>
                                <th>Judul File Desain</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-center">Total</th>
                                    <th colspan="5" class="text-danger maskRupiah"><span id="subtotal"></span></th>
                                </tr>
                            </tfoot>
                        </table>

                            <div class="form-group">
                                <label for="packing">Biaya Packing</label>
                                <input type="text" class="form-control maskRupiah" id="packing" placeholder="Contoh : Rp 100.000" name="biayaPacking" value="{{ old('packing') }}">
                            </div>

                            <div class="form-group">
                                <label for="pasang">Biaya Pasang</label>
                                <input type="text" class="form-control maskRupiah" id="pasang" placeholder="Contoh : Rp 100.000" name="biayaPasang" value="{{ old('pasang') }}">
                            </div>

                            <div class="form-group">
                                <label for="diskon">Diskon / Potongan Harga</label>
                                <input type="text" class="form-control maskRupiah" id="diskon" placeholder="Contoh : Rp 100.000" name="diskon" value="{{ old('diskon') }}">
                            </div>

                            {{-- Menggunakan pajak ? --}}
                            <div class="row mt-3">
                                <div class="col-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch custom-switch-on-primary">
                                            <input type="checkbox" class="custom-control-input" id="usePajak" value="{{ old('usePajak') }}">
                                            <label class="custom-control-label" for="usePajak">Menggunakan Pajak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch custom-switch-on-primary">
                                            <input type="checkbox" class="custom-control-input" id="isOngkir" name="isOngkir" value="{{ old('isOngkir') }}">
                                            <label for="isOngkir" class="custom-control-label">Menggunakan Pengiriman</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPajak">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="ppn">Pajak PPN</label>
                                            {{-- input text for PPN --}}
                                            <input type="text" class="form-control maskRupiah" id="ppn" placeholder="Contoh : Rp 100.000" name="ppn" value="{{ old('ppn') }}" disabled>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch custom-switch-on-primary">
                                                <input type="checkbox" class="custom-control-input" id="usePPN">
                                                <label class="custom-control-label" for="usePPN">Gunakan PPN</label>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="ppn">Pajak PPh</label>
                                            {{-- input text for PPN --}}
                                            <input type="text" class="form-control maskRupiah" id="pph" placeholder="Contoh : Rp 100.000" name="pph" value="{{ old('pph') }}" disabled>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch custom-switch-on-primary">
                                                <input type="checkbox" class="custom-control-input" id="usePPh">
                                                <label class="custom-control-label" for="usePPh">Gunakan PPh</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="form-group pl-2">
                                        <h6 class="font-weight-bold">Total harga sudah termasuk pajak?</h6>
                                        <div class="form-check">
                                            <input class="form-check-input" id="radio1" value="1" type="radio" name="termasukPajak">
                                            <label class="form-check-label">Ya</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" id="radio2" value="0" type="radio" name="termasukPajak">
                                            <label class="form-check-label">Tidak</label>
                                        </div>
                                        <h6 class="mt-1 text-sm font-italic text-secondary">*Jika total harga belum termasuk pajak, maka total harga akan ditambakan pajak.</h6>
                                    </div>
                                </div>
                            </div>

                            <div class="pengiriman" style="display: none;">
                                <div class="form-group divOngkir">
                                    <label for="ongkir">Biaya Ongkir</label>
                                    <input type="text" class="form-control maskRupiah" id="ongkir" placeholder="Contoh : Rp 100.000" name="ongkir" value="{{ old('ongkir') }}">
                                </div>

                                <div class="form-group divAlamatKirim">
                                    <label for="ongkir">Alamat Pengiriman</label>
                                    <input type="text" class="form-control" id="alamatKirim" placeholder="Jl. Alamat Lengkap" name="alamatKirim" value="{{ old('alamatKirim') }}">
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input class="custom-control-input custom-control-input-danger" type="checkbox" id="alamatSama" name="alamatSama" value="{{ old('alamatSama') }}">
                                        <label for="alamatSama" class="custom-control-label">Alamat seperti pada Data Pelanggan</label>
                                    </div>
                                    <p class="text-muted font-italic text-sm mb-0 mt-1">*Harap isi dengan alamat lengkap, agar tidak terjadi kesalahan pengiriman.</p>
                                    <p class="text-muted font-italic text-sm mt-0">*Contoh alamat lengkap: Jalan Mangga Kecil No.13, RT 09 RW 03, Kelurahan Besi Tua, Kecamatan Sukaraja, Kab. Binjai, Sumatera Utara, 53421.</p>
                                </div>

                                <div class="form-group divEkspedisi">
                                    <label for="ekspedisi">Ekspedisi</label>
                                    <select name="ekspedisi" id="ekspedisi" class="form-control">
                                        <option value="" selected disabled>Pilih Ekspedisi</option>
                                        @foreach($ekspedisi as $eks)
                                        <option value="{{ $eks->kode_ekspedisi }}">{{ $eks->nama_ekspedisi }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group divEksLain">
                                    <label for="keterangan">Nama Ekspedisi</label>
                                    <input type="text" class="form-control" id="namaEkspedisi" placeholder="Nama Ekspedisi" name="namaEkspedisi" value="{{ old('namaEkspedisi') }}">
                                </div>

                                <div class="form-group divResi">
                                    <label for="">No. Resi</label>
                                    <input type="text" class="form-control" id="noResi" placeholder="No. Resi" name="noResi" value="{{ old('noResi') }}">
                                    <p class="text-muted font-italic text-sm mb-0 mt-1">*Opsional, khusus order dari Marketplace. Hiraukan selain dari marketplace.</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <span>Total : </span><h4 class="font-weight-bold text-danger" id="totalAll"></h4>
                                </div>
                                {{-- Hidden input untuk mengambil nilai dari id totalAll --}}
                                <input type="hidden" name="totalAllInput" id="totalAllInput">
                            </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Informasi Pembayaran</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="metodePembayaran">Metode Pembayaran</label>
                        <select name="metodePembayaran" id="metodePembayaran" class="form-control" required>
                            <option value="" selected disabled>Pilih Metode Pembayaran</option>
                            <option value="Cash">Cash</option>
                            <option value="BCA">Transfer BCA</option>
                            <option value="BNI">Transfer BNI</option>
                            <option value="BRI">Transfer BRI</option>
                            <option value="Mandiri">Transfer Mandiri</option>
                            <option value="Shopee">Saldo Shopee</option>
                            <option value="Tokopedia">Saldo Tokopedia</option>
                            <option value="Bukalapak">Saldo Bukalapak</option>
                            <option value="Bayar Waktu Ambil">Bayar Waktu Ambil</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="statusPembayaran">Status Pembayaran</label>
                        <select name="statusPembayaran" id="statusPembayaran" class="form-control" required>
                            <option value="" selected disabled>Pilih Status Pembayaran</option>
                            <option value="2">Lunas</option>
                            <option value="1">DP(Down Payment)</option>
                        </select>
                        <p class="text-muted font-italic text-sm mb-0 mt-1">*Jika order dari marketplace, bisa ditandai sebagai lunas.</p>
                    </div>

                    <div class="form-group">
                        <label for="jumlahPembayaran">Jumlah Pembayaran</label>
                        <input type="text" class="form-control maskRupiah" id="jumlahPembayaran" placeholder="Contoh : Rp 100.000" name="jumlahPembayaran" required>
                        <p class="text-muted font-italic text-sm mb-0 mt-1">*Untuk marketplace, total jumlah pembayaran adalah total keseluruhan penjualan (termasuk biaya admin)</p>
                    </div>

                    {{-- Upload bukti transfer using dropzone --}}
                    <div class="form-group">
                        <label for="paymentImage">Bukti Pembayaran</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="paymentImage" name="paymentImage">
                                <label class="custom-file-label" for="paymentImage">Pilih Gambar</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <span>Sisa Pembayaran : </span><h4 class="font-weight-bold text-danger" id="sisaPembayaran"></h4>
                            <input type="text" name="sisaPembayaranInput" id="sisaPembayaranInput" hidden>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Tombol Submit Antrikan --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-right">
                        {{-- Tombol Submit --}}
                        <div class="d-flex align-items-center">
                            <button id="submitToAntrian" type="submit" class="btn btn-primary">Submit<div id="loader" class="loader" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @includeIf('page.antrian-workshop.modal.modal-tambah-pelanggan')
    @includeIf('page.antrian-workshop.modal.modal-pilih-produk')
    @includeIf('page.antrian-workshop.modal.modal-edit-produk')
</div>
@endsection

@section('script')
<script src="{{ asset('adminlte/dist/js/maskMoney.min.js') }}"></script>

<script>
    $(function () {
        bsCustomFileInput.init();
    });

    // Tambah Produk
    function tambahProduk() {
        if($('#customer_id').val() == null || $('#customer_id').val() == ""){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Pilih pelanggan terlebih dahulu!',
            });
        }else{
            $('#modalPilihProduk').modal('show');
        }
    }

    function updateTotalBarang(){
        $.ajax({
            url: "/barang/getTotalBarang/" + $('#customer_id').val(),
            method: "GET",
            success: function(data){
                $('#subtotal').html(data.totalBarang);
                $('#totalAll').html(data.totalBarang);
                $('#totalAllInput').val(data.totalBarang);
                $('#sisaPembayaran').html(data.totalBarang);
                $('#sisaPembayaranInput').val(data.totalBarang);
            },
            error: function(xhr, status, error){
                var err = eval("(" + xhr.responseText + ")");
                alert(err.Message);
            }
        });
    }

    function deleteBarang(id){
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Produk akan dihapus dari antrian ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/barang/" + id,
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "DELETE",
                        id: id
                    },
                    success: function(data){
                        $('#tableProduk').DataTable().ajax.url("/barang/show-create/" + $('#customer_id').val()).load();

                        // function updateTotalBarang
                        updateTotalBarang();

                        //tampilkan toast sweet alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Produk berhasil dihapus',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function(xhr, status, error){
                        var err = eval("(" + xhr.responseText + ")");
                        alert(err.Message);
                    }
                });
            }
        });
    }

    function formatPhoneNumber(phoneNumber){
        // Menghapus semua karakter non-digit dari nomor telepon
        const digits = phoneNumber.replace(/\D/g, '');
        // Membagi nomor menjadi array yang berisi substring 4 digit
        const substrings = digits.match(/.{1,4}/g);
        return substrings ? substrings.join('-') : '';
    }

    function resetFormValues() {
        $('#alamatKirim').val('');
        $('#ongkir').val('');
        $('#ekspedisi').val('');
        $('#namaEkspedisi').val('');
        $('#noResi').val('');
    }

    function addRequiredAttributes() {
        $('#alamatKirim').attr('required', true);
        $('#ongkir').attr('required', true);
        $('#ekspedisi').attr('required', true);
    }

    function updateTotalWithoutShipping() {
        var totalBarang = parseInt($('span#subtotal').text().replace(/\D/g, '') || 0);
        var bPacking = parseInt($('#packing').val().replace(/\D/g, '') || 0);
        var bPasang = parseInt($('#pasang').val().replace(/\D/g, '') || 0);
        var diskon = parseInt($('#diskon').val().replace(/\D/g, '') || 0);

        var totalTanpaOngkir = totalBarang + bPacking + bPasang - diskon;
        $('#totalAll').html('Rp ' + totalTanpaOngkir.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        $('#totalAllInput').val(totalTanpaOngkir);
        $('#sisaPembayaran').html('Rp ' + $('#totalAllInput').val());
    }

    function removeRequiredAttributes() {
        $('#alamatKirim, #ongkir, #ekspedisi').removeAttr('required');
    }

    // Fungsi perhitungan total
    function calculateTotal() {
            var packing = parseInt($('#packing').val().replace(/[^0-9]/g, '')) || 0;
            var ongkir = parseInt($('#ongkir').val().replace(/[^0-9]/g, '')) || 0;
            var pasang = parseInt($('#pasang').val().replace(/[^0-9]/g, '')) || 0;
            var diskon = parseInt($('#diskon').val().replace(/[^0-9]/g, '')) || 0;
            var ppn = $('#ppn').val();
            var pph = $('#pph').val();

            //input ppn dan pph dikosongkan


            var totalAll = parseInt($('#subtotal').text().replace(/[^0-9]/g, '')) || 0;

            var pajak = 0;
            if(ppn != '' || ppn != null || ppn != 0){
                pajak += parseInt(ppn.replace(/[^0-9]/g, '')) || 0;
            }
            if(pph != '' || pph != null || pph != 0){
                pajak += parseInt(pph.replace(/[^0-9]/g, '')) || 0;
            }

            if($('divPajak').is(':visible')) {
                if($('input[name=termasukPajak]:checked').val() == 0){
                    totalAll += packing + ongkir + pasang - diskon - pajak;
                }else{
                    totalAll += packing + ongkir + pasang - diskon + pajak;
                }
            }else{
                totalAll += packing + ongkir + pasang - diskon;
            }
            
            var formattedTotal = totalAll.toLocaleString('id-ID'); // Mengubah ke format mata uang Indonesia
            $('#totalAll').html('Rp ' + formattedTotal);
            $('#totalAllInput').val(totalAll);

            if (totalAll == 0) {
                $('#jumlahPembayaran').val(totalAll);
                $('#jumlahPembayaran').prop('readonly', true);
                $('#sisaPembayaran').html('Rp 0');
                $('#statusPembayaran').val('');
            } else {
                $('#jumlahPembayaran').val('');
                $('#jumlahPembayaran').prop('readonly', false);
                $('#sisaPembayaran').html('Rp ' + formattedTotal + ' (Belum Lunas)');
                $('#statusPembayaran').val('');
            }
        }

    function pilihDesain(button, id){
        // Mengembalikan semua tombol ke keadaan semula
        $('.btnDesainan').html('Pilih').removeClass('btn-dark');
        $('#queueId').val(id);

        // Mengubah tombol yang dipilih
        $(button).html('<i class="fas fa-check"></i>').addClass('btn-dark');
    }

    $(document).ready(function() {
        var pelanggan = $('#customer_id').val();

        $('#divPajak').hide();

        //find atribut name termasukPajak
        $('input[name=termasukPajak]').on('change', function(){
            calculateTotal();
        });
        
        $('#modalTelepon').on('keyup', function(){
            var phone = $(this).val();
            $(this).val(formatPhoneNumber(phone));
        });

        $('#usePajak').on('change', function(){
            $('#divPajak').toggle(this.checked);
            $('#ppn, #pph').val('').prop('disabled', true);
            $('#usePPN, #usePPh').prop('checked', false);
            $('#radio1, #radio2').prop('checked', false);
        });

        $('#usePPN').on('change', function(){
            $('#ppn').attr('disabled', !this.checked);
        });

        $('#usePPh').on('change', function(){
            $('#pph').attr('disabled', !this.checked);
        });

        // ketika isOngkir dicentang maka divAlamatKirim, divOngkir, divEkspedisi akan muncul
        $('#isOngkir').on('change', function(){
            if($(this).is(':checked')){
                resetFormValues();
                $('.pengiriman').show();
                addRequiredAttributes();
            } else {
                updateTotalWithoutShipping();
                resetFormValues();
                $('.pengiriman').hide();
                removeRequiredAttributes();
            }
        });

        // function provinsi
        $.ajax({
            url: "{{ route('getProvinsi') }}",
            method: "GET",
            success: function(data){
                //foreach provinsi
                $.each(data, function(key, value){
                    $('#provinsi').append(`<option value="${key}">${value}</option>`);
                });
            }
        });

        // function kota
        $('#provinsi').on('change', function(){
            var provinsi = $(this).val();
            $('#groupKota').show();
            $('#kota').empty().append(`<option value="" selected disabled>Pilih Kota</option>`);
            $.ajax({
                url: "{{ route('getKota') }}",
                method: "GET",
                data: { provinsi: provinsi },
                success: function(data){
                    //foreach kota
                    $.each(data, function(key, value){
                        $('#kota').append(`<option value="${key}">${value}</option>`);
                    });
                }
            });
        });

        // Masking Rupiah
        $('.maskRupiah').maskMoney({prefix:'Rp ', thousands:'.', decimal:',', precision:0});

        $('#tableProduk').DataTable({
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            info: false,
            ajax: {
                url: "/barang/show/" + pelanggan,
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'kategori', name: 'kategori'},
                {data: 'namaProduk', name: 'namaProduk'},
                {data: 'qty', name: 'qty'},
                {data: 'harga', name: 'harga'},
                {data: 'hargaTotal', name: 'hargaTotal'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'accdesain', name: 'accdesain'},
                {data: 'namaFile', name: 'namaFile'},
                {data: 'action', name: 'action'},
            ],
        });

        // function updateTotalBarang
        updateTotalBarang();

        //fungsi untuk menyembunyikan file acc desain saat kosoongAcc dicentang
        $('#kosongAcc').on('change', function(){
            if($(this).is(':checked')){
                // Disable and remove the file input if 'kosongAcc' is checked
                $('#fileAccDesain').prop('required', false).attr('disabled', true).val('');
                // Remove the selected file name from the label
                $('.custom-file-label').text('Pilih File');
                // Remove the file from input file
            } else {
                // Enable the file input if 'kosongAcc' is unchecked
                $('#fileAccDesain').prop('required', true).attr('disabled', false);
            }
        });

        $('#kosongDesain').on('change', function(){
            if($(this).is(':checked')){
                $('#queueId').val(0);
                $('.btnDesainan').html('Pilih').removeClass('btn-dark').addClass('btn-primary').prop('disabled', true);
            } else {
                $('#queueId').val('');
                $('.btnDesainan').prop('disabled', false);
            }
        });

        // Select2 Pelanggan
        $('#customer_id').select2({
            placeholder: 'Pilih Pelanggan',
            ajax: {
                url: '{{ route('pelanggan.search') }}',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return { text: item.nama, id: item.id };
                        })
                    };
                },
                cache: true
            }
        });

        $('#customer_id').on('change', function(){
            $('#tableProduk').DataTable().ajax.url("/barang/show-create/" + $(this).val()).load();
            updateTotalBarang();
            //idPelanggan get from customer_id
            $('#modalPilihProduk #idPelanggan').val($(this).val());
        });

        //function untuk membuat alamat pengiriman sama dengan alamat pada data pelanggan customer
        $('#alamatSama').on('change', function(){
            if($(this).is(':checked')){
                //get id customer
                var id = $('#customer_id').val();
                $.ajax({
                    url: "/pelanggan/" + id,
                    method: "GET",
                    success: function(data){
                        $('#alamatKirim').val(data.alamat);
                    }
                });
            }else{
                $('#alamatKirim').val('');
            }
        });

        //nama produk select2
        $('#modalPilihProduk #kategoriProduk').on('change', function(){

        $('#modalPilihProduk #namaProduk').val(null).trigger('change').empty().append(`<option value="" selected disabled>Pilih Produk</option>`);

        $('#modalPilihProduk #namaProduk').select2({
            placeholder: 'Pilih Produk',
            ajax: {
                url: "{{ route('job.searchByCategory') }}",
                data: function (params) {
                    return {
                        kategoriProduk: $('#kategoriProduk').val(),
                        q: params.term // tambahkan jika ingin mencari berdasarkan keyword
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return { id: item.id, text: item.job_name };
                        })
                    };
                },
                cache: true
            }
        });
    });

        $('#not_iklan').on('change', function(){
            var isChecked = $(this).is(':checked');
            $('#namaProdukIklan, #tahunIklan, #bulanIklan').val('').prop('disabled', isChecked);
            $('#bulanIklan, .divNamaProduk').toggle(!isChecked);
        });

        $('#not_iklan').on('change', function(){
            $('#periode_iklan').val(null).trigger('change').prop('disabled', $(this).is(':checked'));
        });

        $('#tahunIklan').on('change', function(){
            $('#bulanIklan').show();
        });

        $('#bulanIklan').on('change', function(){
            $('#modalPilihProduk .divNamaProduk').show();
        });

        $('#namaProdukIklan').select2({
            placeholder: 'Pilih Produk',
            allowClear: true,
            ajax: {
                url: "{{ route('getAllJobs') }}",
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return { id: item.id, text: item.job_name };
                        })
                    };
                },
                cache: true
            }
        });

        $('#formTambahProduk').on('submit', function(e){
            e.preventDefault();

            $('#formTambahProduk #submitTambahProduk').html('Menyimpan...').prop('disabled', true);

            // Check if "Kosong ACC" is checked
            if ($('#kosongAcc').is(':checked')) {
                $('#fileAccDesain').prop('required', false);
            } else {
                $('#fileAccDesain').prop('required', true);
            }

            // Check if "Tidak Ada Gambar ACC" is checked
            var fileAccDesain = $('#fileAccDesain')[0].files[0];
            if (!fileAccDesain && !$('#kosongAcc').is(':checked')) {
                alert("Silakan upload file ACC atau centang 'Tidak Ada Gambar ACC'.");
                return;
            }

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('barang.store') }}",
                method: "POST",
                //data is form dataInput, acc_desain, _token
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(data){
                    $('#modalPilihProduk').modal('hide');
                    $('#tableProduk').DataTable().ajax.url("/barang/show-create/" + $('#customer_id').val()).load();
                    $('#formTambahProduk')[0].reset();
                    $('#kosongAcc').prop('checked', false);
                    $('#fileAccDesain').prop('required', true).attr('disabled', false);
                    $('#fileAccDesain').next('.custom-file-label').text('Pilih File');
                    $('#tahunIklan, #bulanIklan, #namaProdukIklan').val('').prop('disabled', false);
                    $('#not_iklan').prop('checked', false);
                    //mengembalikan tombol pilih desain ke semula
                    $('.btnDesainan').html('Pilih').removeClass('btn-dark').addClass('btn-primary');
                    $('#queueId').val('');

                    // function updateTotalBarang
                    updateTotalBarang();

                    //tampilkan toast sweet alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Produk berhasil ditambahkan',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(response){
                    var errors = response.responseJSON.errors;
                    var errorMessage = '';
                    $.each(errors, function(key, value){
                        if(key == 'queueId'){
                            errorMessage += 'Desain belum dipilih!';
                        }else{
                            errorMessage += value + '\n';
                        }
                    });
                    alert(errorMessage);
                }
            });
        });

        // Event handler untuk perubahan pada input packing, ongkir, pasang, dan diskon
        $('#packing, #ongkir, #pasang, #diskon').on('keyup', calculateTotal);

        // Event handler untuk perubahan pada checkbox PPN dan PPH
        $('#ppn, #pph').on('change', calculateTotal);

        $('#statusPembayaran').on('change', function(){
            var statusPembayaran = $('#statusPembayaran').val();
            var totalAll = parseInt($('#totalAllInput').val());
            if(statusPembayaran == 2){
                $('#jumlahPembayaran').val($('#totalAllInput').val());
                $('#jumlahPembayaran').attr('readonly', true);
                $('#sisaPembayaran').html('Rp 0');
            }else if(statusPembayaran == 1){
                $('#jumlahPembayaran').val('');
                $('#jumlahPembayaran').attr('readonly', false);
                $('#sisaPembayaran').html('Rp ' + totalAll.toLocaleString('id-ID') + ' (Belum Lunas)');
            }
        });

        // sisaPembayaran akan berubah saat ada perubahan pada inputan jumlahPembayaran - totalAll
        $('#jumlahPembayaran').on('keyup' , function(){
            var jumlahPembayaran = parseInt($(this).val().replace(/\D/g, '') || 0);
            var totalAll = parseInt($('#totalAll').text().replace(/\D/g, '') || 0);

            var sisaPembayaran = totalAll - jumlahPembayaran;
            if(sisaPembayaran < 0){
                sisaPembayaran = "Melebihi Total Pembayaran";
            } else {
                sisaPembayaran = 'Rp ' + sisaPembayaran.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' (Belum Lunas)';
            }

            $('#sisaPembayaran').html(sisaPembayaran);
        });

        //jika ekspedisi dipilih selain Lainnya maka divEksLain akan hide
        $('#ekspedisi').on('change', function(){
            if($(this).val() != 'LAIN'){
                $('.divEksLain').hide();
                //remove required
                $('#namaEkspedisi').removeAttr('required');
            }else{
                $('.divEksLain').show();
                //add required
                $('#namaEkspedisi').attr('required', true);
            }
        });

        // function simpanPelanggan
        $('#pelanggan-form').on('submit', function(e){
            e.preventDefault();

            $('#modalTambahPelanggan #subPelanggan').html('Menyimpan...').prop('disabled', true);

            $.ajax({
                url: "{{ route('pelanggan.store') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    salesID: $('#salesID').val(),
                    nama: $('#modalNama').val(),
                    telepon: $('#modalTelepon').val(),
                    alamat: $('#modalAlamat').val(),
                    instansi: $('#modalInstansi').val(),
                    infoPelanggan: $('#infoPelanggan').val(),
                    provinsi: $('#provinsi').val(),
                    kota: $('#kota').val(),
                },
                success: function(data){
                    $('#modalTambahPelanggan').modal('hide');
                    $('#modalTambahPelanggan #subPelanggan').html('Simpan').prop('disabled', false);
                    $('#pelanggan-form')[0].reset();
                    $('#namaPelanggan').append(`<option value="${data.id}" selected>${data.nama}</option>`);
                    $('#namaPelanggan').val(data.id).trigger('change');
                    $('#namaPelanggan').select2({
                        placeholder: 'Pilih Pelanggan',
                        ajax: {
                            url: "/pelanggan-all/{{ auth()->user()->sales->id }}",
                            dataType: 'json',
                            delay: 250,
                            processResults: function (data) {
                                return {
                                    results:  $.map(data, function (item) {
                                        return {
                                            text: item.nama + ' - ' + item.telepon,
                                            id: item.id,
                                        };
                                    })
                                };
                            },
                            cache: true
                        }
                    });

                    //tampilkan toast sweet alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Pelanggan berhasil ditambahkan',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr, status, error){
                    var err = eval("(" + xhr.responseText + ")");
                    alert(err.Message);
                }
            });
        });
});
</script>
@endsection
