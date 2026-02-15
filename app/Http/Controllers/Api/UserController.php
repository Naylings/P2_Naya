<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index() {
        $users = User::with(['jabatan','detail'])->get();

        return response()->json([
        'message' => 'Users retrieved successfully',
        'data' => $users
        ]);

    }

    public function store(Request $request) {
        // dd($request->all()); // lihat semua payload
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'jabatan_id' => 'required|exists:jabatan,id',


            'nip' => 'required|string',
            'name' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'nik' => 'required|string',
            'status' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'jabatan_id' => $validatedData['jabatan_id']
            ]);

            UserDetail::create([
                'user_id' => $user->id,
                'nip' => $validatedData['nip'] ?? null,
                'name' => $validatedData['name'] ?? null,
                'no_hp' => $validatedData['no_hp'] ?? null,
                'address' => $validatedData['address'] ?? null,
                'birth_date' => $validatedData['birth_date'] ?? null,
                'nik' => $validatedData['nik'] ?? null,
                'status' => $validatedData['status'] ?? "inactive",
            ]);


            DB::commit();

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user->load('jabatan', 'detail')
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        $user->load('jabatan', 'detail');
        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user
        ], 200);
    }


    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'jabatan_id' => 'required|exists:jabatan,id',

            'nip' => 'nullable|string',
            'name' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'nik' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $detailData = collect($validatedData)->only([
            'nip',
            'name',
            'no_hp',
            'address',
            'birth_date',
            'nik',
            'status'
        ])->toArray();


        DB::beginTransaction();
        try {
        $user->update([
                'email' => $validatedData['email'] ?? $user->email,
                'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password,
                'jabatan_id' => $validatedData['jabatan_id']?? $user->jabatan_id,
            ]);

            if ($user->detail){
                $user->detail->update($detailData);
            } else {
                UserDetail::create(array_merge(
                    $detailData,
                    ['user_id' => $user->id]
                ));
            }

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user->load('jabatan','detail')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function toggleStatus($id) {
        $user = User::with('detail')->findOrFail($id);

        if (!$user->detail){
            return response()->json([
                'message' => 'User detail not found',
            ], 404);
        }

        $currentStatus = $user->detail->status;

        $newStatus = $currentStatus == 'active' ? 'inactive' : 'active';

        $message = $newStatus == 'active' ? 'User activated successfully' : 'User deactivated successfully';

        $user->detail->update(['status' => $newStatus]);

        return response()->json([
        'message' => $message,
            'data' => $user->load('jabatan','detail')
        ], 200);
    }
}
