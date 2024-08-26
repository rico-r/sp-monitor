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
use App\Models\KantorKas;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function dashboard(Request $request)
{
    Log::info('Memasuki fungsi dashboard');

    $title = "Dashboard";
    Log::info('Title set: ' . $title);

    // Retrieve account officers with jabatan_id = 5
    $accountOfficers = User::where('jabatan_id', 5)->get();
    Log::info('Account Officers retrieved: ', ['count' => $accountOfficers->count()]);

    // Dapatkan ID admin kas yang sedang login
    $currentUser = auth()->user();
    $adminKasId = $currentUser->id;
    Log::info('Current User ID: ', ['adminKasId' => $adminKasId]);

    // Memulai query dengan relasi yang diperlukan dan filter berdasarkan ID admin kas
    $query = User::with('jabatan', 'cabang', 'kantorkas','infostatus')
        ->where('id', $adminKasId); 

    Log::info('Query awal: ', ['query' => $query->toSql()]);

    // Filter berdasarkan tanggal (jika diperlukan)
    if ($request->has('date_filter')) {
        $dateFilter = $request->input('date_filter');
        Log::info('Filter tanggal diterapkan', ['date_filter' => $dateFilter]);

        switch ($dateFilter) {
            case 'last_7_days':
                $query->where('created_at', '>=', now()->subDays(7));
                Log::info('Filter last 7 days diterapkan');
                break;
            case 'last_30_days':
                $query->where('created_at', '>=', now()->subDays(30));
                Log::info('Filter last 30 days diterapkan');
                break;
            case 'last_month':
                $query->whereMonth('created_at', '=', now()->subMonth()->month);
                Log::info('Filter last month diterapkan');
                break;
            case 'last_year':
                $query->whereYear('created_at', '=', now()->subYear()->year);
                Log::info('Filter last year diterapkan');
                break;
        }
    }

    Log::info('Query setelah filter tanggal: ', ['query' => $query->toSql()]);

    // Filter berdasarkan pencarian
    $search = $request->input('search');
    if ($search) {
        Log::info('Pencarian diterapkan', ['search' => $search]);
        $query->where('name', 'like', "%{$search}%")
            ->orWhereHas('cabang', function ($q) use ($search) {
                $q->where('nama_cabang', 'like', "%{$search}%");
            })
            ->orWhereHas('kantorkas', function ($q) use ($search) {
                $q->where('nama_kantorkas', 'like', "%{$search}%"); // Perbaikan: Gunakan nama_kantorkas
            });
    }

    // Filter berdasarkan cabang
    $cabangFilter = $request->input('cabang_filter');
    if ($cabangFilter) {
        Log::info('Filter cabang diterapkan', ['cabang_filter' => $cabangFilter]);
        $query->whereHas('cabang', function ($q) use ($cabangFilter) {
            $q->where('id_cabang', $cabangFilter);
        });
    }

    // Filter berdasarkan kantorkas (perbaikan)
    $wilayahFilter = $request->input('wilayah_filter');
    if ($wilayahFilter) {
        Log::info('Filter kantorkas diterapkan', ['wilayah_filter' => $wilayahFilter]);
        $query->whereHas('kantorkas', function ($q) use ($wilayahFilter) {
            $q->where('id_kantorkas', $wilayahFilter); // Perbaikan: Gunakan id_cabang
        });
    }

    Log::info('Query setelah filter cabang dan kantorkas: ', ['query' => $query->toSql()]);

    $users = $query->get(); 
    Log::info('Users retrieved: ', ['count' => $users->count()]);

    $cabangs = Cabang::all();
    Log::info('Cabangs retrieved: ', ['count' => $cabangs->count()]);
    $kantorkas = KantorKas::all();
    Log::info('Wilayahs retrieved: ', ['count' => $kantorkas->count()]);
    $jabatans = Jabatan::all();
    Log::info('Jabatans retrieved: ', ['count' => $jabatans->count()]);
    $statuses = Status::all(); 
    Log::info('Statuses retrieved: ', ['count' => $statuses->count()]);

    // Mengambil semua data user (tanpa filter)
    $allUsers = User::all(); 
    Log::info('All users retrieved: ', ['count' => $allUsers->count()]);

    return view('super-admin.dashboard', compact('title', 'accountOfficers','statuses', 'jabatans', 'users','allUsers', 'cabangs', 'kantorkas', 'currentUser')); 
}

