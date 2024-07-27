<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\PegawaiAccountOffice;
use App\Models\SuratPeringatan;
use App\Models\Cabang;
use App\Models\Wilayah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $title = "Home";
        return view('index', compact('title'));
    }

    public function dashboard()
    {
        $title = "Dashboard";
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
        return view('dashboard', compact('title', 'nasabahs', 'suratPeringatans', 'cabangs', 'wilayahs', 'accountOfficers', 'currentUser'));
    }

    public function editNasabah($no)
    {
        $nasabah = Nasabah::find($no);
        return response()->json($nasabah);
    }

    public function updateNasabah(Request $request, $no)
    {
        $request->validate([
            'nama' => 'required|max:255',
            'total' => 'required|numeric',
            'keterangan' => 'required',
        ]);

        $nasabah = Nasabah::find($no);
        $nasabah->nama = $request->nama;
        $nasabah->total = $request->total;
        $nasabah->keterangan = $request->keterangan;
        $nasabah->save();

        return response()->json(['success' => 'Data updated successfully']);
    }

    public function deleteNasabah($no)
    {
        Nasabah::find($no)->delete();
        return redirect('dashboard');
    }

    public function detailNasabah($no)
    {
        $nasabah = Nasabah::find($no);
        return response()->json($nasabah);
    }

    public function addNasabah(Request $request)
{
    // dd($request->all());
    Log::info('Add Nasabah request received', $request->all());

    $request->validate([
        'no' => 'required',
        'nama' => 'required|max:255',
        'pokok' => 'required|numeric',
        'bunga' => 'required|numeric',
        'denda' => 'required|numeric',
        'keterangan' => 'required',
        'ttd' => 'required|date',
        'kembali' => 'required|date',
        'id_cabang' => 'required|exists:cabangs,id_cabang',
        'id_wilayah' => 'required|exists:wilayahs,id_wilayah',
        'id_admin_kas' => 'required',
        'id_account_officer' => 'required',
    ]);

    try {
        $nasabah = new Nasabah;
        $nasabah->no = $request->no;
        $nasabah->nama = $request->nama;
        $nasabah->pokok = $request->pokok;
        $nasabah->bunga = $request->bunga;
        $nasabah->denda = $request->denda;
        $nasabah->total = $request->pokok + $request->bunga + $request->denda;
        $nasabah->keterangan = $request->keterangan;
        $nasabah->ttd = $request->ttd;
        $nasabah->kembali = $request->kembali;
        $nasabah->id_cabang = $request->id_cabang;
        $nasabah->id_wilayah = $request->id_wilayah;
        $nasabah->id_admin_kas = $request->id_admin_kas;
        $nasabah->id_account_officer = $request->id_account_officer;
        $nasabah->save();

        Log::info('Nasabah added successfully', $nasabah->toArray());

        return redirect('dashboard');
        } catch (\Exception $e) {
        Log::error('Error adding Nasabah: ' . $e->getMessage(), [
            'request' => $request->all(),
            'exception' => $e->getTraceAsString()
        ]);

        return response()->json(['error' => 'Failed to add data']);
        }
    }
}
