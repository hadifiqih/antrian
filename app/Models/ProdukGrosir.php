<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukGrosir extends Model
{
    use HasFactory;

    protected $table = 'produk_grosir';

    protected $fillable = [
        'produk_id',
        'cabang_id',
        'min_qty',
        'max_qty',
        'harga_grosir',
    ];
}
