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
    $search = $request->input('search');
    if ($search) {
        $query->where('nama', 'like', "%{$search}%")
              ->orWhereHas('cabang', function ($q) use ($search) {
                  $q->where('nama_cabang', 'like', "%{$search}%");
              })
              ->orWhereHas('wilayah', function ($q) use ($search) {
                  $q->where('nama_wilayah', 'like', "%{$search}%");
              });
    }

    // Filter based on cabang
    $cabangFilter = $request->input('cabang_filter');
    if ($cabangFilter) {
        $query->whereHas('cabang', function ($q) use ($cabangFilter) {
            $q->where('id_cabang', $cabangFilter);
        });
    }

    // Filter based on wilayah
    $wilayahFilter = $request->input('wilayah_filter');
    if ($wilayahFilter) {
        $query->whereHas('wilayah', function ($q) use ($wilayahFilter) {
            $q->where('id_wilayah', $wilayahFilter);
        });
    }

    Log::info('Query setelah filter cabang dan wilayah: ', ['query' => $query->toSql()]);

    $nasabahs = $query->get();
    $nasabahNames = Nasabah::pluck('nama', 'no');

    $suratPeringatans = SuratPeringatan::select('no', 'tingkat')->get()->sortByDesc('tingkat');
    $cabangs = Cabang::all();
    $wilayahs = Wilayah::all();
    $currentUser = auth()->user();

    return view('admin-kas.dashboard', compact('title', 'accountOfficers','nasabahs', 'suratPeringatans', 'cabangs', 'wilayahs', 'currentUser','nasabahNames'));
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

        return redirect()->route('admin-kas.dashboard')->with('success', 'Data berhasil di update');
    }

public function deleteNasabah($no)
{
    Nasabah::find($no)->delete();
    return redirect()->route('admin-kas.dashboard')->with('success', 'Data berhasil di hapus');
}

public function detailNasabah($no)
{
    $nasabah = Nasabah::find($no);
    return response()->json($nasabah);
}

public function addSurat(Request $request)
{
    Log::info('Add Nasabah request received', $request->all());

    $request->validate([
        'nama' => 'required',
        'tingkat' => 'required',
        'scan_pdf' => 'required|mimes:pdf|max:2048'
    ]);

    try {
        $nasabahData = $request->only(['nama', 'tingkat', 'tanggal']);

        // Handle the image upload for 'bukti_gambar'
        // if ($request->hasFile('bukti_gambar')) {
        //     $buktiGambarPath = $request->file('bukti_gambar')->store('bukti_gambar', 'public');
        //     $nasabahData['bukti_gambar'] = $buktiGambarPath;
        // }

        //Handle limit
        $existingEntries = SuratPeringatan::where('no', $nasabahData['nama'])->count();

        if ($existingEntries >= 3) {
            return redirect()->back()->with('error', 'This Nasabah already has the maximum allowed Surat Peringatan entries (3).');
        }

        $duplicateTingkat = SuratPeringatan::where('no', $nasabahData['nama'])
            ->where('tingkat', $nasabahData['tingkat'])
            ->exists();

        if ($duplicateTingkat) {
            return redirect()->back()->with('error', "Surat Peringatan with Tingkat {$nasabahData['tingkat']} already exists for this Nasabah.");
        }

        $nasabahData['bukti_gambar'] = null;

        // Handle the PDF upload for 'scan_pdf'
        if ($request->hasFile('scan_pdf')) {
            $scanPdfPath = $request->file('scan_pdf')->store('scan_pdf', 'public');
            $nasabahData['scan_pdf'] = $scanPdfPath;
        }

        // Retrieve the Nasabah by name
        $nasabah = Nasabah::where('nama', $nasabahData['nama'])->first();

        if (!$nasabah) {
            return redirect()->back()->with('error', 'Nasabah not found.');
        }

        $accountOfficerId = auth()->user()->id;

        // Save the Surat Peringatan
        SuratPeringatan::create([
            'no' => $nasabah->no, // Assuming you have a relationship between Nasabah and SuratPeringatan
            'tingkat' => $nasabahData['tingkat'],
            'tanggal' => $nasabahData['tanggal'],
            'bukti_gambar' => $nasabahData['bukti_gambar'],
            'scan_pdf' => $nasabahData['scan_pdf'],
            'id_account_officer' => $accountOfficerId,
        ]);

        Log::info('Nasabah added successfully', $nasabahData);

        return redirect()->route('admin-kas.dashboard')->with('success', 'Data berhasil ditambahkan');
    } catch (\Exception $e) {
        Log::error('Error adding Nasabah: ' . $e->getMessage(), [
            'request' => $request->all(),
            'exception' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Failed to add data');
    }
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
        'total' => 'required|numeric',
        'keterangan' => 'required',
        'ttd' => 'required|date',
        'kembali' => 'required|date',
        'id_cabang' => 'required|exists:cabangs,id_cabang',
        'id_wilayah' => 'required|exists:wilayahs,id_wilayah',
        'id_account_officer' => 'required',
    ]);

    try {
        $nasabahData = $request->all();
        $nasabahData['id_admin_kas'] = auth()->user()->id;

        Nasabah::create($nasabahData);  // Insert data into the database

        Log::info('Nasabah added successfully', $nasabahData);
        

        return redirect()->route('admin-kas.dashboard')->with('success', 'Data berhasil ditambahkan');
    } catch (\Exception $e) {
        Log::error('Error adding Nasabah: ' . $e->getMessage(), [
            'request' => $request->all(),
            'exception' => $e->getTraceAsString()
        ]);

        return response()->json(['error' => 'Failed to add data']);
    }
}
}
