<?php

namespace App\Service;

use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
            $user = auth()->user();
            if ($user->image_url != null) {
                Storage::delete($user->image_url);
            }
            $file_name = time() . '.' . $photo->file('picture')->getClientOriginalExtension();
            $path =  $photo->file('picture')->storeAs('public/images', $file_name);
            // $path = $photo->file('picture')->move('./storage/photocustomer', $file_name);
            $user->image_url = $path;
            $user->save();
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
            $user = auth()->user();
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
