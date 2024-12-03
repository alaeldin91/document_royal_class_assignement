<?php
  
 namespace App\Repositories;

 interface AuthRepository
 {
       function register(array $data);
       function login(array $data);
 }