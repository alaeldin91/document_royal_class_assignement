<?php 

namespace App\Services;

use App\Repositories\AuthRepositoryImpl;
use Illuminate\Http\Request;

class AuthServiceImpl implements AuthService 
{

    private AuthRepositoryImpl $authRepository;
   
    public function __construct(AuthRepositoryImpl $authRepository) {
      
        $this->authRepository = $authRepository;
    }
   
    //function Login User
    public function login(Request $request)
    {
       $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        return response()->json([$this->authRepository->login($data)]);

    }

    //function register New  User
    public function register(Request $request)
    {
        $data = $request->validate([
            'user_name' => 'required|string|max:255',
            'email' => 'required|string|email',
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        return response()->json([$this->authRepository->register($data)],201);

    }
}

