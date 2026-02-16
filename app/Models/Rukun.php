<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rukun extends Model
{
    protected $table = 'rukun';

    protected $fillable = [
        'type',
        'no',
    ];
    
    public function rtFamilies()
    {
        return $this->hasMany(Family::class, 'rt_id');
    }
    
    public function rwFamilies()
    {
        return $this->hasMany(Family::class, 'rw_id');
    }


    // protected $casts = [ 
    //     'type' => 'string',
    // ];
}
