<?php

namespace App\Http\Repositories\User;

interface UserRepositoryInterface 
{
    public function createUser(array $data);
    public function authenticateUser(array $data);
}

