@extends('layouts.app')

@section('title', 'Edit Antrian | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Antrian')

@section('breadcrumb', 'Edit Antrian')

@section('content')
<style>
    .spesifikasi {
        white-space: pre-line;
    }
    .select2-selection__choice {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: #fff !important;
        padding: 0 10px !important;
    }
    .select2-selection__choice__remove {
        color: #fff !important;
        
    }
</style>

<div class="container-fluid">
    {{-- Data Barang --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Detail Produk</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="font-weight-bold">Data Barang</h5>
                    <div class="table-responsive">
                        <table id="tabelBarang" class="table table-responsive display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Catatan</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total</th>
                                    <th id="jumlahBarang">{{ $totalBarang }}</th>
                                    <th id="jumlahHargaBarang">Rp{{ $totalHargaBarang }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach($barangs as $barang)
        @php
            $namaProduk = $barang->job->job_name;
        @endphp
    <div class="row">
    <div class="col-md-12">
        <div class="card collapsed-card mb-4">
            <div class="card-header">
                <h2 class="card-title">Penugasan Pengerjaan - {{ $namaProduk }}</h2>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
        <form id="formEditAntrian{{ $barang->id }}" action="{{ route('antrian.update', $barang->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="job_id" value="{{ $barang->job->id }}">
        <input type="hidden" name="ticketOrder" value="{{ $antrian->ticket_order }}">
        <div class="row ml-1">
            <h5 class="font-weight-bold">Pilih Operator :</h5>
        </div>
        @if($operators != null)
        <div class="row">
            @foreach($operators as $operator)
            <div class="col-md-3">
                <div class="form-group">
                    <div class="form-check">
                        @php
                            $operatorId = \App\Models\DataKerja::where('barang_id', $barang->id)->pluck('operator_id')->first();
                            $implodeOperatorId = explode(',', $operatorId);
                            
                            if($operatorId != null){
                                $isCheckedOperator = in_array($operator->id, $implodeOperatorId) ? 'checked' : '';
                            } else {
                                $isCheckedOperator = false;
                            }
                        @endphp
                        <input name="operator_id[]" value="{{ $operator->id }}" class="form-check-input" type="checkbox" {{ $isCheckedOperator }}>
                        <label class="form-check-label">{{ $operator->name }}</label>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="col-md-3">
                <div class="form-group">
                    <div class="form-check">
                        @php
                            $isOprRekanan = in_array('r', $implodeOperatorId) ? 'checked' : '';
                        @endphp
                        <input name="operator_id[]" value="r" class="form-check-input" type="checkbox" {{ $isOprRekanan }}>
                        <label class="form-check-label"><span>Rekanan</span></label>
                    </div>
                </div>
            </div>
        </div>
        @else
        -
        @endif
        <hr>

        <div class="row ml-1">
            <h5 class="font-weight-bold">Pilih Finishing :</h5>
        </div>
        @if($operators != null)
        <div class="row">
            @foreach($operators as $operator)
            <div class="col-md-3">
                <div class="form-group">
                    <div class="form-check">
                        @php
                            $finishingId = \App\Models\DataKerja::where('barang_id', $barang->id)->pluck('finishing_id')->first();
                            $finishingId = explode(',', $finishingId);
                            if($finishingId != null){
                                $isCheckedFinishing = in_array($operator->id, $finishingId) ? 'checked' : '';
                            } else {
                                $isCheckedFinishing = false;
                            }
                        @endphp
                        <input name="finishing_id[]" value="{{ $operator->id }}" class="form-check-input" type="checkbox" {{ $isCheckedFinishing }}>
                        <label class="form-check-label">{{ $operator->name }}</label>
                    </div>
                </div>
            </div>  
            @endforeach
            <div class="col-md-3">
                <div class="form-group">
                    <div class="form-check">
                        @php
                            $isFinRekanan = in_array('r', $finishingId) ? 'checked' : '';
                        @endphp
                        <input name="finishing_id[]" value="r" class="form-check-input" type="checkbox" {{ $isFinRekanan }}>
                        <label class="form-check-label"><span>Rekanan</span></label>
                    </div>
                </div>
            </div>
        </div>
        @else
        -
        @endif
        <hr>

        <div class="row ml-1">
            <h5 class="font-weight-bold">Pilih QC : </h5>
        </div>
        @if($qualitys != null)
        <div class="row">
            @foreach($qualitys as $qc)
            <div class="col-md-3">
                <div class="form-group">
                    <div class="form-check">
                        @php
                            $qualityId = \App\Models\DataKerja::where('barang_id', $barang->id)->pluck('qc_id')->first();
                            $qualityId = explode(',', $qualityId);
                            if($qualityId != null){
                                $isCheckedQC = is_array($qualityId) && in_array($qc->id, $qualityId);
                            } else {
                                $isCheckedQC = false;
                            }
                        @endphp
                        <input name="qc_id[]" value="{{ $qc->id }}" class="form-check-input" type="checkbox" {{ $isCheckedQC ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $qc->name }}</label>
                    </div>
                </div>
            </div>  
            @endforeach
        </div>
        @else
        -
        @endif
        <hr>

        {{-- Memilih tempat pengerjaan di Surabaya, Kediri, Malang --}}
        <div class="mb-3">
            {{-- Pilih Tempat Pengerjaan Menggunakan Checkbox --}}
            <div class="row ml-1">
                <h6 class="font-weight-bold">Tempat Pengerjaan :</h6>
            </div>
            <div class="row">
            @foreach($tempatCabang as $cabang => $value)
                <div class="col-md-3">
                    <div class="form-check">
                        @php
                            $cabangId = \App\Models\DataKerja::where('barang_id', $barang->id)->pluck('cabang_id')->first();
                            $cabangId = explode(',', $cabangId);
                            if($cabangId != null){
                                $isCheckedCabang = is_array($cabangId) && in_array($cabang, $cabangId) ? 'checked' : '';
                            } else {
                                $isCheckedCabang = false;
                            }
                        @endphp
                        <input id="cabang{{ $barang->id }}" name="cabang_id[]" value="{{ $cabang }}" class="form-check-input" type="checkbox" {{ $isCheckedCabang }}>
                        <label class="form-check-label" id="ca">{{ $value }}</label>
                    </div>
                </div>
            @endforeach
            </div>
        </div>

        {{-- Memilih jenis mesin berdasarkan tempat --}}
        <div class="mb-3">
            <div class="form-group">
                <label>Jenis Mesin :</label>
                <select id="cariMesin{{ $barang->id }}" class="custom-select rounded-1 select2" multiple="multiple" name="jenisMesin[]" style="width: 100%">

                </select>
                @if($antrian->cabang_id != null)
                    <p class="text-sm text-danger font-italic mt-1">*Jika tidak ada perubahan, <strong>biarkan kosong.</strong></p>
                @endif
            </div>
        </div>

        <div class="mb-3">
            {{-- Masukkan start job --}}
            @php
                $mulai = \App\Models\DataKerja::where('barang_id', $barang->id)->pluck('tgl_mulai')->first();
            @endphp
            <label for="start_job" class="form-label">Mulai<span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control" id="start_job" aria-describedby="start_job" name="start_job" value="{{ $mulai }}" required>
        </div>
        <div class="mb-3">
            @php
                $selesai = \App\Models\DataKerja::where('barang_id', $barang->id)->pluck('tgl_selesai')->first();
            @endphp
            {{-- Masukkan Deadline --}}
            <label for="deadline" class="form-label">Deadline<span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control" id="deadline" aria-describedby="deadline" name="end_job" value="{{ $selesai }}" required>
        </div>
        <div class="mb-3">
            {{-- Masukkan Keterangan --}}
            @php
                $note = \App\Models\DataKerja::where('barang_id', $barang->id)->pluck('admin_note')->first();
            @endphp
            <label for="keterangan" class="form-label">Catatan Admin <span class="text-muted font-italic text-sm">(Opsional)</span></label>
            <textarea class="form-control" id="keterangan" rows="3" name="admin_note">{{ $note }}</textarea>
        </div>

        <input type="hidden" name="isEdited" value="{{ $isEdited }}">
        <div class="d-flex align-items-center">
            <input id="submitEdit{{ $barang->id }}" type="submit" class="btn btn-primary" value="Submit"><span id="loader{{ $barang->id }}" class="loader m-2" style="display: none"></span>
        </div>
    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>
@endsection

@section('script')
<script src="https://kit.fontawesome.com/a49a4a7eca.js" crossorigin="anonymous"></script>
<script>
    function penugasanOtomatis(id) {
        let tiketAntrian = "{{ $antrian->ticket_order }}"

        $.ajax({
            url: "{{ route('workshop.penugasanOtomatis') }}",
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                "id": id,
                "tiket": tiketAntrian
            },
            success: function(response) {
                if(response.status == 'success') {
                    alert('Penugasan otomatis berhasil');
                    location.reload();
                } else {
                    alert('Penugasan otomatis gagal');
                }
            }
        });
    }

    $(document).ready(function() {

        // Tabel Barang Datatables
        $('#tabelBarang').DataTable({
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

        $('.select2').select2({
            placeholder: "Pilih Mesin",
            allowClear: true,
            ajax: {
                url: '{{ route("mesin.search") }}',
                dataType: 'json',
                delay: 250, 
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                }
            }
        });

        @foreach($barangs as $barang)
        $('#formEditAntrian{{ $barang->id }}').on('submit', function(e) {
            e.preventDefault(); // Mencegah form dari submit default
            $(this).find('input[type="submit"]').prop('disabled', true); // Nonaktifkan tombol submit
            $('#loader{{ $barang->id }}').show(); // Tampilkan loader

            // Ambil data dari form
            var formData = $(this).serialize();

            // Kirim data menggunakan AJAX
            $.ajax({
                url: $(this).attr('action'), // URL tujuan dari form action
                type: 'POST', // Metode pengiriman, bisa juga 'GET' tergantung kebutuhan
                data: formData, // Data yang akan dikirim
                success: function(response) {
                    $(document).Toasts('create', {
                        title: 'Notifikasi',
                        autohide: true,
                        delay: 1500,
                        class: 'bg-success',
                        body: 'Penugasan {{ $barang->job->job_name }} berhasil diperbarui !'
                    });
                },
                error: function(xhr, status, error) {
                    $(document).Toasts('create', {
                        title: 'Notifikasi',
                        autohide: true,
                        delay: 1500,
                        class: 'bg-danger',
                        body: 'Penugasan {{ $barang->job->job_name }} gagal diperbarui !'
                    });
                },
                complete: function() {
                    // Fungsi yang akan dijalankan setelah request selesai (baik berhasil maupun gagal)
                    $('#formEditAntrian{{ $barang->id }}').find('input[type="submit"]').prop('disabled', false); // Aktifkan kembali tombol submit
                    $('#loader{{ $barang->id }}').hide(); // Sembunyikan loader
                }
            });
        });

        $.ajax({
            type: 'GET',
            url: '/design/get-machine-by-idbarang/' + '{{ $barang->id }}',
            success: function(data){
                data.forEach(function(mesin){
                    var option = new Option(mesin.nama, mesin.mid, true, true);
                    $('#cariMesin{{ $barang->id }}').append(option).trigger('change');

                    $('#cariMesin{{ $barang->id }}').trigger({
                        type: 'select2:select',
                        params: {
                            data: data
                        }
                    });
                });
            }
        });
        @endforeach
    });
</script>
@endsection
