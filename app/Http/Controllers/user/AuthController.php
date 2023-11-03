<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\user\AuthLoginUserRequest;
use App\Http\Requests\user\AuthRegisterUserRequest;
use App\Service\UserService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected UserService $service)
    {
    }

    public function login(AuthLoginUserRequest $req)
    {
        $data = $req->only(['email', 'password']);
        return $this->service->login($data);
    }

    function register(AuthRegisterUserRequest $req)
    {
        $data = $req->only(['username', 'password', 'email', 'name', 'image_url', 'roles', 'gender']);

        return $this->service->create($data);
    }

    function logout(Request $req)
    {
        return $this->service->logout();
    }
}
