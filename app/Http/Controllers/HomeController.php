<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah;
use App\Models\PegawaiAccountOffice;
use App\Models\SuratPeringatan;
use App\Models\Cabang;
use App\Models\Wilayah;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
  
    public function index()
    {
        $title = "Home";
        return view('index', compact('title'));
    }

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

        return view('dashboard', compact('title', 'nasabahs', 'suratPeringatans', 'cabangs', 'wilayahs', 'accountOfficers', 'currentUser'));
    }

    public function search(Request $request)
{
    $search = $request->input('search');
    $nasabahs = Nasabah::where('nama', 'like', '%' . $search . '%')
                      ->orWhere('id_cabang', 'like', '%' . $search . '%')
                      ->orWhere('id_wilayah', 'like', '%' . $search . '%')
                      ->get();

    $output = '';
    foreach ($nasabahs as $nasabah) {
        $progresSp = SuratPeringatan::where('no', $nasabah->no)->first();
        $output .= '
            <tr>
                <td>' . $nasabah->no . '</td>
                <td>' . $nasabah->nama . '</td>
                <td>' . $nasabah->total . '</td>
                <td>' . $nasabah->keterangan . '</td>
                <td>' . ($progresSp ? $progresSp->tingkat : 'N/A') . '</td>
                <td>
                    <button class="btn btn-primary btn-sm edit-btn" data-no="' . $nasabah->no . '" data-toggle="modal" data-target="#editModal">Edit</button>
                    <button class="btn btn-info btn-sm detail-btn" data-no="' . $nasabah->no . '" data-toggle="modal" data-target="#detailModal">Detail</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-no="' . $nasabah->no . '" data-toggle="modal" data-target="#deleteModal">Delete</button>
                </td>
            </tr>
        ';
    }

    return response($output);
}


    public function editNasabah($no)
    {
        $nasabah = Nasabah::find($no);
        return response()->json($nasabah);
    }

    public function updateNasabah(Request $request, $no)
    {
        Log::info('Menerima permintaan update untuk nasabah', [
            'no' => $no,
            'request_data' => $request->all()
        ]);

        $request->validate([
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

        $nasabah = Nasabah::find($no);
        if (!$nasabah) {
            Log::warning('Nasabah tidak ditemukan', ['no' => $no]);
            return redirect()->back()->with('error', 'Nasabah tidak ditemukan');
        }

        $nasabah->update($request->all());

        Log::info('Data nasabah berhasil diperbarui', [
            'no' => $no,
            'updated_data' => $nasabah
        ]);

        return redirect('dashboard')->with('success', 'Data berhasil diperbarui');
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
