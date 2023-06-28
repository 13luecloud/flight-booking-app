<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Repositories\User\UserRepositoryInterface; 

class UserController extends Controller
{
    private $repository; 
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CreateUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request) 
    {
        return $this->repository->createUser($request->validated());
    }

    public function login(LoginUserRequest $request) 
    {
       return $this->repository->authenticateUser($request->validated());
    }

    public function logout() 
    {
        Auth::user()->currentAccessToken()->delete();
    }
}
