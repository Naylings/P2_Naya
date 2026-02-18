<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentLog extends Model
{
    protected $table = 'document_log';
    
    public $timestamps = false; 

    protected $fillable = [
        'detail',
        'doc_type',
        'local_file',
    ];

    protected $casts = [
        'detail'     => 'array',   
        'created_at' => 'datetime',
    ];
}