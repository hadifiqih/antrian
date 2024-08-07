<?php

namespace App\Models;

use App\Models\Sales;
use App\Models\SocialRecord;
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
        'update_followers',
    ];

    protected $table = 'social_accounts';

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    public function socialRecord()
    {
        return $this->hasMany(SocialRecord::class, 'social_account_id', 'id');
    }
}
