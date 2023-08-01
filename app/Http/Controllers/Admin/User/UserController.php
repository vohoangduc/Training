<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\BaseController;
use App\Interfaces\AdminInterface\UserInterface;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    protected $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    function listUser()
    {
        $listUser = $this->userRepository->getUsers();

        return $this->sendSuccessResponse(null, $listUser);
    }

    function createUser(Request $request)
    {
        $data = $request->all();
        $this->userRepository->createUser($data);

        return $this->sendSuccessResponse("create successly", null);
    }

    function updateUser($idUser, Request $request)
    {
        $user = $this->getUserById($idUser);
        $user->update($request->all());

        return $this->sendSuccessResponse("update user successly", null);
    }

    function deleteUser($idUser)
    {
        $this->userRepository->deleteUser($idUser);

        return $this->sendSuccessResponse("delete user successly", null);
    }
}
