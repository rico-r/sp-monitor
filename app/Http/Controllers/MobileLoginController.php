<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MobileLoginController extends Controller
{
    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     // Check if the user exists
    //     $user = User::where('email', $request->email)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'User tidak terdaftar.',
    //         ], 404); // Not Found status code
    //     }

    //     if ($user->status != '1') {
    //         return response()->json([
    //             'message' => 'Akun belum aktif, hubungi admin.',
    //         ], 403); // Forbidden status code
    //     }

    //     // Attempt to authenticate the user
    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();

    //         // Generate a token (you may want to use a stronger hashing method)
    //         $token = md5(time() . '.' . md5($request->email));

    //         // Save the token to the user's api_token field
    //         $user->forceFill([
    //             'api_token' => $token,
    //         ])->save();

    //         // Return the token in the JSON response
    //         return response()->json([
    //             'user_id' => $user->id,
    //             'token' => $token,
    //             'jabatan_id' => $user->jabatan_id,
    //             'name' => $user->name
    //         ]);
    //     }

    //     // If authentication fails
    //     return response()->json([
    //         'message' => 'Email atau password yang Anda masukkan salah.',
    //     ], 401); // Unauthorized status code
    // }
    public function login(Request $request)
    {
        // Log the incoming request data
        Log::info('Login attempt', ['email' => $request->email]);
    
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Check if the user exists
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            Log::warning('Login failed: User not found', ['email' => $request->email]);
            return response()->json([
                'message' => 'Email atau password yang Anda masukkan salah.',
            ], 404); // Not Found status code
        }
    
        if ($user->status != '1') {
            Log::warning('Login failed: Account not active', [
                'email' => $request->email,
                'status' => $user->status
            ]);
            return response()->json([
                'message' => 'Akun belum aktif, hubungi admin.',
            ], 403); // Forbidden status code
        }
    
        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
    
            // Generate a token (you may want to use a stronger hashing method)
            $token = md5(time() . '.' . md5($request->email));
    
            // Save the token to the user's api_token field
            $user->forceFill([
                'api_token' => $token,
            ])->save();
    
            Log::info('Login successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'token' => $token
            ]);
    
            // Return the token in the JSON response
            return response()->json([
                'user_id' => $user->id,
                'token' => $token,
                'jabatan_id' => $user->jabatan_id,
                'name' => $user->name
            ]);
        }
    
        // If authentication fails
        Log::warning('Login failed: Invalid credentials', [
            'email' => $request->email,
        ]);
    
        return response()->json([
            'message' => 'Email atau password yang Anda masukkan salah.',
        ], 401); // Unauthorized status code
    }
}