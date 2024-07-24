<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Software Antree | Kassab Syariah</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/dist/css/adminlte.min.css">

    <link href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- Dropzone --}}
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
</head>
<body onload="printAndReturn()">
    <div class="wrapper">
        <section class="invoice">
            <div class="row mb-5">
                <h4 class="col-6 font-weight-bold">Faktur Penjualan</h4>
                <div class="col-6 text-right">
                    <p class="font-weight-bold">No Invoice : {{ $penjualan->no_invoice }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <address>
                        <strong>DITERBITKAN ATAS NAMA</strong><br>
                        Penjual :
                        <strong>{{ $sales->sales_name }}</strong><br>
                        {{ $sales->address }}<br>
                        Telp: {{ $sales->sales_phone }}
                    </address>
                </div>
                <div class="col-6">
                    <div class="row">
                        <div class="col-6">
                            <address>
                                <strong>Pembeli</strong><br>
                                <strong>{{ $penjualan->customer->nama }}</strong><br>
                                {{ ucwords($penjualan->customer->alamat) }}<br>
                                Telp: {{ $penjualan->customer->telepon }}
                            </address>
                        </div>
                        <div class="col-6">
                            <address>
                                <strong>Tanggal Penjualan</strong><br>
                                {{ date_format($penjualan->created_at, 'd F Y') }}<br>
                            </address>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
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
                                <td class="text-center">Rp. {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->jumlah }}</td>
                                <td class="text-center font-weight-bold">Rp. {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right font-weight-bold">Total</td>
                                <td class="text-center font-weight-bold">Rp. {{ number_format($total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-6"></div>
                <div class="col-6">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="text-right">Diskon : </h6>
                        </div>
                        <div class="col-6">
                            <h6 class="text-center">Rp. {{ number_format($diskon, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h6 class="text-right">Grand Total (Pembulatan) : </h6>
                        </div>
                        <div class="col-6">
                            <h5 class="text-center font-weight-bold text-success">Rp. {{ number_format($penjualan->total, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-6">
                    <h6>Keterangan : </h6>
                    <h6 class="font-weight-bold">{{ $penjualan->keterangan == 'datang' ? 'Datang ke Toko' : 'Dikirim' }}</h6>
                    <h6>{{ $penjualan->customer->nama }} - {{ $penjualan->telepon }}</h6>
                    <h6>{{ $penjualan->alamat }}</h6>
                </div>
                <div class="col-6">
                    <h6>Metode Pembayaran : </h6>
                    <h6 class="font-weight-bold">{{ ucwords($penjualan->metode_pembayaran) }} - {{ $rekening }}</h6>
                </div>
            </div>
        </section>
    </div>
    <script>
        function printAndReturn() {
            window.onafterprint = function() {
                // Navigasi kembali ke halaman sebelumnya setelah pencetakan selesai
                window.history.back();
            };

            // Memulai pencetakan
            window.print();
        }
    </script>
</body>
</html>
