@extends('layouts.app')

@section('title', 'Antrian | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Antrian')

@section('breadcrumb', 'Antrian Stempel')

@section('content')
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Whoops!</strong> Terdapat kesalahan saat input data:
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sukses!</strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<style>
    body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .dashboard-header {
        background-color: #4a90e2;
        color: white;
        padding: 20px 0;
        margin-bottom: 30px;
    }
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    .card-header {
        background-color: #ffffff;
        border-bottom: 2px solid #4a90e2;
        font-weight: bold;
        color: #333;
    }
    .stat-card {
        text-align: center;
        padding: 20px;
    }
    .stat-card i {
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: #4a90e2;
    }
    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
    }
    .stat-card .stat-label {
        font-size: 0.9rem;
        color: #666;
    }
    .social-icons i {
        font-size: 1.8rem;
        margin-right: 15px;
        color: #4a90e2;
    }
    .table th {
        font-weight: normal;
        color: #666;
    }
    .table td {
        font-weight: bold;
        color: #333;
    }
    @media (max-width: 768px) {
        .dashboard-header h1 {
            font-size: 1.5rem;
        }
        .stat-card {
            margin-bottom: 15px;
        }
        .stat-card i {
            font-size: 2rem;
        }
        .stat-card .stat-value {
            font-size: 1.2rem;
        }
        .stat-card .stat-label {
            font-size: 0.8rem;
        }
        .social-icons i {
            font-size: 1.5rem;
            margin-right: 10px;
        }
        .table {
            font-size: 0.9rem;
        }
    }
</style>

