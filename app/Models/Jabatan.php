<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{   
    protected $table = 'jabatan';
    
    public function jabatan()
    {
        return $this->hasMany(Jabatan::class);
    }

    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }
}

