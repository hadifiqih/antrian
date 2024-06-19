<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdukHarga extends Model
{
    use HasFactory;

    protected $table = 'produk_harga';

    protected $fillable = [
        'produk_id',
        'cabang_id',
        'harga_kulak',
        'harga_jual',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id');
    }
}
