<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumberPelanggan extends Model
{
    use HasFactory;

    protected $table = 'sumber_pelanggan';

    protected $fillable = [
        'nama_sumber',
        'code_sumber',
    ];

    public function iklan()
    {
        return $this->hasMany(Iklan::class, 'platform', 'code_sumber');
    }

}
