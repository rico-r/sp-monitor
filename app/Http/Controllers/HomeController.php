<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
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
        $nasabahs = Nasabah::all();
        $suratPeringatans = SuratPeringatan::select('no', 'tingkat')->get();
        $cabangs = Cabang::all();
        $wilayahs = Wilayah::all();
        $accountOfficers = User::where('jabatan_id', 5)->get();

        return view('dashboard', compact('title', 'nasabahs', 'suratPeringatans', 'cabangs', 'wilayahs', 'accountOfficers'));
    }

    public function editNasabah($id)
    {
        $nasabah = Nasabah::find($id);
        return response()->json($nasabah);
    }

    public function updateNasabah(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:255',
            'total' => 'required|numeric',
            'keterangan' => 'required',
        ]);

        $nasabah = Nasabah::find($id);
        $nasabah->nama = $request->nama;
        $nasabah->total = $request->total;
        $nasabah->keterangan = $request->keterangan;
        $nasabah->save();

        return response()->json(['success' => 'Data updated successfully']);
    }

    public function deleteNasabah($id)
    {
        Nasabah::find($id)->delete();
        return response()->json(['success' => 'Data deleted successfully']);
    }

    public function detailNasabah($id)
    {
        $nasabah = Nasabah::find($id);
        return response()->json($nasabah);
    }

    public function addNasabah(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:255',
            'pokok' => 'required|numeric',
            'bunga' => 'required|numeric',
            'denda' => 'required|numeric',
            'keterangan' => 'required',
            'ttd' => 'required|date',
            'kembali' => 'required|date',
            'id_cabang' => 'required|exists:cabangs,id_cabang',
            'id_wilayah' => 'required|exists:wilayahs,id_wilayah',
            'id_account_officer' => 'required|exists:users,jabatan_id',
        ]);

        $nasabah = new Nasabah;
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
        $nasabah->id_account_officer = $request->id_account_officer;
        $nasabah->save();

        return response()->json(['success' => 'Data added successfully']);
    }
}
