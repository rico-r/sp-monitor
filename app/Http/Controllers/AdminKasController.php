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
    public function dashboard(Request $request)
{
    Log::info('Memasuki fungsi dashboard');

    $title = "Dashboard";

    // Retrieve account officers with jabatan_id = 5
    $accountOfficers = User::where('jabatan_id', 5)->get();  // Change pluck to get to retrieve the full user objects

    
    $query = Nasabah::with('accountOfficer','adminKas','cabang','wilayah');

    // Log query awal
    Log::info('Query awal: ', ['query' => $query->toSql()]);

    // Filter berdasarkan tanggal
    if ($request->has('date_filter')) {
        $dateFilter = $request->input('date_filter');
        Log::info('Filter tanggal diterapkan', ['date_filter' => $dateFilter]);

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

    // Log query setelah filter tanggal
    Log::info('Query setelah filter tanggal: ', ['query' => $query->toSql()]);

    // Filter berdasarkan pencarian
    if ($request->has('search')) {
        $search = $request->input('search');
        Log::info('Filter pencarian diterapkan', ['search' => $search]);

        $query->where(function($q) use ($search) {
            $q->where('nama', 'like', '%' . $search . '%')
              ->orWhere('branch', 'like', '%' . $search . '%')
              ->orWhere('region', 'like', '%' . $search . '%');
        });
    }

    // Log query setelah filter pencarian
    Log::info('Query setelah filter pencarian: ', ['query' => $query->toSql()]);

    $nasabahs = $query->get();
    Log::info('Nasabahs retrieved', ['nasabahs' => $nasabahs]);

    // Log untuk memeriksa setiap nasabah dan user
    foreach ($nasabahs as $nasabah) {
        if ($nasabah->user) {
            Log::info('Nasabah memiliki user', ['nasabah' => $nasabah->no, 'user' => $nasabah->user->id]);
        } else {
            Log::warning('Nasabah tidak memiliki user', ['nasabah' => $nasabah->no]);
        }
    }

    $suratPeringatans = SuratPeringatan::select('no', 'tingkat')->get();
    $cabangs = Cabang::all();
    $wilayahs = Wilayah::all();
    $currentUser = auth()->user();

    return view('admin-kas.dashboard', compact('title', 'accountOfficers','nasabahs', 'suratPeringatans', 'cabangs', 'wilayahs', 'currentUser'));
}
public function editNasabah($no)
{
    $nasabah = Nasabah::find($no);
    return response()->json($nasabah);
}

public function update(Request $request, $no)
    {
        $request->validate([
            'nama' => 'required',
            'pokok' => 'required|numeric',
            'bunga' => 'required|numeric',
            'denda' => 'required|numeric',
            'keterangan' => 'required',
            'ttd' => 'required|date',
            'kembali' => 'required|date',
            'id_cabang' => 'required|integer',
            'id_wilayah' => 'required|integer',
            'id_account_officer' => 'required|integer'
        ]);

        $nasabah = Nasabah::where('no', $no)->firstOrFail();
        $nasabah->update($request->all());

        return redirect()->route('admin-kas.dashboard')->with('success', 'Data updated successfully');
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
    Log::info('Add Nasabah request received', $request->all());

    $request->validate([
        'no' => 'required|numeric',
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
        Nasabah::create($request->all());
        Log::info('Nasabah added successfully', $request->all());

        return redirect('dashboard')->with('success', 'Data berhasil ditambahkan');
    } catch (\Exception $e) {
        Log::error('Error adding Nasabah: ' . $e->getMessage(), [
            'request' => $request->all(),
            'exception' => $e->getTraceAsString()
        ]);

        return response()->json(['error' => 'Failed to add data']);
    }
}
}
