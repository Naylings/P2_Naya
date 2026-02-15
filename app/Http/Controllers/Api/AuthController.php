<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);


            if (! $token = auth('api')->attempt($credentials)) {
                   return response()->json([
                       'message' => 'Unauthorized'
                   ], 401);
               }

               $user = auth('api')->user()->load([
                   'jabatan',
                   'detail'
               ]);
               
               if (!$user->isActive()) {
                   auth('api')->logout();
                   return response()->json(['message' => 'User is inactive'], 403);
               }


               return response()->json([
                      'token' => $token,
                      'user' => $user
                  ]);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Logged out']);
    }

    public function me()
    {
        $user = auth('api')->user()->load([
        'jabatan:id,name,slug',
        'detail'
        ]);

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,

            // role dari jabatan
            'role' => $user->jabatan->slug,
            'jabatan' => $user->jabatan->name,

            // detail tambahan
            'detail' => $user->detail,

        ]);
    }
}
