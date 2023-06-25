<?php

namespace App\Http\Repositories\User; 

use Illuminate\Http\Response; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User; 

class UserRepository implements UserRepositoryInterface 
{
    public function createUser(array $data) {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $token = $user->createToken('authToken')->plainTextToken; 
        return response()->success(
            'Successfully created ' . $user->role . ' user', 
            [
                'user' => $user,
                'access_token' => $token
            ]
        );
    }
}

