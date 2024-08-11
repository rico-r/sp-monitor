<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;
use App\Models\PegawaiAdminKas;
use App\Models\PegawaiSupervisor;
use App\Models\PegawaiKepalaCabang;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PegawaiAccountOffice;
use Illuminate\Support\Facades\Auth;

class MobileMonitoringController extends Controller
{
    public function getNasabah(Request $request)
    {
        Log::info('Request received for getNasabah', ['request' => $request->all()]);
        $user = Auth::user();
        Log::info('Authenticated user', ['user' => $user]);
        $jabatan = $user->jabatan->nama_jabatan;
        Log::info('Jabatan user', ['jabatan' => $jabatan]);

        $perPage = 15; // Jumlah nasabah per halaman

        // Eager load semua relasi yang diperlukan
        $query = Nasabah::with([
            'cabang:id_cabang,nama_cabang',
            'wilayah:id_wilayah,nama_wilayah',
            'adminkas:id,name',
            'accountofficer:id,name',

            // 'adminkas:id_admin_kas,nama_admin_kas',
            // 'accountofficer:id_account_officer,nama_account_officer',
            'suratPeringatan' => function ($query) {
                $query->orderBy('tingkat', 'desc'); // Urutkan surat peringatan berdasarkan tingkat dari yang terbesar
            }
        ]);

        switch ($jabatan) {
            case 'Direksi':
                Log::info('Jabatan: direksi - fetching all nasabahs');
                break;

            case 'Admin':
                Log::info('Jabatan: direksi - fetching all nasabahs');
                break;
            case 'Kepala Cabang':
                // $pegawaiKepalaCabang = PegawaiKepalaCabang::where('id_user', $user->id)->first();
                if ($user) {
                    $idCabang = $user->id_cabang;
                    Log::info('Jabatan: kepala_cabang - fetching nasabahs for cabang', ['id_cabang' => $idCabang]);
                    $query->where('id_cabang', $idCabang);
                } else {
                    Log::error('Cabang not found for Kepala Cabang', ['user_id' => $user->id]);
                    return response()->json(['error' => 'Cabang not found for this Kepala Cabang'], 403);
                }
                break;

            case 'Supervisor':
                // $pegawaiSupervisor = PegawaiSupervisor::where('id_user', $user->id)->first();
                if ($user) {
                    $idCabang = $user->id_cabang;
                    $idWilayah = $user->id_wilayah;
                    Log::info('Jabatan: supervisor - fetching nasabahs for cabang and wilayah', ['id_cabang' => $idCabang, 'id_wilayah' => $idWilayah]);
                    $query->where('id_cabang', $idCabang)->where('id_wilayah', $idWilayah);
                } else {
                    Log::error('Supervisor not found for Supervisor', ['user_id' => $user->id]);
                    return response()->json(['error' => 'Supervisor not found for this Supervisor'], 403);
                }
                break;

            case 'Admin Kas':
                // $pegawaiAdminKas = PegawaiAdminKas::where('id_user', $user->id)->first();
                if ($user) {
                    $idUser = $user->id;
                    Log::info('Jabatan: admin_kas - fetching nasabahs for admin_kas', ['id_admin_kas' => $idUser]);
                    $query->where('id_admin_kas', $idUser);
                } else {
                    Log::error('Admin Kas not found for Admin Kas', ['user_id' => $user->id]);
                    return response()->json(['error' => 'Admin Kas not found for this Admin Kas'], 403);
                }
                break;

            case 'Account Officer':
                // $pegawaiAccountOfficer = PegawaiAccountOffice::where('id_user', $user->id)->first();
                if ($user) {
                    $idUser = $user->id;
                    Log::info('Jabatan: account_officer - fetching nasabahs for account_officer', ['id_account_officer' => $idUser]);
                    $query->where('id_account_officer', $idUser);
                } else {
                    Log::error('Account Officer not found for Account Officer', ['user_id' => $user->id]);
                    return response()->json(['error' => 'Account Officer not found for this Account Officer'], 403);
                }
                break;

            default:
                Log::warning('Unauthorized access attempt', ['user_id' => $user->id, 'jabatan' => $jabatan]);
                return response()->json(['error' => 'Unauthorized'], 403);
        }

        // if ($request->has('search')) {
        //     $search = $request->search;
        //     Log::info('Search parameter provided', ['search' => $search]);
        //     $query->where(function ($q) use ($search) {
        //         $q->where('nama', 'LIKE', '%' . $search . '%')
        //             ->orWhereHas('cabang', function($q) use ($search) {
        //                 $q->where('nama_cabang', 'LIKE', '%' . $search . '%');
        //             });
        //     });
        // }
        if ($request->has('search')) {
            $search = $request->search;
            Log::info('Search parameter provided', ['search' => $search]);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('cabang', function($q) use ($search) {
                        $q->where('nama_cabang', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('accountofficer', function($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                    });
            });
        }
        
        Log::info('Executing query to fetch nasabahs');
        $nasabahs = $query->paginate($perPage);

        Log::info('Transforming nasabah data to include all Surat Peringatan');
        $nasabahs->getCollection()->transform(function($nasabah) {
            $allSuratPeringatan = $nasabah->suratPeringatan->map(function($suratPeringatan) {
                return [
                    'no' => $suratPeringatan->no,
                    'tingkat' => $suratPeringatan->tingkat,
                    'tanggal' => $suratPeringatan->tanggal,
                    'keterangan' => $suratPeringatan->keterangan,
                    'bukti_gambar' => $suratPeringatan->bukti_gambar,
                    'scan_pdf' => $suratPeringatan->scan_pdf,
                    'id_account_officer' => $suratPeringatan->user,
                ];
            });

            return [
                'no' => $nasabah->no,
                'nama' => $nasabah->nama,
                'pokok' => $nasabah->pokok,
                'bunga' => $nasabah->bunga,
                'denda' => $nasabah->denda,
                'total' => $nasabah->total,
                'keterangan' => $nasabah->keterangan,
                'ttd' => $nasabah->ttd,
                'kembali' => $nasabah->kembali,
                'cabang' => $nasabah->cabang->nama_cabang,
                'wilayah' => $nasabah->wilayah->nama_wilayah,
                'adminKas' => $nasabah->adminkas->name,
                'accountOfficer' => $nasabah->accountofficer->name,

                'suratPeringatan' => $allSuratPeringatan->toArray(), // Convert collection to array
            ];
        });

        Log::info('Customers fetched successfully', ['customers' => $nasabahs->toArray()]);
        return response()->json($nasabahs->toArray());
    }
}
