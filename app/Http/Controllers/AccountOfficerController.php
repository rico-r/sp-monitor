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

    $suratPeringatans = SuratPeringatan::select('id_peringatan', 'no', 'tingkat', 'tanggal', 'bukti_gambar', 'scan_pdf')
        ->latest('tanggal')
        ->get();
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

public function deleteNasabah($id_peringatan)
{
    SuratPeringatan::find($id_peringatan)->delete();
    return redirect()->route('account-officer.dashboard')->with('success', 'Data berhasil di hapus');
}

public function detailNasabah($id_peringatan)
{
    $nasabah = SuratPeringatan::find($id_peringatan);
    return response()->json($nasabah);
}

public function addNasabah(Request $request)
{
    Log::info('Add Nasabah request received', $request->all());

    $request->validate([
        'nama' => 'required',
        'tingkat' => 'required',
        'bukti_gambar' => 'required|image|max:2048',
    ]);

    try {
        $nasabahData = $request->only(['nama', 'tingkat', 'tanggal']);

        // Handle the image upload for 'bukti_gambar'
        if ($request->hasFile('bukti_gambar')) {
            $buktiGambarPath = $request->file('bukti_gambar')->store('bukti_gambar', 'public');
            $nasabahData['bukti_gambar'] = $buktiGambarPath;
        }

        // Retrieve the Nasabah by name
        $nasabah = Nasabah::where('nama', $nasabahData['nama'])->first();

        if (!$nasabah) {
            return redirect()->back()->with('error', 'Nasabah not found.');
        }

        // Check if the Surat Peringatan already exists
        $suratPeringatan = SuratPeringatan::where('no', $nasabah->no)
            ->where('tingkat', $nasabahData['tingkat'])
            ->first();

        if (!$suratPeringatan) {
            return redirect()->back()->with('error', "Surat Peringatan with Tingkat {$nasabahData['tingkat']} not found for this Nasabah.");
        }

        $accountOfficerId = auth()->user()->id;

        // Update the existing Surat Peringatan
        $suratPeringatan = SuratPeringatan::where('no', $nasabahData['no'])
            ->where('tingkat', $nasabahData['tingkat'])
            ->where('id_account_officer', $accountOfficerId)
            ->whereNull('bukti_gambar')
            ->firstOrFail();

        $suratPeringatan->update([
            'tanggal' => $nasabahData['tanggal'],
            'bukti_gambar' => $nasabahData['bukti_gambar'],
            'id_account_officer' => $accountOfficerId,
        ]);

        Log::info('Nasabah updated successfully by Account Officer', $nasabahData);

        return redirect()->route('account-officer.dashboard')->with('success', 'Data berhasil diperbarui');
    } catch (\Exception $e) {
        Log::error('Error updating Nasabah: ' . $e->getMessage(), [
            'request' => $request->all(),
            'exception' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Failed to update data');
    }
}
    public function showAddNasabahForm()
    {
        $loggedInAccountOfficerId = auth()->user()->id;

        $nasabahWithPendingSuratPeringatan = SuratPeringatan::whereNull('bukti_gambar')
            ->where('id_account_officer', $loggedInAccountOfficerId)
            ->join('nasabah', 'surat_peringatan.no', '=', 'nasabah.no')
            ->select('nasabah.no', 'nasabah.nama', 'surat_peringatan.tingkat')
            ->get()
            ->groupBy('nama')
            ->map(function ($group) {
                return $group->pluck('tingkat', 'no')->toArray();
            });

        return view('account_officer.dashboard', compact('nasabahSurat'));
    }

}
