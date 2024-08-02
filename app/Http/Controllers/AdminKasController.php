<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Nasabah;
use App\Models\PegawaiAccountOffice;
use App\Models\SuratPeringatan;
use App\Models\Cabang;
use App\Models\Wilayah;
use App\Models\User;


class AdminKasController extends Controller
{
    public function dashboard()
    {
        $title = "Dashboard Admin Kas";
        $nasabahs = Nasabah::with('accountofficer')->get();
        // $users = User::with('pegawaiAdminKas')->get();
        $suratPeringatans = SuratPeringatan::select('no', 'tingkat')->get();
        $cabangs = Cabang::all();
        $wilayahs = Wilayah::all();
        $accountOfficers = PegawaiAccountOffice::all();
        $currentUser = auth()->user(); 
        // foreach ($users as $user) {
        //     $pegawaiAdminKas = $user->pegawaiAdminKas;
        //     // Lakukan sesuatu dengan $pegawaiAdminKas
        // }
        return view('admin-kas.dashboard', compact('title', 'nasabahs', 'suratPeringatans', 'cabangs', 'wilayahs', 'accountOfficers', 'currentUser'));
    }
}
