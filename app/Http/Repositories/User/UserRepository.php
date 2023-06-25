<?php

namespace App\Http\Repositories\User; 

use Illuminate\Support\Facades\Hash;
use App\Models\User; 

class UserRepository implements UserRepositoryInterface 
{
    public function createUser(array $data) {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json($user);
    }
}

