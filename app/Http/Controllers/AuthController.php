<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                "name" => "required|string|min:4",
                "email" => "required|string|unique:users,email",
                "password" => "required|string|min:8",
            ]);

            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" =>  Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'validation error',
                'message' => $e->getMessage(),
                'data' => null,
            ], status: 400);
        } catch (Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null,
            ], status: 500);
        }
    }
    //public function login (Request $request)
    public function login(Request $request)
{
    try {
        $request->validate([
            "email" => "required|string",
            "password" => "required|string",
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['email atau password yang anda masukan salah.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User logged in successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 200);
    } catch (ValidationException $e) {
        return response()->json([
            'status' => 'validation error',
            'message' => $e->getMessage(),
            'data' => null,
        ], status: 401);
    } catch (Throwable $th) {
        return response()->json([
            'status' => 'error',
            'message' => $th->getMessage(),
            'data' => null,
        ], status: 500);
    }
}
    // {
    //     // validasi data
    //     /// ngecek user dah ada or blom
    //     /// proceed login -> generate token
    // }

    public function logout(Request $request)
    {
        // get authenticeted user
        // delete token
    }
}
