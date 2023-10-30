<?php

namespace App\Service;

use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class UserService
{


    public function create($user)
    {
        try {
            DB::beginTransaction();
            $user =  User::create($user);
            DB::commit();
            return response()->json([
                'message' => 'User created',
                'data' => $user,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            if ($e instanceof QueryException) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'data' => null,
                ], 501);
            }

            return response()->json([
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function login($cradentials)
    {
        if (!$token = auth()->attempt($cradentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expire_in' => auth()->factory()->getTTL() * 60,
            'data' => auth()->user(),
        ], 200);
    }



    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'Successfully logout'
        ]);
    }
}
