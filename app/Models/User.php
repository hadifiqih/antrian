<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Cabang;
use App\Models\DesignQueue;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'can_design',
        'phone',
        'status',
        'email_verified_at',
        'can_design',
        'remember_token',
        'beams_token',
        'can_design',
        'design_load',
        'cabang_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdminWorkshop()
    {
        return $this->role_id == 15;
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    public function isSales()
    {
        return $this->role_id == 11 ? true : false;
    }

    public function isDesigner()
    {
        return $this->can_design == 1 || $this->role_id == 16 || $this->role_id == 17;
    }

    public function isSpvDesain()
    {
        return $this->role_id == 5;
    }

    public function isCustomer()
    {
        return $this->role == 'customer';
    }

    public function isProduksi()
    {
        return $this->role_id == 13 || $this->role_id == 17;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function sales()
    {
        return $this->hasOne(Sales::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function designQueue()
    {
        return $this->hasMany(DesignQueue::class, 'designer_id', 'id');
    }

    public function ambilJumlahAntrian($idUser)
    {
        $jumlahAntrian = $this->designQueue()->where('designer_id', $idUser)->where('status', 1)->count();
        return $jumlahAntrian;
    }
}
