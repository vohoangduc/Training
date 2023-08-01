<?php

namespace App\Repositories\AdminRepository;

use App\Interfaces\AdminInterface\UserInterface;
use App\Models\User;
use Illuminate\Http\Request;

class UserRepository implements UserInterface
{

    /**
     * Get all users 
     * 
     * @return mixed
     */
    public function getUsers()
    {
        return User::orderBy('id', 'desc')->get();
    }

    /**
     * Create user 
     *
     * @param array $attributes
     * @return mixed
     */
    public function createUser(array $attributes)
    {
        return User::create($attributes);
    }

        /**
     * Get user by id 
     *
     * @param int
     * @return mixed
     */
    public function getUserById($idUser)
    {
        return User::findOrFail($idUser);
    }

    /**
     * Create user 
     *
     * @param int $idUser
     * @return mixed
     */
    public function deleteUser($idUser)
    {
        return User::destroy($idUser);
    }
}
