<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    protected $table = 'keranjang';

    public function items()
    {
        return $this->hasMany(KeranjangItem::class, 'keranjang_id', 'id');
    }

    public static function getKeranjang()
    {
        return Keranjang::all();
    }

    public static function getItemByIdCustomer($customer_id)
    {
        return static::where('customer_id', $customer_id)->get();
    }

}
