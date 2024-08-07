<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'social_records';

    protected $fillable = [
        'social_account_id',
        'platform',
        'followers',
    ];
}
