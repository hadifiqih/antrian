<?php

namespace App\Models;

use App\Models\Pengiriman;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'ticket_order',
        'metode_pembayaran',
        'biaya_packing',
        'biaya_pasang',
        'diskon',
        'total_harga',
        'dibayarkan',
        'status_pembayaran',
        'nominal_pelunasan',
        'file_peluasan',
        'tanggal_pelunasan',
        'status_pembayaran'
    ];

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class);
    }
}
