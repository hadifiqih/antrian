<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBahan extends Model
{
    use HasFactory;

    protected $table = 'stok_bahan';

    protected $fillable = [
        'product_id',
        'cabang_id',
        'jumlah_stok',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'product_id', 'id');
    }
}