public function tampilkanCabang()
{
    $title = 'Cabang Admin';
    $cabangs = Cabang::all(); 
    $kantorkas = KantorKas::all();
    return view('super-admin.cabang', compact('cabangs','kantorkas','title'));
}

public function addCabang(Request $request)
{
    Log::info('Add Cabang request received', $request->all());

    $request->validate([
        'nama_cabang' => 'required|max:255', // Validasi hanya untuk nama_cabang
    ]);

    try {
        // Buat array baru hanya dengan data yang diperlukan
        $cabangData = [
            'nama_cabang' => $request->input('nama_cabang')
        ];

        Cabang::create($cabangData); // Insert data ke tabel cabang

        Log::info('Cabang added successfully', $cabangData);

        return redirect()->route('super-admin.cabang')->with('success', 'Cabang berhasil ditambahkan');
    } catch (\Exception $e) {
        Log::error('Error adding Cabang: ' . $e->getMessage(), [
            'request' => $request->all(),
            'exception' => $e->getTraceAsString()
        ]);

        return response()->json(['error' => 'Failed to add cabang']); // Pesan error yang lebih spesifik
    }
}

public function tampilkanKantorKas()
{
    $cabangs = Cabang::all();
    $title = 'Admin Kantorkas';
    $kantorkas = KantorKas::all(); 
    return view('super-admin.kantorkas', compact('kantorkas','title','cabangs'));
}

    public function edit($id)
    {
        Log::info('Memasuki fungsi edit', ['user_id' => $id]);

        $user = User::with('jabatan', 'cabang', 'kantorkas','infostatus')->findOrFail($id);
        Log::info('User found for editing: ', ['user' => $user]);
        // $allUsers = User::all();  

        $jabatans = Jabatan::all();
        Log::info('Jabatans retrieved: ', ['count' => $jabatans->count()]);
        $cabangs = Cabang::all();
        Log::info('Cabangs retrieved: ', ['count' => $cabangs->count()]);
        $kantorkas = KantorKas::all();
        Log::info('Wilayahs retrieved: ', ['count' => $kantorkas->count()]);
        $statuses = Status::all(); 
        Log::info('Statuses retrieved: ', ['count' => $statuses->count()]);

        // return view('super-admin.dashboard', compact('allUsers','user', 'jabatans', 'cabangs', 'kantorkas', 'statuses'));
        return response()->json($user);
    }

    public function addKantorkas(Request $request)
{
    Log::info('Add Kantorkas request received', $request->all());

    $request->validate([
        'nama_kantorkas' => 'required|max:255', // Validasi hanya untuk nama_cabang
    ]);

    try {
        // Buat array baru hanya dengan data yang diperlukan
        $kantorkasData = [
            'nama_kantorkas' => $request->input('nama_kantorkas')
        ];

        KantorKas::create($kantorkasData); // Insert data ke tabel cabang

        Log::info('Kantorkas added successfully', $kantorkasData);

        return redirect()->route('super-admin.kantorkas')->with('success', 'Kantorkas berhasil ditambahkan');
    } catch (\Exception $e) {
        Log::error('Error adding Cabang: ' . $e->getMessage(), [
            'request' => $request->all(),
            'exception' => $e->getTraceAsString()
        ]);

        return response()->json(['error' => 'Failed to add kantorkas']); // Pesan error yang lebih spesifik
    }
}

public function deleteKantorkas($id_kantorkas)
{
    Kantorkas::find($id_kantorkas)->delete();
    return redirect()->route('super-admin.kantorkas')->with('success', 'Data berhasil di hapus');
}



    public function update(Request $request, $id)
    {
        Log::info('Memasuki fungsi update', ['user_id' => $id]);

        // Validasi data input jika diperlukan
        $validatedData = $request->validate([
            'name' => 'required',
            'jabatan_id' => 'required',
            'id_cabang' => 'required',
            'id_wilayah' => 'required',
        ]);
        Log::info('Data tervalidasi: ', ['validated_data' => $validatedData]);

        $user = User::findOrFail($id);
        Log::info('User found for updating: ', ['user' => $user]);

        $user->update($request->all());
        Log::info('User updated successfully', ['user' => $user]);

        return redirect()->route('super-admin.dashboard')->with('success', 'Data pengguna berhasil diperbarui!');
    }
}