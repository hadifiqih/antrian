<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 0px auto;
            padding: 0px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

    </style>
</head>
<table>
    <tbody>
        <tr>
            <td colspan="2">Nama Produk</td>
            <td>:</td>
            <td>{{ $barang->job->job_name }}</td>
        </tr>
        <tr>
            <td colspan="2">Harga Jual</td>
            <td>:</td>
            <td>Rp{{ number_format($barang->price,0,',','.') }}</td>
        </tr>
        <tr>
            <td colspan="2">Qty</td>
            <td>:</td>
            <td>{{ $barang->qty }}</td>
        </tr>
        <tr>
            <td colspan="2">Total Omset</td>
            <td>:</td>
            <td>Rp{{ number_format($barang->price * $barang->qty,0,',','.') }}</td>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Bahan</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bahan as $key => $b)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $b->nama_bahan }}</td>
            <td>{{ $b->qty }}</td>
            <td>{{ $b->harga }}</td>
            <td>{{ $b->qty * $b->harga }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Total</th>
            @php
            $totalProduksi = 0;
            foreach($bahan as $b){
                $totalProduksi += $b->qty * $b->harga;
            }
            @endphp
            <th>Rp{{ number_format($totalProduksi,0,',','.') }}</th>
        </tr>
    </tfoot>
</table>

<p>Biaya Tambahan Lainnya</p>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Biaya</th>
            <th>Persentase</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        @if($barang->kategori_id == 3)
        @foreach($biayaLainnya as $key => $b)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $b->nama_biaya }}</td>
            <td>{{ $b->persentase }}</td>
            <td>{{ $b->nominal }}</td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="4">Tidak ada biaya tambahan lainnya</td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total</th>
            @php
            $totalBiaya = 0;
            if($barang->kategori_id == 3){
                foreach($biaya as $b){
                    $totalBiaya += $b->nominal;
                }
            }else{
                $totalBiaya = 0;
            }
            @endphp
            <th>Rp{{ number_format($totalBiaya,0,',','.') }}</th>
        </tr>
    </tfoot>
</table>

{{-- Total Biaya Bahan + Biaya Tambahan Lainnya --}}
@php
$total = $totalProduksi + $totalBiaya;
@endphp
<p>Total Biaya Produksi</p>
<table>
    <thead>
        <tr>
            <th>Total Biaya Produksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Rp{{ number_format($total,0,',','.') }}</td>
        </tr>
    </tbody>
</table>