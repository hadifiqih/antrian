@extends('layouts.app')

@section('title', 'Antrian | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Antrian')

@section('breadcrumb', 'Antrian Stempel')

@section('content')

<div class="container">
    {{-- Filter berdasarkan jenis produk --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <h5 class="text-secondary">Jenis Produk</h5>
            <form action="{{ route('documentation.gallery') }}" method="GET">
                <select class="form-control select2" {{ $selectedProduk == '' ? '' : 'disabled' }} id="filterJenisProduk" name="produk" style="width=100%">
                    <option value="0">Semua</option> @foreach($jenisProduk as $jp) <option value="{{ $jp->id }}" {{ $jp->id == $selectedProduk ? 'disabled selected' : '' }}>{{ $jp->job_name }}</option> @endforeach
                </select> 
                @if($selectedProduk == '') 
                    <button type="submit" class="btn btn-sm btn-primary mt-2">Filter</button> 
                @else 
                    <a href="{{ route('documentation.gallery') }}" class="btn btn-sm btn-danger mt-2">Reset</a> 
                @endif
            </form>
        </div>
    </div>
    <!-- Card 1 --> 
    @if(count($barang) == 0) 
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning" role="alert"> Dokumentasi tidak ditemukan. </div>
        </div>
    </div>
    @else
    <div class="row">
    @foreach($barang as $b)
    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card">
            <img 
                data-sizes="auto" 
                data-srcset="
                {{ isset($b->documentation->filename) ? asset('storage/dokumentasi/'. $b->documentation->filename.'?w=300') : asset('adminlte/dist/img/placeholder.svg') }} 300w,
                {{ isset($b->documentation->filename) ? asset('storage/dokumentasi/'. $b->documentation->filename.'?w=600') : asset('adminlte/dist/img/placeholder.svg') }} 600w,
                {{ isset($b->documentation->filename) ? asset('storage/dokumentasi/'. $b->documentation->filename.'?w=900') : asset('adminlte/dist/img/placeholder.svg') }} 900w" 
                src="{{ asset('adminlte/dist/img/placeholder.svg') }}" 
                data-src="{{ isset($b->documentation->filename) ? asset('storage/dokumentasi/'. $b->documentation->filename) : asset('adminlte/dist/img/placeholder.svg')}}"
                class="card-img-top lazyload" 
                alt="..."
                onerror="this.onerror=null;this.src='{{ asset('adminlte/dist/img/placeholder.svg') }}';">
    
            <div class="card-body">
                <h5 class="card-title">{{ $b->job->job_name }}</h5>
                <p class="card-text text-secondary text-sm">{{ $b->user->sales->sales_name }}</p>
            </div>
            <div class="card-footer">
                <button onclick="modalSpesifikasi({{ $b->id }})" type="button" class="btn btn-sm btn-primary">Lihat Detail</button>
            </div>
        </div>
    </div>    
    <!-- ... other cards ... --> 
    @endforeach
    </div>
    @endif
    <div class="row">
        <div class="mt-3">
            <div class="d-flex justify-content-center">
                {{ $barang->links() }}
            </div>
        </div>
    </div>
</div>
@include('page.dokumentasi.modal.show-detail-spek')
@endsection

@section('script')
<script>
    function formatRupiah(angka){
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
            ribuan = ribuan.join('.').split('').reverse().join('');
        return 'Rp ' + ribuan;
    }

    function unduhGambar() {
        var src = $('#gambarDokum').attr('src');
            var a = document.createElement('a');
            var waktu = Date.now();
            //ambil extensi gambar
            var ext = src.split('.').pop();
            a.href = src;
            a.download = waktu + '.' + ext;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
    }

    function modalSpesifikasi(id) {
        $.ajax({
            url: '/barang/getBarangById/' + id,
            type: 'GET',
            success: function(response) {
                console.log(response);
                $('#modalSpesifikasi').modal('show');
                //reset modal
                $('#modalSpesifikasi .modal-body #gambarDokum').attr('src', '');
                $('#modalSpesifikasi .modal-body #spesifikasi').text('');
                $('#modalSpesifikasi .modal-body #harga').text('');

                $('#modalSpesifikasi .modal-body #gambarDokum').attr('src', '{{ asset("storage/dokumentasi") }}/' + response.barang.documentation.filename);
                $('#modalSpesifikasi .modal-body #spesifikasi').text(response.barang.note);
                $('#modalSpesifikasi .modal-body #harga').text(formatRupiah(response.barang.price));
                $('#modalSpesifikasi .modal-footer #btnUnduhGambar').attr('onclick', 'unduhGambar()');
            }
        });
    }

    $(document).ready(function() {
        $('#filterJenisProduk').select2({
        });
    });
</script>
@endsection