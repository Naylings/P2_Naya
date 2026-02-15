<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jabatan;

class JabatanController extends Controller
{
    public function index() { 
        $jabatan = Jabatan::all();
        return response()->json([ 'message' => 'Jabatan retrieved successfully', 'data' => $jabatan 
        ], 200);
    }

}
