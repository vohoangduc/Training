<?php

namespace App\Interfaces\AdminInterface;

interface UserInterface
{
    public function getUsers();
    public function createUser(array $attributes);
}
