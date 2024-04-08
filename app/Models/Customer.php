<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    public static function antrian()
    {
        return $this->hasMany(Antrian::class);
    }

    public static function order()
    {
        return $this->hasMany(Order::class);
    }

    public static function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public static function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    public static function documentation()
    {
        return $this->hasMany(Documentation::class);
    }

    public static function getCustomer($id)
    {
        return $this->where('id', $id)->first();
    }
}
