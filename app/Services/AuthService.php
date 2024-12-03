<?php

 namespace App\Services;

use Illuminate\Http\Request;

 interface AuthService {

    public function login(Request $request);
    public function register(Request $request);

   }