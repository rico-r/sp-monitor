<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticateWithApiToken
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Ambil token dari header X-Authorization
        $token = $request->header('X-Authorization');

        // Hapus prefix 'Bearer ' jika ada
        if ($token) {
            $token = str_replace('Bearer ', '', $token);
        }
        Log::info('token checked:', ['token' => $token]);

        // Temukan pengguna berdasarkan token
        $user = User::where('api_token', $token)->first();

        // Log data request dan roles
        Log::info('Request URL:', ['url' => $request->url()]);
        Log::info('Request Method:', ['method' => $request->method()]);
        Log::info('Roles checked:', ['roles' => $roles]);

        if (!$user) {
            // Log jika tidak ada pengguna yang ditemukan
            Log::warning('Unauthorized access attempt: No user found with provided token.');
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Autentikasi pengguna
        Auth::login($user);

        if (!in_array($user->jabatan->nama_jabatan, $roles)) {
            // Log jika pengguna tidak memiliki hak akses yang sesuai
            Log::warning('Unauthorized access attempt:', [
                'user_id' => $user->id,
                'user_role' => $user->jabatan->nama_jabatan,
                'roles_required' => $roles
            ]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Log jika akses diterima
        Log::info('Access granted:', [
            'user_id' => $user->id,
            'user_role' => $user->jabatan->nama_jabatan
        ]);

        return $next($request);
    }
}