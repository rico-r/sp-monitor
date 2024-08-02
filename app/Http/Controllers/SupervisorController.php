<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\PegawaiAccountOffice;
use App\Models\SuratPeringatan;
use App\Models\Cabang;
use App\Models\Wilayah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class SupervisorController extends Controller
{
    public function dashboard(Request $request)
    {
        $title = "Dashboard";

        $query = Nasabah::with('accountofficer');

        // Filter berdasarkan tanggal
        if ($request->has('date_filter')) {
            $dateFilter = $request->input('date_filter');
            switch ($dateFilter) {
                case 'last_7_days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case 'last_30_days':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', '=', now()->subMonth()->month);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', '=', now()->subYear()->year);
                    break;
            }
        }

        // Filter berdasarkan pencarian
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('branch', 'like', '%' . $search . '%')
                  ->orWhere('region', 'like', '%' . $search . '%');
            });
        }

        $nasabahs = $query->get();
        $suratPeringatans = SuratPeringatan::select('no', 'tingkat')->get();
        $cabangs = Cabang::all();
        $wilayahs = Wilayah::all();
        $accountOfficers = PegawaiAccountOffice::all();
        $currentUser = auth()->user();

        return view('supervisor.dashboard', compact('title', 'nasabahs', 'suratPeringatans', 'cabangs', 'wilayahs', 'accountOfficers', 'currentUser'));
    }
}
