<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\password;

class AuthRepositoryImpl implements AuthRepository
{
    // Register function
    public function register(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        $role = Role::where('name', $data['role'])->first();
        $password = trim($data['password']);

        // Update or create a new User
        if ($user) {
            $user->user_name = $data['user_name'];
            $user->email = $data['email'];
            $user->password = Hash::make($password); // Hash the password
            $user->role_id = $role->id;

            // Save the updated user in the database
            $user->save();
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'status' => true,
                'token' => $token,
                'user' => $user,
            ];
        } else {
            // If user doesn't exist, create a new user
            $user = new User();
            $user->user_name = $data['user_name'];
            $user->email = $data['email'];
            $user->password = Hash::make($password); // Hash the password
            $user->role_id = $role->id;

            // Save the new user in the database
            $user->save();
            // Generate a token
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'status' => true,
                'token' => $token,
                'user' => $user,
            ];
        }
    }

    // Login function
    public function login(array $data)
    {
        try {
            // Find the user by email
            $user = User::where('email', $data['email'])->first();
            $password = trim($data['password']);
            // Check if the user exists
            if (!$user) {
                return [
                    'status' => false,
                    'error' => 'Email does not exist.',
                ];
            }

            // Verify the password using Hash::check()
            if (!password_verify($data['password'], $user->password)) {
                // Log the password mismatch for debugging (optional)
                Log::debug("Password mismatch for email: {$data['email']}");
                return [
                    'status' => false,
                    'error' => 'Password mismatch.',
                    'debug' => [
                        'input_password' => $data['password'],
                        'stored_password' => $user->password,
                    ],
                ];
            }

            // Password is correct, generate a new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'status' => true,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ];
        } catch (Exception $e) {
            // Catch any exception and return a generic error
            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
