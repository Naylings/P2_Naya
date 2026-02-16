<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rukun;
use App\Models\Family;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RukunController extends Controller
{

    public function index() {
        $rukun = Rukun::orderByRaw("FIELD(type, 'RT', 'RW')")
                        ->orderBy('no','asc')
                        ->get();

        return response()->json([
        'message' => 'Rukun retrieved successfully',
        'data' => $rukun
        ]);

    }

    public function store(Request $request) {
        
        $validatedData = $request->validate([
            'type' => 'required|in:RT,RW',
            'no'   => 'required|numeric|max_digits:3',
        ]);
    
        // Format menjadi 3 digit
        $formattedNo = str_pad($validatedData['no'], 3, '0', STR_PAD_LEFT);
    
        // Cek kombinasi unik
        $exists = Rukun::where('type', $validatedData['type'])
                        ->where('no', $formattedNo)
                        ->exists();
    
        if ($exists) {
            return response()->json([
                'message' => $validatedData['type'] . ' no ' . $formattedNo . ' already exist'
            ], 422);
        }
    
        $rukun = Rukun::create([
            'type' => $validatedData['type'],
            'no'   => $formattedNo,
        ]);
    
        return response()->json([
            'message' => $validatedData['type'] . ' no ' . $formattedNo . ' created successfully',
            'data' => $rukun
        ], 201);
        
    }

    
    public function destroy($id)
    {
        $rukun = Rukun::findOrFail($id);
        
        // Cek apakah sudah digunakan di table family
        $isUsed = Family::where('rt_id', $id)
                    ->orWhere('rw_id', $id)
                    ->exists();
        
        if ($isUsed) {
            return response()->json([
                'message' => $rukun->type . ' no ' . $rukun->no . ' cannot be deleted because it is used in family data'
            ], 422);
        }
        
        $type = $rukun->type;
        $no   = $rukun->no;
    
        $rukun->delete();
    
        return response()->json([
            'message' => "{$type} no {$no} deleted successfully"
        ]);
    }
}
