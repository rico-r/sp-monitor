<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Nasabah;
use App\Models\PegawaiAccountOffice;
use App\Models\SuratPeringatan;
use App\Models\Cabang;
use App\Models\Jabatan;
use App\Models\Status;
use App\Models\Wilayah;
use App\Models\User;

class SuperAdminController extends Controller
{
    
    public function dashboard(Request $request)
    {
        Log::info('Memasuki fungsi dashboard');
    
        $title = "Dashboard";
    
        // Retrieve account officers with jabatan_id = 5
        $accountOfficers = User::where('jabatan_id', 5)->get();
    
        // Dapatkan ID admin kas yang sedang login
        $currentUser = auth()->user();
        $adminKasId = $currentUser->id;
    
        // Memulai query dengan relasi yang diperlukan
        $query = User::with('jabatan', 'cabang', 'wilayah','infostatus')
            ->where('id', $adminKasId); // Filter berdasarkan id pengguna yang login
    
        Log::info('Query awal: ', ['query' => $query->toSql()]);
    
        // Filter berdasarkan tanggal (jika diperlukan, sesuaikan dengan kolom yang relevan di tabel users)
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
    
        Log::info('Query setelah filter tanggal: ', ['query' => $query->toSql()]);
    
        // Filter berdasarkan pencarian (sesuaikan kolom yang akan dicari)
        $search = $request->input('search');
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('cabang', function ($q) use ($search) {
                    $q->where('nama_cabang', 'like', "%{$search}%");
                })
                ->orWhereHas('wilayah', function ($q) use ($search) {
                    $q->where('nama_wilayah', 'like', "%{$search}%");
                });
        }
    
        // Filter berdasarkan cabang
        $cabangFilter = $request->input('cabang_filter');
        if ($cabangFilter) {
            $query->whereHas('cabang', function ($q) use ($cabangFilter) {
                $q->where('id_cabang', $cabangFilter);
            });
        }
    
        // Filter berdasarkan wilayah
        $wilayahFilter = $request->input('wilayah_filter');
        if ($wilayahFilter) {
            $query->whereHas('wilayah', function ($q) use ($wilayahFilter) {
                $q->where('id_wilayah', $wilayahFilter);
            });
        }
    
        Log::info('Query setelah filter cabang dan wilayah: ', ['query' => $query->toSql()]);
    
        $users = $query->get(); // Mengambil data dari tabel users

        $cabangs = Cabang::all();
        $wilayahs = Wilayah::all();
        $jabatans = Jabatan::all();
        $statuses = Status::all(); 

    // Mengambil semua data user (tanpa filter)
        $allUsers = User::all();     
        return view('super-admin.dashboard', compact('title', 'accountOfficers','statuses', 'jabatans', 'users','allUsers', 'cabangs', 'wilayahs', 'currentUser')); 
    }
    public function edit($id)
    {
        $user = User::with('jabatan', 'cabang', 'wilayah', 'status')->findOrFail($id);
        $jabatans = Jabatan::all();
        $cabangs = Cabang::all();
        $wilayahs = Wilayah::all();
        $statuses = Status::all(); // Pastikan Anda memiliki model Status

        return view('super-admin.user.edit', compact('user', 'jabatans', 'cabangs', 'wilayahs', 'statuses')); // Sesuaikan dengan nama view Anda
    }

    public function update(Request $request, $id)
    {
        // Validasi data input jika diperlukan
        $request->validate([
            'name' => 'required',
            'jabatan_id' => 'required',
            'id_cabang' => 'required',
            'id_wilayah' => 'required',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->all());

        return redirect()->route('super-admin.dashboard')->with('success', 'Data pengguna berhasil diperbarui!');
    }
}
