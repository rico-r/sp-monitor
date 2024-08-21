<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\PegawaiAccountOffice;
use App\Models\SuratPeringatan;
use App\Models\Cabang;
use App\Models\KantorKas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class SupervisorController extends Controller
{

    public function dashboard(Request $request)
{
    Log::info('Memasuki fungsi dashboard');
    $title = "Dashboard";
    $currentUser = auth()->user();
    
    // Retrieve account officers with jabatan_id = 5
    $accountOfficers = User::where('jabatan_id', 5)->get();
    
    // Query nasabah dengan filter berdasarkan id_cabang dari user yang login
    $query = Nasabah::with('accountOfficer','adminKas','cabang','kantorkas')
        ->where('id_cabang', $currentUser->id_cabang)  // Filter based on id_cabang of the logged-in user
        ->where('id_kantorkas', $currentUser->id_kantorkas);

    // Filter berdasarkan no
    if ($request->has('no')) {
        $noFilter = $request->input('no');
        $query->where('no', $noFilter);
    }
    
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
    
    // Filter berdasarkan pencarian
    $search = $request->input('search');
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhereHas('cabang', function ($subQ) use ($search) {
                  $subQ->where('nama_cabang', 'like', "%{$search}%");
              })
              ->orWhereHas('kantorkas', function ($subQ) use ($search) {
                  $subQ->where('nama_wilayah', 'like', "%{$search}%");
              });
        });
    }
    
    // Filter berdasarkan cabang, jika ada filter tambahan
    $cabangFilter = $request->input('cabang_filter');
    if ($cabangFilter) {
        $query->where('id_cabang', $cabangFilter);
    }
    
    // Filter based on kantorkas
    $wilayahFilter = $request->input('wilayah_filter');
    if ($wilayahFilter) {
        $query->where('id_kantorkas', $wilayahFilter);
    }
    
    $nasabahs = $query->get();
    $nasabahNames = Nasabah::pluck('nama', 'no');
    $suratPeringatans = SuratPeringatan::select('surat_peringatans.*', 'nasabahs.nama')
        ->join('nasabahs', 'surat_peringatans.no', '=', 'nasabahs.no')
        ->get()
        ->sortByDesc('tingkat');
    $cabangs = Cabang::all();
    $kantorkas = KantorKas::all();
    
    return view('supervisor.dashboard', compact('title', 'accountOfficers', 'nasabahs', 'suratPeringatans', 'cabangs', 'kantorkas', 'currentUser', 'nasabahNames'));
}

//     public function dashboard(Request $request)
// {
//     Log::info('Memasuki fungsi dashboard');

//     $title = "Dashboard";

//     // Retrieve account officers with jabatan_id = 5
//     $accountOfficers = User::where('jabatan_id', 5)->get();  // Change pluck to get to retrieve the full user objects

    
//     $query = Nasabah::with('accountOfficer','adminKas','cabang','kantorkas');

//     // Log query awal
//     Log::info('Query awal: ', ['query' => $query->toSql()]);

//     // Filter berdasarkan tanggal
//     if ($request->has('date_filter')) {
//         $dateFilter = $request->input('date_filter');
//         Log::info('Filter tanggal diterapkan', ['date_filter' => $dateFilter]);

//         switch ($dateFilter) {
//             case 'last_7_days':
//                 $query->where('created_at', '>=', now()->subDays(7));
//                 break;
//             case 'last_30_days':
//                 $query->where('created_at', '>=', now()->subDays(30));
//                 break;
//             case 'last_month':
//                 $query->whereMonth('created_at', '=', now()->subMonth()->month);
//                 break;
//             case 'last_year':
//                 $query->whereYear('created_at', '=', now()->subYear()->year);
//                 break;
//         }
//     }

//     // Log query setelah filter tanggal
//     Log::info('Query setelah filter tanggal: ', ['query' => $query->toSql()]);

//     // Filter berdasarkan pencarian
//     $search = $request->input('search');
//     if ($search) {
//         $query->where('nama', 'like', "%{$search}%")
//               ->orWhereHas('cabang', function ($q) use ($search) {
//                   $q->where('nama_cabang', 'like', "%{$search}%");
//               })
//               ->orWhereHas('kantorkas', function ($q) use ($search) {
//                   $q->where('nama_wilayah', 'like', "%{$search}%");
//               });
//     }

//     // Filter based on cabang
//     $cabangFilter = $request->input('cabang_filter');
//     if ($cabangFilter) {
//         $query->whereHas('cabang', function ($q) use ($cabangFilter) {
//             $q->where('id_cabang', $cabangFilter);
//         });
//     }

