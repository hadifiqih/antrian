<?php

namespace App\Models;

use App\Models\Cabang;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MutasiStok extends Model
{
    use HasFactory;

    protected $table = 'mutasi_stok';

    protected $fillable = [
        'produk_id',
        'cabang_id',
        'kategori_mutasi',
        'jenis_mutasi',
        'jumlah_stok',
        'keterangan'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }
}
