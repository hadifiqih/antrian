<?php

namespace App\Models;

use App\Models\Sales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sales_id',
        'platform',
        'username',
        'email',
        'phone',
        'password',
    ];

    protected $table = 'social_accounts';

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }
}
