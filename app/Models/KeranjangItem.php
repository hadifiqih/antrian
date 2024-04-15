<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KeranjangItem extends Model
{
    use HasFactory;

    protected $table = 'keranjang_item';

    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class, 'keranjang_id', 'id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id');
    }

    public static function getKeranjang()
    {
        return static::all();
    }

    public static function getItemByIdCart($cart_id)
    {
        return static::where('keranjang_id', $cart_id)->get();
    }

    public static function getTotalItem($cart_id)
    {
        $items = static::where('keranjang_id', $cart_id)->get();
        $total = 0;
        
        if(count($items) > 1){
            foreach ($items as $item) {
                $total += ($item->harga - $item->diskon) * $item->jumlah;
            }
        }elseif(count($items) == 1){
            $total = ($items[0]->harga - $items[0]->diskon) * $items[0]->jumlah;
        }else{
            $total = 0;
        }
        
        return $total;
    }
}