@if(auth()->user()->role->role_slug == 'CEO' || auth()->user()->role->role_slug == 'DIRUT')
<div class="container mb-3">
    <form action="{{ route('sales.summaryReport') }}" method="GET">
        <div class="form-group">
            <label for="sales">Sales <span class="text-danger">*</span></label>
            <select class="form-control select2" id="idSales" name="sales_id" style="width: 100%" required>
                @foreach ($salesAll as $item)
                    <option value="{{ $item->id }}"{{ $salesId->id == $item->id ? 'selected' : ''}}>{{ $item->sales_name }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>
@endif

<div class="dashboard-header" style="border-radius: 10px">
    <div class="container">
        <h1 class="display-4 font-weight-bold mx-3">Dashboard Performa Sales</h1>
        <h6 class="display-8 font-weight-bold mx-3">{{ $salesId->sales_name }}</h6>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="card stat-card">
                <i class="fas fa-chart-line"></i>
                <div class="stat-value">Rp {{ $omsetBulanan }}</div>
                <div class="stat-label font-weight-bold {{ $dalamPersen >= 100 ? 'text-success' : 'text-danger' }}">Tercapai {{ $dalamPersen }}%</div>
                <div class="stat-label">Capaian Omset</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card">
                <i class="fas fa-trophy"></i>
                <div class="stat-value">Rp {{ number_format($salesId->omset_tertinggi,0,',','.') }}</div>
                <div class="stat-label">Omset Tertinggi</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-value">{{ $totalPelanggan }}</div>
                <div class="stat-label">Total Pelanggan</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card">
                <i class="fas fa-star"></i>
                <div class="stat-value">{{ $pelangganLoyal }}</div>
                <div class="stat-label">Pelanggan Loyal</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Informasi Umum</div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Target Omset</th>
                            <td>Rp {{ number_format($targetSales, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Rata-rata Pelanggan Baru/Hari</th>
                            <td>{{ $avgPelangganBaru }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Target Pasar & Produk Unggulan</div>
                <div class="card-body">
                    <h6>Target Pasar:</h6>
                    <p>UMKM, Corporate, perusahaan, pemerintah, perkantoran, pabrik, notaris, universitas, perorangan, hotel, rumah sakit, klinik, dll</p>
                    <h6>Produk dengan Nilai Penjualan Tertinggi:</h6>
                    <ul>
                        @foreach ($productHighOmset as $produk)
                            <li>{{ $produk->job->job_name }} (Rp {{ number_format($produk->price, 0, ',', '.') }})</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Media Sosial <button class="btn btn-primary btn-sm float-right" onclick="showFormMedsos()" >Isi Form</button></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 social-media-card mb-3">
                            <i class="fab fa-instagram social-media-icon"></i>
                            <span class="social-media-name font-weight-bold">Instagram</span>
                            @foreach ($igs as $ig)
                                <div class="social-media-account">{{ $loop->iteration }}. {{ $ig->username }} : {{ $ig->update_followers }} pengikut</div>
                            @endforeach
                        </div>
                        <div class="col-md-6 col-lg-4 social-media-card mb-3">
                            <i class="fab fa-facebook social-media-icon"></i>
                            <span class="social-media-name font-weight-bold">Facebook</span>
                            @foreach ($fbs as $fb)
                                <div class="social-media-account">{{ $loop->iteration }}. {{ $fb->username }} : {{ $fb->update_followers }} pengikut</div>
                            @endforeach
                        </div>
                        <div class="col-md-6 col-lg-4 social-media-card mb-3">
                            <i class="fab fa-tiktok social-media-icon"></i>
                            <span class="social-media-name font-weight-bold">TikTok</span>
                            @foreach ($tts as $tt)
                                <div class="social-media-account">{{ $loop->iteration }}. {{ $tt->username }} : {{ $tt->update_followers }} pengikut</div>
                            @endforeach
                        </div>
                        <div class="col-md-6 col-lg-4 social-media-card mb-3">
                            <i class="fab fa-youtube social-media-icon"></i>
                            <span class="social-media-name font-weight-bold">YouTube</span>
                            @foreach ($yts as $yt)
                                <div class="social-media-account">{{ $loop->iteration }}. {{ $yt->username }} : {{ $yt->update_followers }} pengikut</div>
                            @endforeach
                        </div>
                        <div class="col-md-6 col-lg-4 social-media-card mb-3">
                            <i class="fas fa-shopping-bag social-media-icon"></i>
                            <span class="social-media-name font-weight-bold">Shopee</span>
                            @foreach ($sps as $sp)
                                <div class="social-media-account">{{ $loop->iteration }}. {{ $sp->username }} : {{ $sp->update_followers }} pengikut</div>
                            @endforeach
                        </div>
                        <div class="col-md-6 col-lg-4 social-media-card mb-3">
                            <i class="fas fa-shopping-bag social-media-icon"></i>
                            <span class="social-media-name font-weight-bold">Tokopedia</span>
                            @foreach ($tps as $tp)
                                <div class="social-media-account">{{ $loop->iteration }}. {{ $tp->username }} : {{ $tp->update_followers }} pengikut</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="text-sm">Aktivitas Harian | {{ date_format(now(), 'd-m-Y') }}
                    @if(Auth::user()->role->role_name == 'Marketing Online')
                        <button class="btn btn-sm btn-primary float-right" onclick="showFormAktivitas()">Isi Form</button>
                    @elseif (Auth::user()->role->role_name == 'Sales')
                        <button class="btn btn-sm btn-primary float-right" onclick="showFormAktivitasSales()">Isi Form</button>
                    @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr><th>Update Instagram</th><td>Story: {{ $dailyActivitiesIgs->where('jenis_konten', 'Story')->count() }}x,<br>Feed: {{ $dailyActivitiesIgs->where('jenis_konten', 'Feed')->count() }}x,<br>Reels: {{ $dailyActivitiesIgs->where('jenis_konten', 'Reels')->count() }}x</td></tr>
                                <tr><th>Update Facebook</th><td>Story: {{ $dailyActivitiesFbs->where('jenis_konten', 'Story')->count() }}x,<br>Feed: {{ $dailyActivitiesFbs->where('jenis_konten', 'Feed')->count() }}x,<br>Reels: {{ $dailyActivitiesFbs->where('jenis_konten', 'Reels')->count() }}x,<br>Halaman: {{ $dailyActivitiesFbs->where('jenis_konten', 'Reels')->count() }}x,<br>FBM: {{ $dailyActivitiesFbs->where('jenis_konten', 'FBM')->count() }}x,<br>FB Grup: {{ $dailyActivitiesFbs->where('jenis_konten', 'FBG')->count() }}</td></tr>
                                <tr><th>Video TikTok</th><td>{{ $dailyActivitiesTts->where('jenis_konten', 'Video')->count() }}x</td></tr>
                                <tr><th>Video YouTube</th><td>{{ $dailyActivitiesYts->where('jenis_konten', 'Video')->count() }}x</td></tr>
                                <tr><th>Naikkan Shopee</th><td>{{ $dailyActivitiesSps->where('jenis_konten', 'Naikkan')->where('keterangan', 'Pagi')->count() }}x Pagi, {{ $dailyActivitiesSps->where('jenis_konten', 'Naikkan')->where('keterangan', 'Sore')->count() }}x Sore</td></tr>
                                <tr><th>Naikkan Tokopedia</th><td>{{ $dailyActivitiesTps->where('jenis_konten', 'Naikkan')->where('keterangan', 'Pagi')->count() }}x Pagi, {{ $dailyActivitiesTps->where('jenis_konten', 'Naikkan')->where('keterangan', 'Sore')->count() }}x Sore</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tr><th>Broadcast</th><td>{{ $dailyActivitiesWa->where('jenis_konten', 'Broadcast')->count() }}x</td></tr>
                                <tr><th>Penawaran</th><td>{{ $dailyActivitiesWa->where('jenis_konten', 'Penawaran')->count() }}x</td></tr>
                                <tr><th>Lead Masuk</th><td>{{ $customerOrderFromLead }}</td></tr>
                                <tr><th>Order dari Lead</th><td>{{ $customerLead }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@includeIf('page.report.sales.modal.form-medsos')
@includeIf('page.report.sales.modal.form-aktivitas')
@includeIf('page.report.sales.modal.form-aktivitas-sales')
@endsection

@section('script')
<script>
    function showFormMedsos() {
        $('#modalMedsos').modal('show');
    }

    function showFormAktivitas() {
        $('#modalAktivitas').modal('show');
    }

    function showFormAktivitasSales() {
        $('#modalAktivitasSales').modal('show');
    }

    $(document).ready(function() {
        var selectedPlatform = '';
        $('#platform').on('change', function() {
            selectedPlatform = $(this).val();
            $('#akun').val(null).trigger('change');
            $('#akun').prop('disabled', false);

            $('#akun').select2({
                placeholder: 'Pilih akun',
                allowClear: true,
                ajax: {
                    url: `/sales/get-sosmed-by-platform/${selectedPlatform}`,
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.platform + ' - ' + item.username
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        });

        $('#idSales').on('change', function() {
            var idSales = $(this).val();
            window.location.href = `/sales/summary-report?sales_id=${idSales}`;
        });

        $('#formMedsos').on('show.bs.modal', function() {
            $('#platform').val('');
            $('#akun').val('');
            $('#followers').val('');
        });

        $('#formUpdateFollowers').on('submit', function(e) {
            e.preventDefault();
            var platform = $('#platform').val();
            var akun = $('#akun').val();
            var followers = $('#followers').val();

            $.ajax({
                url: `{{ route('social-record.store') }}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    platform: platform,
                    akun: akun,
                    followers: followers
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });

                    $('#formMedsos').modal('hide');
                    // Reload the page
                    location.reload();
                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.responseJSON.message
                    });
                }
            });
        });

        $('#modalAktivitas').on('show.bs.modal', function() {
            $('#sales_id').val('');
            $('#user_id').val('');
            $('#platform').val('');
            $('#jenis_konten').val('');
            $('#jumlah').val('');
            $('#keterangan').val('');
            $('#lampiran').val('');
        });
    });
</script>
@endsection
