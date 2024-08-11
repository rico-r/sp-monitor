<?php

namespace App\Http\Controllers;

use App\Models\Key;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class MobileRegisterController extends Controller
{
    public function checkConnection()
    {
        return response()->json(['message' => 'Server connection is OK!'], 200);
    }
    public function register(Request $request)
    {
        // Validasi input dengan pesan dalam bahasa Indonesia
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'key' => 'required|integer',
        ], [
            'required' => 'Kolom :attribute wajib diisi',
            'email' => 'Format :attribute tidak valid',
            'unique' => ':attribute sudah digunakan',
            'min' => 'Minimal :attribute karakter 8 karakter',
            'integer' => 'Kolom :attribute harus berupa angka',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        // Cek apakah key ada di tabel keys
        $keyRecord = Key::where('key', $request->key)->first();
        if (!$keyRecord) {
            return response()->json(['message' => 'Key tidak valid.'], 422);
        }
    
        // Cek apakah key sudah digunakan di tabel users
        $keyUsed = User::where('key', $request->key)->exists();
        if ($keyUsed) {
            return response()->json(['message' => 'Key sudah digunakan.'], 422);
        }
    
        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'jabatan_id' => $keyRecord->jabatan, // Ambil jabatan dari tabel keys
            'key' => $request->key, // Simpan key
        ]);
    
        // Respon dengan user yang baru dibuat
        return response()->json(['message' => 'User berhasil terdaftar', 'user' => $user], 201);
    }
}
