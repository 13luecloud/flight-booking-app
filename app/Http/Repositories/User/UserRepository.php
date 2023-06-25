<?php

namespace App\Http\Repositories\User; 

use App\Exceptions\InvalidCredentialsException;

use Illuminate\Http\Response; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\User; 

class UserRepository implements UserRepositoryInterface 
{
    public function createUser(array $data) 
    {
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

    public function authenticateUser(array $data) 
    {
        log::info(Auth::attempt($data));
        
        if(!Auth::attempt($data)) {
            throw new InvalidCredentialsException;
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken; 
        return response()->success(
            'Successfully logged in',
            [
                'user' => $user,
                'access_token' => $token
            ]
        );
    }
}

