<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Status;
use App\Models\Direksi;
use App\Models\Jabatan;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use App\Models\PegawaiAdminKas;
use App\Models\PegawaiSupervisor;
use App\Models\PegawaiKepalaCabang;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class MobileAdminController extends Controller
{
    public function getAllData()
    {
        $jabatan = Jabatan::all();
        $cabang = Cabang::all();
        $wilayah = Wilayah::all();
        $direksi = Direksi::all();
        $kepalaCabang = PegawaiKepalaCabang::all();
        $supervisor = PegawaiSupervisor::all();
        $adminKas = PegawaiAdminKas::all();
        $status = Status::all(); // Assuming you have a Status model for infostatus

        return response()->json([
            'jabatan' => $jabatan,
            'cabang' => $cabang,
            'wilayah' => $wilayah,
            'direksi' => $direksi,
            'kepala_cabang' => $kepalaCabang,
            'supervisor' => $supervisor,
            'admin_kas' => $adminKas,
            'status' => $status
        ]);
    }

    public function getUserAdmin(Request $request)
    {
        Log::info('Request received for getUsers', ['request' => $request->all()]);
        
        $perPage = 15; // Jumlah pengguna per halaman

        // Eager load semua relasi yang diperlukan
        $query = User::with([
            'jabatan',
            'infostatus',
            'cabang',
            'wilayah',
            // 'direksi:id_direksi,nama',
            // 'pegawaiKepalaCabang.cabang:id_cabang,nama_cabang',
            // 'pegawaiSupervisor.cabang:id_cabang,nama_cabang',
            // 'pegawaiAdminKas.cabang:id_cabang,nama_cabang',
            // 'pegawaiAccountOfficer.cabang:id_cabang,nama_cabang',
            // 'pegawaiSupervisor.wilayah:id_wilayah,nama_wilayah',
            // 'pegawaiAdminKas.wilayah:id_wilayah,nama_wilayah',
            // 'pegawaiAccountOfficer.wilayah:id_wilayah,nama_wilayah'
        ]);

        if ($request->has('search')) {
            $search = $request->search;
            Log::info('Search parameter provided', ['search' => $search]);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')

                    ->orWhereHas('cabang', function($q) use ($search) {
                        $q->where('nama_cabang', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('jabatan', function($q) use ($search) {
                        $q->where('nama_jabatan', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('wilayah', function($q) use ($search) {
                        $q->where('nama_wilayah', 'LIKE', '%' . $search . '%');
                    });
            });
        }

        Log::info('Executing query to fetch users');
        $users = $query->paginate($perPage);

        Log::info('Transforming user data to include all relations');
        
        $users->getCollection()->transform(function($user) {
            Log::info('Transforming user', ['user' => $user]);
            Log::info('User status', ['status' => $user->status]);
            $cabang = $user->cabang ? $user->cabang : null;
                    // ($user->pegawaiSupervisor ? $user->pegawaiSupervisor->cabang :
                    // ($user->pegawaiAdminKas ? $user->pegawaiAdminKas->cabang :
                    // ($user->pegawaiAccountOfficer ? $user->pegawaiAccountOfficer->cabang : null)));

            $wilayah = 
                 
                    $user->wilayah ? $user->wilayah : null;
                    // ($user->pegawaiAdminKas ? $user->pegawaiAdminKas->wilayah :
                    // ($user->pegawaiAccountOfficer ? $user->pegawaiAccountOfficer->wilayah : null));

            // $direksi = $user->pegawaiKepalaCabang ? $user->pegawaiKepalaCabang->direksi :null;
            // $kepalaCabang = $user->pegawaiSupervisor ? $user->pegawaiSupervisor->kepalaCabang :null;
            // $superVisor = $user->pegawaiAdminKas ? $user->pegawaiAdminKas->supervisor :null;
            // $adminKas = $user->pegawaiAccountOfficer ? $user->pegawaiAccountOfficer->adminKas :null;
            // $status = $user->status ? $user->status : null;
            // $statuus = $user->infostatus ? $user->infostatus->infostatus :null;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'jabatan' => $user->jabatan ? $user->jabatan->nama_jabatan : null,
                'id_jabatan' => $user->jabatan ? $user->jabatan->id_jabatan : null,
                'cabang' => $cabang ? $cabang->nama_cabang : null,
                'id_cabang' => $cabang ? $cabang->id_cabang : null,
                'wilayah' => $wilayah ? $wilayah->nama_wilayah : null,
                'id_wilayah' => $wilayah ? $wilayah->id_wilayah : null,
                // 'id_direksi' => $direksi ? $direksi->nama : null,
                // 'direksi_id' => $direksi ? $direksi->id_direksi : null,
                // 'id_kepala_cabang' => $kepalaCabang ? $kepalaCabang->nama_kepala_cabang : null,
                // 'kepalacabang_id' => $kepalaCabang ? $kepalaCabang->id_kepala_cabang : null,
                // 'id_supervisor' => $superVisor ? $superVisor->nama_supervisor : null,
                // 'supervisor_id' => $superVisor ? $superVisor->id_supervisor : null,
                // 'id_admin_kas' => $adminKas ? $adminKas->nama_admin_kas : null,
                // 'adminkas_id' => $adminKas ? $adminKas->id_admin_kas : null,
                'status' => $user->infostatus ? $user->infostatus->nama_status : null,
                'status_id' => $user->infostatus ? $user->infostatus->id : null,

            ];
        });

        Log::info('Users fetched successfully', ['users' => $users->toArray()]);
        return response()->json($users->toArray());
    }
    
    public function updateUser(Request $request, $id)
    {
    Log::info('Update user request received', ['id' => $id, 'request_data' => $request->all()]);

        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string',
                'jabatan' => 'required|integer',
                'cabang' => 'nullable|integer',
                'wilayah' => 'nullable|integer',
                // 'id_direksi' => 'nullable|integer',
                // 'id_kepala_cabang' => 'nullable|integer',
                // 'id_supervisor' => 'nullable|integer',
                // 'id_admin_kas' => 'nullable|integer',
                'status' => 'nullable|integer',
            ]);

            Log::info('Input validated', ['validated_data' => $validated]);
        } catch (ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        // Cari pengguna berdasarkan ID
        $user = User::find($id);

        if (!$user) {
            Log::warning('User not found', ['id' => $id]);
            return response()->json(['message' => 'User not found'], 404);
        }

        Log::info('User found', ['user' => $user]);

    

        // Update tabel berdasarkan jabatan
        switch (strtolower($validated['jabatan'])) {
            case '1':
                Log::info('Updating Direksi table', ['user_id' => $id]);
                // $direksi = Direksi::find($id);
                if ($user) {
                    $user->name = $validated['name'];
                    $user->email = $validated['email'];
                    $user->jabatan_id = $validated['jabatan'];
                    $user->status = $validated['status'];

                    // $user->cabang = $validated['cabang'];
                    $user->save();
                    Log::info('Direksi data updated successfully', ['direksi' => $user]);
                } else {
                    Log::warning('Direksi not found', ['id' => $id]);
                }
                break;

            case '2':
                Log::info('Updating Kepala Cabang table', ['user_id' => $id]);
                // $kepalaCabang = PegawaiKepalaCabang::find($id);
                if ($user) {
                    $user->name = $validated['name'];
                    $user->email = $validated['email'];
                    $user->jabatan_id = $validated['jabatan'];

                    $user->id_cabang = $validated['cabang'];
                    $user->status = $validated['status'];

                    $user->save();
                    Log::info('Kepala Cabang data updated successfully', ['kepalaCabang' => $user]);
                } else {
                    Log::warning('Kepala Cabang not found', ['id' => $id]);
                }
                break;

            case '3':
                Log::info('Updating Supervisor table', ['user_id' => $id]);
                // $supervisor = PegawaiSupervisor::find($id);
                if ($user) {
                    $user->name = $validated['name'];
                    $user->email = $validated['email'];
                    $user->jabatan_id = $validated['jabatan'];

                    $user->id_cabang = $validated['cabang'];
                    $user->id_wilayah = $validated['wilayah'];
                    $user->status = $validated['status'];

                    $user->save();
                    Log::info('Supervisor data updated successfully', ['supervisor' => $user]);
                } else {
                    Log::warning('Supervisor not found', ['id' => $id]);
                }
                break;

            case '4':
                Log::info('Updating Admin Kas table', ['user_id' => $id]);
                // $adminKas = PegawaiAdminKas::find($id);
                if ($user) {
                    $user->name = $validated['name'];
                    $user->email = $validated['email'];
                    $user->jabatan_id = $validated['jabatan'];

                    $user->id_cabang = $validated['cabang'];
                    $user->id_wilayah = $validated['wilayah'];
                    $user->status = $validated['status'];

                    $user->save();
                    Log::info('Admin Kas data updated successfully', ['adminKas' => $user]);
                } else {
                    Log::warning('Admin Kas not found', ['id' => $id]);
                }
                break;

            case '5':
                Log::info('Updating Account Officer table', ['user_id' => $id]);
                // $accountOfficer = PegawaiAccountOffice::find($id);
                if ($user) {
                    $user->name = $validated['name'];
                    $user->email = $validated['email'];
                    $user->jabatan_id = $validated['jabatan'];

                    $user->id_cabang = $validated['cabang'];
                    $user->id_wilayah = $validated['wilayah'];
                    // $accountOfficer->id_admin_kas = $validated['id_admin_kas'];
                    $user->status = $validated['status'];
                    // $accountOfficer->save();
                    $user->save();
                    Log::info('Account Officer data updated successfully', ['accountOfficer' => $user]);
                    Log::info('user data updated successfully', ['user' => $user]);
                } else {
                    Log::warning('Account Officer not found', ['id' => $id]);
                }
                break;

            default:
                Log::error('Invalid jabatan provided', ['jabatan' => $validated['jabatan']]);
                return response()->json(['message' => 'Invalid jabatan'], 400);
        }

        Log::info('User update process completed', ['user_id' => $id]);

        return response()->json(['message' => 'User updated successfully'], 200);
    }
}
