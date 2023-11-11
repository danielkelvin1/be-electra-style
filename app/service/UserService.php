<?php

namespace App\Service;

use App\Models\Adress;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $user = User::with(['address'])->find(auth()->user()->id);
            return response()->json([
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null,
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

    public function uploadPicture($photo)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if (isset($user->image_url)) {
                Storage::delete($user->image_url);
            }
            $file_name = time() . Str::uuid() . '.' . $photo->file('picture')->getClientOriginalExtension();
            $path =  $photo->file('picture')->storeAs('public/images', $file_name);
            $user->image_url = $path;
            $user->save();
            DB::commit();
            return response()->json([
                'message' => 'Upload Image Success',
                "path" => $path,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            if (isset($user->image_url)) {
                Storage::delete($user->image_url);
            }
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function addAddress($data)
    {
        try {
            DB::beginTransaction();
            $userId = auth()->user()->id;
            $provinceId = $data['province_id'];
            $cityId = $data['city_id'];
            $completeAddress = $data['complete_address'];
            $address = Adress::create([
                'province_id' => $provinceId,
                'city_id' => $cityId,
                'complete_address' => $completeAddress,
                'user_id' => $userId,
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Address created',
                'data' => $address
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function updateAddress($data, $addressId)
    {
        try {
            DB::beginTransaction();
            $userId = auth()->user()->id;
            $provinceId = $data['province_id'];
            $cityId = $data['city_id'];
            $completeAddress = $data['complete_address'];
            Adress::where('id', $addressId)
                ->where('user_id', $userId)
                ->update([
                    'province_id' => $provinceId,
                    'city_id' => $cityId,
                    'complete_address' => $completeAddress,
                ]);
            DB::commit();
            return response()->json([
                'message' => "Address update success",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function deleteAddress($addressId)
    {
        try {
            DB::beginTransaction();
            $userId = auth()->user()->id;
            $address = Adress::where('id', $addressId)
                ->where('user_id', $userId)->firstOrFail();
            $address->delete();
            DB::commit();
            return response()->json([
                'message' => 'delete success'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    "message" => "Data not found",
                    "data" => null
                ], 404);
            }

            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
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
