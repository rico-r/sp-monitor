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


class AccountOfficerController extends Controller
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

    $suratPeringatans = SuratPeringatan::select('no', 'tingkat', 'tanggal', 'bukti_gambar', 'scan_pdf')
        ->latest('tanggal')
        ->get()
        ->unique('no');
    $cabangs = Cabang::all();
    $wilayahs = Wilayah::all();
    $currentUser = auth()->user();

    return view('account-officer.dashboard', compact('title', 'accountOfficers','nasabahs', 'suratPeringatans', 'cabangs', 'wilayahs', 'currentUser', 'nasabahNames'));
}
public function editNasabah($no)
{
    $nasabah = Nasabah::find($no);
    return response()->json($nasabah);
}

public function update(Request $request, $no)
    {
        $request->validate([
            'nama'=> 'required',
            'tingkat' => 'required|integer|in:1,2,3',
            'tanggal' => 'required|date',
            'bukti_gambar' => 'required',
            'scan_pdf' => 'required'
        ]);

        $nasabah = Nasabah::where('no', $no)->firstOrFail();
        $nasabah->update($request->all());

        return redirect()->route('account-officer.dashboard')->with('success', 'Data berhasil di update');
    }

public function deleteNasabah($no)
{
    Nasabah::find($no)->delete();
    return redirect()->route('account-officer.dashboard')->with('success', 'Data berhasil di hapus');
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
        'nama' => 'required',
        'tingkat' => 'required',
        'tanggal' => 'required|date',
        'bukti_gambar' => 'required|image|max:2048', // Validate image
        'scan_pdf' => 'required|mimes:pdf|max:2048' // Validate PDF
    ]);

    try {
        $nasabahData = $request->only(['nama', 'tingkat', 'tanggal']);

        // Handle the image upload for 'bukti_gambar'
        if ($request->hasFile('bukti_gambar')) {
            $buktiGambarPath = $request->file('bukti_gambar')->store('bukti_gambar', 'public');
            $nasabahData['bukti_gambar'] = $buktiGambarPath;
        }

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

        return redirect()->route('account-officer.dashboard')->with('success', 'Data berhasil ditambahkan');
    } catch (\Exception $e) {
        Log::error('Error adding Nasabah: ' . $e->getMessage(), [
            'request' => $request->all(),
            'exception' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Failed to add data');
    }
}
}