//     // Filter based on kantorkas
//     $wilayahFilter = $request->input('wilayah_filter');
//     if ($wilayahFilter) {
//         $query->whereHas('kantorkas', function ($q) use ($wilayahFilter) {
//             $q->where('id_kantorkas', $wilayahFilter);
//         });
//     }

//     Log::info('Query setelah filter cabang dan kantorkas: ', ['query' => $query->toSql()]);

//     $nasabahs = $query->get();
//     $nasabahNames = Nasabah::pluck('nama', 'no');

//     $suratPeringatans = SuratPeringatan::select('surat_peringatans.*', 'nasabahs.nama')
//     ->join('nasabahs', 'surat_peringatans.no', '=', 'nasabahs.no')
//     ->get()
//     ->sortByDesc('tingkat');
//     $cabangs = Cabang::all();
//     $kantorkas = KantorKas::all();
//     $currentUser = auth()->user();

//     return view('direksi.dashboard', compact('title', 'accountOfficers','nasabahs', 'suratPeringatans', 'cabangs', 'kantorkas', 'currentUser','nasabahNames'));
// }
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
            'id_kantorkas' => 'required|integer',
            'id_account_officer' => 'required|integer'
        ]);

        $nasabah = Nasabah::where('no', $no)->firstOrFail();
        $nasabah->update($request->all());

        return redirect()->route('direksi.dashboard')->with('success', 'Data berhasil di update');
    }

public function deleteNasabah($no)
{
    Nasabah::find($no)->delete();
    return redirect()->route('direksi.dashboard')->with('success', 'Data berhasil di hapus');
}

public function detailNasabah($no)
{
    $nasabah = Nasabah::find($no);
    return response()->json($nasabah);
}

public function addSurat(Request $request)
{
    Log::info('Add Surat request received', $request->all());

    $request->validate([
        'no' => 'required',
        'tingkat' => 'required',
        'scan_pdf' => 'required|mimes:pdf|max:2048'
    ]);

    Log::info('Data passed validation', $request->all());

    try {
        $suratData = $request->only(['no', 'tingkat', 'dibuat','kembali']);

        // Handle limit
        $existingEntries = SuratPeringatan::where('no', $suratData['no'])->count();

        if ($existingEntries >= 3) {
            return redirect()->back()->with('error', 'This Nasabah already has the maximum allowed Surat Peringatan entries (3).');
        }

        $duplicateTingkat = SuratPeringatan::where('no', $suratData['no'])
            ->where('tingkat', $suratData['tingkat'])
            ->exists();

        if ($duplicateTingkat) {
            return redirect()->back()->with('error', "Surat Peringatan with Tingkat {$suratData['tingkat']} already exists for this Nasabah.");
        }

        // Handle the PDF upload for 'scan_pdf'
        if ($request->hasFile('scan_pdf')) {
            $scanPdfPath = $request->file('scan_pdf')->store('scan_pdf', 'public');
            $suratData['scan_pdf'] = $scanPdfPath;

            Log::info('PDF file uploaded successfully', ['path' => $scanPdfPath]);
        }

        $accountOfficerId = auth()->user()->id;

        // Save the Surat Peringatan
        SuratPeringatan::create([
            'no' => $suratData['no'],
            'tingkat' => $suratData['tingkat'],
            'dibuat' => $suratData['dibuat'],
            'kembali' => $suratData['kembali'],
            'scan_pdf' => $suratData['scan_pdf'],
            'id_account_officer' => $accountOfficerId,
        ]);

        Log::info('Surat Peringatan added successfully', $suratData);

        return redirect()->route('direksi.dashboard')->with('success', 'Data berhasil ditambahkan');
    } catch (\Exception $e) {
        Log::error('Error adding Surat Peringatan: ' . $e->getMessage(), [
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
        // 'ttd' => 'required|date',
        // 'kembali' => 'required|date',
        'id_cabang' => 'required|exists:cabangs,id_cabang',
        'id_kantorkas' => 'required|exists:kantorkas,id_kantorkas',
        'id_account_officer' => 'required',
    ]);

    try {
        $nasabahData = $request->all();
        $nasabahData['id_admin_kas'] = auth()->user()->id;

        Nasabah::create($nasabahData);  // Insert data into the database

        Log::info('Nasabah added successfully', $nasabahData);
        

        return redirect()->route('direksi.dashboard')->with('success', 'Data berhasil ditambahkan');
    } catch (\Exception $e) {
        Log::error('Error adding Nasabah: ' . $e->getMessage(), [
            'request' => $request->all(),
            'exception' => $e->getTraceAsString()
        ]);

        return response()->json(['error' => 'Failed to add data']);
    }
}

}
