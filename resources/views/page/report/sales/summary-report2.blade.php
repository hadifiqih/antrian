@extends('layouts.app')

@section('title', 'Antrian | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Antrian')

@section('breadcrumb', 'Antrian Stempel')

@section('content')
<style>
    .container {
        margin-top: 20px;
    }
    .card {
        margin-bottom: 20px;
    }
    .header {
        text-align: center;
        margin-bottom: 30px;
    }
    .table-responsive {
        margin-top: 20px;
    }
</style>
    <div class="container">
        <div class="header">
            <h1 class="font-weight-bold">Laporan Produktivitas</h1>
            <h2>{{ $sales->sales_name }}</h2>
        </div>

        <!-- Company Information -->
        <div class="card">
            <div class="card-header">
                <h4>Informasi Umum</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Target Market</th>
                            <td>{{ ucwords("UMKM, Corporate, perusahaan, pemerintah, perkantoran, pabrik, notaris, universitas, perorangan, hotel, rumah sakit, klinik, dll") }}</td>
                        </tr>
                        <tr>
                            <th>Target Omset</th>
                            <td>Rp {{ number_format($sales->target_omset,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Omset Tertinggi</th>
                            <td>Rp {{ $sales->omset_tertinggi }}</td>
                        </tr>
                        <tr>
                            <th>Rata-rata Omset</th>
                            <td>Rp </td>
                        </tr>
                        <tr>
                            <th>Produk Bernilai Tinggi</th>
                            <td>Trodat dengan qty banyak/grosir, stempel custom, hot stamp, stampo</td>
                        </tr>
                        <tr>
                            <th>Total Pelanggan</th>
                            <td>{{ $totalPelanggan }}</td>
                        </tr>
                        <tr>
                            <th>Pelanggan Loyal (RO)</th>
                            <td>{{ $pelangganLoyal }}</td>
                        </tr>
                        <tr>
                            <th>Rata-rata Pelanggan Baru / Hari</th>
                            <td>{{ $avgPelangganBaru }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales Information -->
        <div class="card">
            <div class="card-header">
                <h4>Informasi Sosial Media</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Social Media Accounts</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Repeat this block for each sales person -->
                            <tr>
                                <td>[Sales Name]</td>
                                <td>[Sales Email]</td>
                                <td>[Sales Phone]</td>
                                <td>
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Facebook:</strong> [Facebook Account]</li>
                                        <li class="list-group-item"><strong>Instagram:</strong> [Instagram Account]</li>
                                        <li class="list-group-item"><strong>TikTok:</strong> [TikTok Account]</li>
                                        <li class="list-group-item"><strong>YouTube:</strong> [YouTube Account]</li>
                                    </ul>
                                </td>
                            </tr>
                            <!-- End of block -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Social Media Overview -->
        <div class="card">
            <div class="card-header">
                <h4>Social Media Overview</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Facebook</h5>
                        <ul class="list-group">
                            <!-- Repeat this block for each account -->
                            <li class="list-group-item"><strong>Account:</strong> [Facebook Account]</li>
                            <li class="list-group-item"><strong>Followers:</strong> [Followers]</li>
                            <!-- End of block -->
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Instagram</h5>
                        <ul class="list-group">
                            <!-- Repeat this block for each account -->
                            <li class="list-group-item"><strong>Account:</strong> [Instagram Account]</li>
                            <li class="list-group-item"><strong>Followers:</strong> [Followers]</li>
                            <!-- End of block -->
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>TikTok</h5>
                        <ul class="list-group">
                            <!-- Repeat this block for each account -->
                            <li class="list-group-item"><strong>Account:</strong> [TikTok Account]</li>
                            <li class="list-group-item"><strong>Followers:</strong> [Followers]</li>
                            <!-- End of block -->
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>YouTube</h5>
                        <ul class="list-group">
                            <!-- Repeat this block for each account -->
                            <li class="list-group-item"><strong>Account:</strong> [YouTube Account]</li>
                            <li class="list-group-item"><strong>Followers:</strong> [Followers]</li>
                            <!-- End of block -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Frequencies -->
        <div class="card">
            <div class="card-header">
                <h4>Update Frequencies</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Instagram</h5>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Story Updates per Day:</strong> 3</li>
                            <li class="list-group-item"><strong>Feed Updates per Day:</strong> 1</li>
                            <li class="list-group-item"><strong>Reels Updates per Day:</strong> 1</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Facebook</h5>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Story Updates per Day:</strong> 3 (1x Pagi, 1x Siang, 1x Sore)</li>
                            <li class="list-group-item"><strong>Feed Updates per Day:</strong> 1</li>
                            <li class="list-group-item"><strong>Reels Updates per Day:</strong> 1</li>
                            <li class="list-group-item"><strong>Page Updates per Day:</strong> 1</li>
                            <li class="list-group-item"><strong>FBM Products:</strong> 9</li>
                            <li class="list-group-item"><strong>FB Groups:</strong> 6</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>TikTok</h5>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Updates per Day:</strong> 1</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>YouTube</h5>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Updates per Day:</strong> 1</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Shopee</h5>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Updates per Day:</strong>
                                <li class="list-group-item"><strong>Updates per Day:</strong> 5x Pagi, 5x Sore</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advertising and Sales -->
            <div class="card">
                <div class="card-header">
                    <h4>Advertising and Sales</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Advertising Costs</h5>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Stampo:</strong> Rp315.000</li>
                                <li class="list-group-item"><strong>Stempel Bongkar Pasang (Beton):</strong> Rp315.000</li>
                                <li class="list-group-item"><strong>Products Advertised:</strong> Stampo, Stempel Bongkar Pasang (Beton)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Sales</h5>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Stamp Sold:</strong> [Jumlah pcs tidak dicantumkan]</li>
                                <li class="list-group-item"><strong>Stampo Sold:</strong> 4 pcs</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Broadcast and Leads</h5>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Broadcasts per Day:</strong> 10-20 akun/nomor</li>
                                <li class="list-group-item"><strong>Offers per Day:</strong> 1-2</li>
                                <li class="list-group-item"><strong>Leads per Day:</strong> 20-30</li>
                                <li class="list-group-item"><strong>Orders from Leads per Day:</strong> 5-10</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('script')

@endsection
