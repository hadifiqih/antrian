<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeranjangItem extends Model
{
    use HasFactory;

    protected $table = 'keranjang_item';

    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class, 'keranjang_id', 'id');
    }

    public static function getKeranjang()
    {
        return static::all();
    }

    public static function getItemByIdCart($cart_id)
    {
        return static::where('keranjang_id', $cart_id)->get();
    }
}
