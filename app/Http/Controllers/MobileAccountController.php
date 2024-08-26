<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MobileAccountController extends Controller
{
    public function getUserDetails(Request $request)
    {
        $user = $request->user(); // Mendapatkan user yang sedang login
        $jabatan = $user->jabatan;

        // Log informasi awal
        Log::info('Fetching user details for user: ' . $user->id);
        Log::info('Fetching jabatan details for user: ' . $jabatan);

        // Eager load relasi yang diperlukan berdasarkan jabatan
        switch ($jabatan->id_jabatan) {
            case 2:
                $user->load('cabang');
                break;
            case 3:
            case 4:
            case 5:
                $user->load('cabang', 'kantorkas');
                break;
        }

        Log::info('User loaded with relations: ' . $user);

        $userDetails = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'jabatan' => $jabatan->nama_jabatan // Menampilkan nama jabatan
        ];

        if (in_array($jabatan->id_jabatan, [2, 3, 4, 5])) {
            $cabang = $user->cabang;
            $kantorkas = $user->kantorkas;

            Log::info($jabatan->nama_jabatan . ': ' . $user);
            Log::info('Cabang: ' . $cabang);
            Log::info('Kantor Kas: ' . $kantorkas);

            $userDetails['cabang'] = $cabang ? $cabang->nama_cabang : null;
            if (in_array($jabatan->id_jabatan, [3, 4, 5])) {
                $userDetails['kantorkas'] = $kantorkas ? $kantorkas->nama_kantorkas : null;
            }
        }

        // Log informasi akhir sebelum respons
        Log::info('User details fetched successfully for user: ' . $user->name);

        return response()->json($userDetails);
    }
    public function logout(Request $request)
    {
        $request->user()->forceFill([
            'api_token' => null,
        ])->save();
        return response()->json([
            'message' => 'success'
        ]);
    }
}