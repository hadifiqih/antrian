<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Biaya Produksi</title>
</head>
<body>
<table>
    <tbody>
        <tr></tr>
        <tr>
            <td></td>
            <td colspan="2" style="text-align: center;">Sales</td>
            <td colspan="3" style="text-align: center; font-weight: bold;">{{ $barang->user->sales->sales_name }}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" style="text-align: center;">Nama Produk</td>
            <td colspan="3" style="text-align: center; font-weight: bold;">{{ $barang->job->job_name }}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" style="text-align: center;">Harga Jual</td>
            <td style="text-align: center; font-weight: bold;">{{ $barang->price }}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" style="text-align: center;">Qty</td>
            <td colspan="3" style="text-align: center; font-weight: bold;">{{ $barang->qty }}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" style="text-align: center;">Total Omset</td>
            <td colspan="3" style="text-align: center; font-weight: bold;">{{ $barang->price * $barang->qty }}</td>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <td></td>
            <th style="font-weight: bold; text-align:center;">No</th>
            <th style="font-weight: bold; text-align:center;">Nama Bahan</th>
            <th style="font-weight: bold; text-align:center;">Qty</th>
            <th style="font-weight: bold; text-align:center;">Harga</th>
            <th style="font-weight: bold; text-align:center;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bahan as $key => $b)
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">{{ $key + 1 }}</td>
            <td>{{ $b->nama_bahan }}</td>
            <td style="text-align: center;">{{ $b->qty }}</td>
            <td style="text-align: center;">{{ $b->harga }}</td>
            <td style="text-align: center;">{{ $b->qty * $b->harga }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th colspan="4" style="text-align: center;">Total</th>
            @php
            $totalProduksi = 0;
            foreach($bahan as $b){
                $totalProduksi += $b->qty * $b->harga;
            }
            @endphp
            <th style="text-align: center;">{{ $totalProduksi }}</th>
        </tr>
    </tfoot>
</table>

<table>
    <thead>
        <tr>
            <th></th>
            <th colspan="5" style="text-align: center;">Biaya Tambahan Lainnya</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td style="font-weight: bold; text-align:center;">No</td>
            <td style="font-weight: bold; text-align:center;">Nama Biaya</td>
            <td style="font-weight: bold; text-align:center;">Persentase</td>
            <td colspan="2" style="font-weight: bold; text-align:center;">Nominal</td>
        </tr>
        @foreach($biayaLainnya as $key => $b)
        <tr>
            <td></td>
            <td style="text-align: center;">{{ $key + 1 }}</td>
            <td >{{ $b->nama_biaya }}</td>
            <td style="text-align: center;">{{ $b->persentase }}%</td>
            @if($barang->kategori_id == 3)
            <td colspan="2" style="text-align: center;">{{ ($b->persentase / 100) * $barang->price }}</td>
            @else
            <td colspan="2" style="text-align: center;">0</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th colspan="3" style="text-align: center;">Total Biaya Lainnya</th>
            @php
            $totalBiaya = 0;
            if($barang->kategori_id == 3){
                foreach($biayaLainnya as $b){
                    $totalBiaya += ($b->persentase / 100) * $barang->price;
                }
            }else{
                $totalBiaya = 0;
            }
            @endphp
            <th colspan="2" style="text-align: center;">{{ $totalBiaya }}</th>
        </tr>
    </tfoot>
</table>

{{-- Total Biaya Bahan + Biaya Tambahan Lainnya --}}
@php
$total = $totalProduksi + $totalBiaya;
@endphp
<table>
    <thead>
        <tr>
            <th></th>
            <th colspan="3" style="text-align: center;">Total Biaya Produksi</th>
            <th colspan="2" style="text-align: center;">{{ $total }}</th>
        </tr>
    </thead>
</table>
</body>
</html>