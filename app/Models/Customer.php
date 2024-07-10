<?php

namespace App\Models;

use App\Models\SumberPelanggan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
        'infoPelanggan',
        'instansi',
        'frekuensi_order',
        'count_followUp',
        'sales_id',
        'provinsi',
        'kota',
    ];

    public function antrian()
    {
        return $this->hasMany(DataAntrian::class);
    }

    public function sumberPelanggan()
    {
        return $this->belongsTo(SumberPelanggan::class, 'infoPelanggan', 'id');
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    public function documentation()
    {
        return $this->hasMany(Documentation::class);
    }

    public function getCustomer($id)
    {
        return $this->where('id', $id)->first();
    }
}
