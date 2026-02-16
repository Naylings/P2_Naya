<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{   
    
    protected $table = 'family';
    
    public function rt()
    {
        return $this->belongsTo(Rukun::class, 'rt_id');
    }
    
    public function rw()
    {
        return $this->belongsTo(Rukun::class, 'rw_id');
    }

    
    
}

