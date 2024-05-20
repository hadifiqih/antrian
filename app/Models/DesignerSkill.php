<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DesignerSkill extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'designers_skill';

    protected $fillable = [
        'designer_id',
        'job_id',
    ];

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}
