@extends('layouts.app')

@section('title', 'Detail Project')

@section('breadcrumb', 'Detail Project')

@section('page', 'Antrian')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<style>
    @media (max-width: 767px) {
        .col-sm h5, .col-sm h6 {
            font-size: 14px;
        }
    }
</style>
<input type="hidden" id="ticket_order" value="{{ $antrian->ticket_order }}">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-clipboard mr-2"></i> <strong>Status Pesanan </strong></h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row ml-1">
                @php
                    $batas = new DateTime($antrian->dataKerja->tgl_selesai);
                    $selesai = new DateTime($antrian->finish_date);
                @endphp
                @if($antrian->status == 1 && $antrian->cabang_id == null)
                    <p class="text-danger"><i class="fas fa-circle"></i> <span class="ml-2">Belum Diantrikan</span></p>
                @elseif($antrian->status == 1 && $antrian->cabang_id != null)
                    <p class="text-primary"><i class="fas fa-circle"></i> <span class="ml-2">Sedang Dikerjakan</span></p>
                @elseif($antrian->status == 2 && $antrian->cabang_id != null)
                    <p class="text-success"><i class="fas fa-circle"></i> <span class="ml-2">Selesai : <strong>{{ date_format($selesai , 'd F Y - H:i')}}</strong></span></p>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user mr-2"></i> <strong>Data Pelanggan</strong></h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
                        <label for="nama">Nama Pelanggan </label>
                        <p>{{ $antrian->customer->nama ?? '-' }} <span class="badge bg-danger">{{ $antrian->customer->frekuensi_order >= 2 ? 'Repeat Order' : 'Pelanggan Baru'}}</span></p>
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        <label for="nama">Telepon</label>
                        <p>{{ $antrian->customer->telepon ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md">
                    <label for="alamat">Sumber Pelanggan</label>
                    <p>{{ $antrian->customer->infoPelanggan ?? '-' }}</p>
                </div>
                <div class="col-md">
                    <label for="alamat">Instansi</label>
                    <p>{{ $antrian->customer->instansi ?? '-'}}</p>
                </div>
                <div class="col-md">
                    <label for="iklan">Status Iklan</label>
                    <p>Facebook / -</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <label for="alamat">Alamat</label>
                    <p>{{ $antrian->customer->alamat ?? '-'}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-money-bill-wave mr-2"></i> <strong>Informasi Pembayaran</strong></h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <h6 class="mb-3 mr-2"><strong><i class="fas fa-circle"></i> <span class="ml-2">Ticket Order - </span></strong>{{ $antrian->ticket_order }}</h6>
                <h6 class="mb-3 ml-2"><strong><i class="fas fa-circle"></i> <span class="ml-2">Sales : {{ $antrian->sales->sales_name }}</span></strong></h6>
                <div class="ml-auto">
                    <a href="{{ route('report.faktur', $antrian->ticket_order) }}" class="btn btn-primary btn-sm" ><i class="fas fa-print"></i> Faktur</a>
                </div>
            </div>
            <div class="row table-responsive">
                <div class="col-12">
            <table id="tableItems" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Note</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right">Total</th>
                        <th>{{ 'Rp '.number_format($total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
        <div class="row">
            <div class="col pr-4 mt-3">
                <h5><strong>Total Penjualan : </strong><span class="float-right font-weight-bold text-danger">Rp{{ number_format($total, 0, ',', '.') }}</span></h5>
                <h6>Ongkos Kirim<span class="float-right text-danger">Rp{{ !isset($pengiriman->ongkir ) ? '-' : number_format($pengiriman->ongkir, 0, ',', '.') }}</span></h6>
                <h6>Biaya Pasang<span class="float-right text-danger">Rp{{ number_format($antrian->pembayaran->biaya_pasang, 0, ',', '.') }}</span></h6>
                <h6>Biaya Packing<span class="float-right text-danger">Rp{{ number_format($antrian->pembayaran->biaya_packing, 0, ',', '.') }}</span></h6>
                @php
                    $ongkir = !isset($pengiriman->ongkir) ? 0 : $pengiriman->ongkir;
                    $biayaPasang = $antrian->pembayaran->biaya_pasang;
                    $biayaPacking = $antrian->pembayaran->biaya_packing;
                    $totalKeseluruhan = $total + $ongkir + $biayaPasang + $biayaPacking;
                @endphp
                <h5><strong>Total Keseluruhan : </strong><span class="float-right font-weight-bold text-danger" id="totalKeseluruhan">Rp{{ number_format($totalKeseluruhan, 0, ',', '.') }}</span></h5>
                <h6>Diskon<span class="float-right text-danger">-Rp{{ number_format($antrian->pembayaran->diskon, 0, ',', '.') }}</span></h6>
                @php
                    $diskon = $antrian->pembayaran->diskon;
                    $nominal = $antrian->pembayaran->dibayarkan;
                    $totalPendapatan = $totalKeseluruhan - $diskon;
                    $sisaBayar = $totalPendapatan - $nominal;
                @endphp
                <h5><strong>Total Pendapatan : </strong><span class="float-right font-weight-bold text-danger">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</span></h5>
                <h6>Dibayarkan<span class="float-right text-danger">-Rp{{ number_format($nominal, 0, ',', '.') }}</span></h6>
                <h5><strong>Sisa Pembayaran : </strong><span class="float-right font-weight-bold text-danger">Rp{{ number_format($sisaBayar, 0, ',', '.') }}</span></h5>
            </div>
        </div>
        <div class="row mt-2">
                @if($antrian->pembayaran->nominal_pelunasan == null)
                {{-- Alert --}}
                <div class="col-sm-12">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Perhatian!</strong> Pesanan ini belum lunas, silahkan konfirmasi pelunasan.
                        <button onclick="modalPelunasan({{ $antrian->ticket_order }})" type="button" class="btn btn-sm btn-danger float-right" style="display: {{ auth()->user()->role_id != 11 ? 'none' : '' }}" >Unggah Pelunasan</button>
                    </div>
                </div>
                @else
                <div class="col-sm-12">
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check mr-1"></i> <strong>Terima Kasih!</strong> Pesanan ini sudah lunas.
                    </div>
                </div>
                {{-- End Alert --}}
                @endif
        </div>
    </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-folder-open mr-2"></i> <strong>File Cetak & Pendukung</strong></h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis File</th>
                        <th>Produk</th>
                        <th>Nama File</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $file)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>File Cetak</td>
                            <td>{{ $file->job->job_name }}</td>
                            <td>{{ $file->designQueue->file_cetak ?? 'File Tidak Terdeteksi' }}</td>
                            <td>
                                @if($file->designQueue && $file->designQueue->file_cetak)
                                    <a href="{{ route('design.downloadFile', $file->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Unduh</a>
                                @else
                                    <a href="#" class="btn btn-secondary btn-sm disabled"><i class="fas fa-download"></i> Unduh</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="row">
        @foreach ($items as $barang)
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clipboard mr-2"></i> <strong>Penugasan Pekerjaan - {{ $barang->job->job_name }}</strong></h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row ml-1">
                        <div class="col">
                            <h5><strong>Operator</strong></h5>
                                @php
                                    $dataOperator = \App\Models\DataKerja::where('ticket_order', $barang->ticket_order)->where('barang_id', $barang->id)->pluck('operator_id')->first();
                                    $operatorId = explode(',', $dataOperator);
                                    foreach ($operatorId as $item) {
                                        if($item == 'r'){
                                            echo '<p class="text-primary mb-0"><i class="fas fa-circle"></i> Rekanan</p>';
                                        }else{
                                            $operatorTugas = App\Models\Employee::find($item);
                                            //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                            if ($operatorTugas) {
                                                // Tampilkan name dari tabel employees
                                                echo '<p class="text-success mb-0"><i class="fas fa-circle"></i> ' . $operatorTugas->name . '</p>';
                                            } else {
                                                // Jika $antriann null, tampilkan pesan error atau kosongkan
                                                echo '<p class="text-danger mb-0"><i class="fas fa-circle"></i> Belum Ditugaskan</p>';
                                            }
                                        }
                                    }
                                @endphp
                        </div>
                        <div class="col">
                            <h5><strong>Finishing</strong></h5>
                                @php
                                    $dataFinisher = \App\Models\DataKerja::where('ticket_order', $barang->ticket_order)->where('barang_id', $barang->id)->pluck('finishing_id')->first();
                                    $finisherId = explode(',', $dataFinisher);
                                    foreach ($finisherId as $item) {
                                        if($item == 'r'){
                                            echo '<p class="text-primary mb-0"><i class="fas fa-circle"></i> Rekanan</p>';
                                        }
                                        else{
                                            $finishingTugas = App\Models\Employee::find($item);
                                            //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                            if ($finishingTugas) {
                                                // Tampilkan name dari tabel employees
                                                echo '<p class="text-success mb-0"><i class="fas fa-circle"></i> ' . $finishingTugas->name . '</p>';
                                            } else {
                                                // Jika $antriann null, tampilkan pesan error atau kosongkan
                                                echo '<p class="text-danger mb-0"><i class="fas fa-circle"></i> Belum Ditugaskan</p>';
                                            }
                                        }
                                    }
                                @endphp
                        </div>
                        <div class="col">
                            <h5><strong>Quality Control</strong></h5>
                                @php
                                    $dataQC = \App\Models\DataKerja::where('ticket_order', $barang->ticket_order)->where('barang_id', $barang->id)->pluck('qc_id')->first();
                                    $qcId = explode(',', $dataQC);
                                    foreach ($qcId as $item) {
                                            $qcTugas = App\Models\Employee::find($item);
                                            //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                            if ($qcTugas) {
                                                // Tampilkan name dari tabel employees
                                                echo '<p class="text-success mb-0"><i class="fas fa-circle"></i> ' . $qcTugas->name . '</p>';
                                            } else {
                                                // Jika $antriann null, tampilkan pesan error atau kosongkan
                                                echo '<p class="text-danger mb-0"><i class="fas fa-circle"></i> Belum Ditugaskan</p>';
                                            }
                                        }
                                @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-truck mr-2"></i> <strong>Informasi Pengiriman</strong></h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md">
                    <h5><strong>Alamat Pengiriman</strong></h5>
                    <p>{{ !isset($pengiriman->alamat_pengiriman) ? '-' : $pengiriman->alamat_pengiriman }}</p>
                </div>
                <div class="col-md">
                    <h5><strong>Ekspedisi Pengiriman</strong></h5>
                    <p>{{ !isset($pengiriman->ekspedisi) ? '-' : $pengiriman->ekspedisi}}</p>
                </div>
                <div class="col-md">
                    <h5><strong>Biaya Pengiriman</strong></h5>
                    <p>Rp{{ !isset($pengiriman->ongkir) ? '-' : number_format($pengiriman->ongkir, 0, ',', '.') }}</p>
                </div>
                <div class="col-md">
                    <h5><strong>Resi (Airway Bill)</strong></h5>
                    <p>{{ !isset($pengiriman->no_resi) || $pengiriman->no_resi == null ? '-' : $pengiriman->no_resi }}</p>
                </div>

            </div>
        </div>
    </div>
    
    @includeIf('page.antrian-workshop.modal.modal-ref-acc')
    @includeIf('page.antrian-workshop.modal.modal-bukti-pembayaran')
    @includeIf('page.antrian-workshop.modal.modal-pelunasan')
@endsection

@section('script')
<script src="{{ asset('adminlte/dist/js/maskMoney.min.js') }}"></script>

    <script>
        //menampilkan modal pelunasan
        function modalPelunasan(id) {
            $('#modalPelunasan').modal('show');
            $('#modalPelunasan #ticketPelunasan').val(id);
        }
        //menampilkan modal gambar acc desain
        function modalRefACC() {
            $('#modalRefACC').modal('show');
        }

        function modalBuktiPembayaran() {
            $('#modalBuktiPembayaran').modal('show');
        }

        //menampilkan form modal tambah bahan
        function modalBahan() {
            $('#modalBahan').modal('show');
        }

        //fungsi untuk menandai selesai hitung BP
        function tandaiSelesaiHitungBP() {
            var omset = $('#totalKeseluruhan').text();

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Biaya Produksi akan disimpan dan tidak dapat diubah lagi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Tandai Selesai',
                cancelButtonText: 'Batal'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('biaya.produksi.update', $antrian->ticket_order) }}",
                        type: "POST",
                        data: {
                            _method: "PUT",
                            _token: "{{ csrf_token() }}",
                            omsetTotal: omset,
                        },
                        success: function(response) {
                            //muncul toast success
                            var Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'Biaya Produksi berhasil disimpan !'
                            });

                            //ajax reload table
                            $('#tabelBahan').DataTable().ajax.reload();
                            //refresh ajax dari route bahan.total
                            $.ajax({
                                url: "{{ route('bahan.total', $antrian->ticket_order) }}",
                                type: "GET",
                                success: function(response) {
                                    $('#totalProduksi').html(response.totalProduksi + ' (' + response.persenProduksi + ')');
                                    $('#bahanTotal').html(response.total);
                                    $('#profit').html(response.profit + ' (' + response.persenProfit + ')');
                                },
                                error: function(xhr) {
                                    console.log(xhr.responseText);
                                }
                            });
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            })
        }

        //menampilkan daftar bahan produksi
        $('#tabelBahan').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                paging: false,
                searching: false,
                info: false,
                ajax: {
                    url: "{{ route('bahan.show', $antrian->ticket_order) }}",
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'id'},
                    {data: 'nama_bahan', name: 'nama_bahan'},
                    {data: 'harga', name: 'harga'},
                    {data: 'note', name: 'note'},
                    {data: 'barang', name: 'barang'},
                    {data: 'aksi', name: 'aksi'},
                ],
            });

        //ajax untuk menghapus bahan
        function deleteBahan(id) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Bahan akan dihapus dari daftar biaya produksi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('bahan') }}/"+id,
                        type: "POST",
                        data: {
                            "_method": "DELETE",
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            //muncul toast success
                            var Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'Bahan berhasil dihapus'
                            });

                            //ajax reload table
                            $('#tabelBahan').DataTable().ajax.reload();
                            //refresh ajax dari route bahan.total
                            $.ajax({
                                url: "{{ route('bahan.total', $antrian->ticket_order) }}",
                                type: "GET",
                                success: function(response) {
                                    $('#totalProduksi').html(response.totalProduksi + ' (' + response.persenProduksi + ')');
                                    $('#bahanTotal').html(response.total);
                                    $('#profit').html(response.profit + ' (' + response.persenProfit + ')');
                                },
                                error: function(xhr) {
                                    console.log(xhr.responseText);
                                }
                            });

                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            })
        }

        $(document).ready(function() {
            //maskMoney untuk nominal pelunasan pada modal
            $('#nominal').maskMoney({prefix:'Rp ', thousands:'.', decimal:',', precision:0});

            $('#tutupModalBahan').on('click', function() {
                $('#modalBahan').modal('hide');
                //reset form
                $('#formBahan').trigger('reset');
            });

            //ONCHANGE JENIS PELUNASAN
            $('#jenisPelunasan').on('change', function() {
                var jenis = $(this).val();
                if(jenis == 'TF'){
                    $('#formFilePelunasan').show();
                    $('#filePelunasan').attr('required', true);
                }
                else if(jenis == 'TU'){
                    $('#formFilePelunasan').hide();
                    $('#filePelunasan').attr('required', false);
                }
            });

            $("#filePelunasan").on("change", function() {
                const file = $(this)[0].files[0];

                if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    $("#judulTampilan").show();
                    $("#preview-image").attr("src", event.target.result);
                };

                reader.readAsDataURL(file);
                } else {
                    $("#judulTampilan").hide();
                    $("#preview-image").attr("src", "");
                }
            });

            //ajax untuk menampilkan total biaya bahan
            $.ajax({
                url: "{{ route('bahan.total', $antrian->ticket_order) }}",
                type: "GET",
                success: function(response) {
                    $('#totalProduksi').html(response.totalProduksi + ' (' + response.persenProduksi + ')');
                    $('#bahanTotal').html(response.total);
                    $('#profit').html(response.profit + ' (' + response.persenProfit + ')');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });

            //format rupiah menggunakan maskMoney
            $('#harga_bahan').maskMoney({prefix:'Rp ', thousands:'.', decimal:',', precision:0});

            $('.keterangan').each(function() {
                var text = $(this).text();
                $(this).html(text.replace(/\n/g, '<br/>'));
            });

            //ajax untuk menambahkan bahan
            $('#formBahan').on('submit', function(e){
                e.preventDefault();
                var nama_bahan = $('#nama_bahan').val();
                var harga_bahan = $('#harga_bahan').val();
                var note = $('#note').val();
                var ticket_order = $('#ticket_order').val();
                var barang = $('#barang').val();

                $.ajax({
                    url: "{{ route('bahan.store') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        nama_bahan: nama_bahan,
                        harga_bahan: harga_bahan,
                        note: note,
                        ticket_order: ticket_order,
                        barangId: barang,
                    },
                    success: function(response) {
                        $('#modalBahan').modal('hide');
                        //muncul toast success
                        var Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Bahan berhasil ditambahkan'
                        });

                        //ajax reload table
                        $('#tabelBahan').DataTable().ajax.reload();
                        //refresh ajax dari route bahan.total
                        $.ajax({
                                url: "{{ route('bahan.total', $antrian->ticket_order) }}",
                                type: "GET",
                                success: function(response) {
                                    $('#totalProduksi').html(response.totalProduksi + ' (' + response.persenProduksi + ')');
                                    $('#bahanTotal').html(response.total);
                                    $('#profit').html(response.profit + ' (' + response.persenProfit + ')');
                                },
                                error: function(xhr) {
                                    console.log(xhr.responseText);
                                }
                            });
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            })

            $('#nominal').on('keyup', function() {
                var nominal = $(this).val();
                // hilangkan Rp dan titik lalu ubah ke integer
                nominal = parseInt(nominal.replace(/Rp|\.|/g, ''));
                var sisaBayar = {{ $sisaBayar }};
                if(nominal > sisaBayar){
                    $('#errorNominal').text('Nominal tidak boleh melebihi sisa pembayaran!');
                    $('#btnPelunasan').attr('disabled', true);
                }else{
                    $('#errorNominal').text('');
                    $('#btnPelunasan').attr('disabled', false);
                }
            });

            //ajax untuk menampilkan barang yang dipesan
            $('#tableItems').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                paging: false,
                searching: false,
                info: false,
                ajax: {
                    url: "{{ route('barang.show', $antrian->ticket_order) }}",
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'id'},
                    {data: 'nama_produk', name: 'nama_produk'},
                    {data: 'note', name: 'note'},
                    {data: 'harga', name: 'harga'},
                    {data: 'qty', name: 'qty'},
                    {data: 'subtotal', name: 'subtotal'},
                ],
            });
        });
    </script>
@endsection
