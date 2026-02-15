<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{

    protected $fillable = [
        'email',
        'password',
        'jabatan_id'
    ];

    protected $hidden = [
        'password',
    ];
    
    public function isActive()
    {
        if ($this->jabatan->slug === 'administrator') {
            return true;
        }
    
        return $this->detail && $this->detail->status === 'active';
    }
    
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }

    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    
    public function getJWTCustomClaims(): array
    {
        return [];
    }

}
