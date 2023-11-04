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
        try {
            if (!$token = auth()->attempt($cradentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expire_in' => auth()->factory()->getTTL() * 60,
                'data' => auth()->user(),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function getUser()
    {
        try {
            return response()->json([
                'data' => auth()->user(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function uploadPicture($photo)
    {
        try {
            $path = time() . '.' . $photo->file('picture')->getClientOriginalExtension();
            $photo->file('picture')->storeAs('public/images', $path);
            return response()->json([
                'message' => 'Upload Image Success',
                "path" => $path,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function updateProfile($data)
    {
        try {
            $id = auth()->user()->id;
            $user = User::find($id);
            $user->username = $data["username"];
            $user->name = $data["name"];
            $user->password = $data["password"];
            $user->gender = $data["gender"];
            $user->save();
            return response()->json([
                "message" => "Data success update",
                "data" => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }


    public function logout()
    {
        try {
            auth()->logout();
            return response()->json([
                'message' => 'Successfully logout'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
