<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthServiceImpl;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthServiceImpl $authServiceImpl;

    public function __construct(AuthServiceImpl $authServiceImpl) {
   
        $this->authServiceImpl = $authServiceImpl;
    }
   
    public function register(Request $request)
    {
     
        return $this->authServiceImpl->register($request);
    }

    public function login(Request $request){

        return $this->authServiceImpl->login($request);
    }
}
