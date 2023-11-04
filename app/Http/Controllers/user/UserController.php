<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\user\UserUpdateRequest;
use App\Http\Requests\user\UserUploadImageRequest;
use App\Service\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    ) {
    }

    function getUser()
    {
        return $this->service->getUser();
    }

    function uploadPicutre(UserUploadImageRequest $req)
    {
        return $this->service->uploadPicture($req);
    }

    function updateProfile(UserUpdateRequest $req)
    {
        $data = $req->only(['username', 'name', 'password', 'gender']);
        return $this->service->updateProfile($data);
    }
}
