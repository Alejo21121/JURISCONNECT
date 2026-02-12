<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Proceso;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'foto_perfil',
        'password_changed',
        'avatar',
        'role_id',
        'profile_photos'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function procesosAsignados()
    {
        return $this->hasMany(Proceso::class, 'lawyer_id');
    }


    public function lawyer()
    {
        return $this->hasOne(Lawyer::class, 'user_id');
    }

    public function assistant()
    {
        return $this->hasOne(Assistant::class, 'user_id');
    }

}
