<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;
use App\Models\SuratPeringatan;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MobileSuratPeringatanController extends Controller
{
    public function getNasabahSP()
    {
        $user = Auth::user();
        
        // Pastikan pengguna ditemukan
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    
        // Periksa jabatan_id
        if ($user->jabatan_id == 99) {
            Log::info('Jabatan: admin - fetching all nasabahs');
            
            // Ambil semua data nasabah
            $nasabah = Nasabah::all();
            return response()->json($nasabah, 200);
        }
    
        // Pastikan PegawaiAccountOfficer ditemukan
        if ($user) {
            $idUser = $user->id;
            Log::info('Jabatan: account_officer - fetching nasabahs for account_officer', ['id_account_officer' => $idUser]);
            
            // Ambil data nasabah berdasarkan id_account_officer
            $nasabah = Nasabah::where('id_account_officer', $idUser)->get();
            return response()->json($nasabah, 200);
        } else {
            return response()->json(['error' => 'Account Officer not found for this user'], 403);
        }
    }

    public function SuratPeringatan(Request $request)
    {
        try {
            // Log data yang diterima
            Log::info('Data yang diterima untuk SuratPeringatan: ' . json_encode($request->all()));

            $validated = $request->validate([
                'no' => 'required|integer',
                'tingkat' => 'required|integer',
                'tanggal' => 'required|date',
                'bukti_gambar' => 'required|image|mimes:jpeg,png,jpg,gif', // Validate image file
                'scan_pdf' => 'required|mimes:pdf|max:2048', // Validate PDF file
                'id_account_officer' => 'required'
            ]);

            // Log file details before saving
            if ($request->hasFile('bukti_gambar')) {
                Log::info('bukti_gambar: ' . $request->file('bukti_gambar')->getClientOriginalName() . ', size: ' . $request->file('bukti_gambar')->getSize() . ' bytes');
            }
            if ($request->hasFile('scan_pdf')) {
                Log::info('scan_pdf: ' . $request->file('scan_pdf')->getClientOriginalName() . ', size: ' . $request->file('scan_pdf')->getSize() . ' bytes');
            }

            // Ambil tingkat untuk digunakan dalam nama file
            $tingkat = $validated['tingkat'];
            $namaNasabah = $validated['no'];

            // Cek apakah sudah ada tingkat yang lebih rendah yang sudah terisi
            for ($i = 1; $i < $tingkat; $i++) {
                $existingSuratPeringatan = SuratPeringatan::where('no', $validated['no'])
                    ->where('tingkat', $i)
                    ->first();

                if (!$existingSuratPeringatan) {
                    Log::info("Tingkat $i belum diisi untuk Nasabah No: " . $validated['no']);
                    return response()->json(['error' => "Tingkat $i belum diisi untuk Nasabah ini. Harap isi tingkat yang lebih rendah terlebih dahulu."], 422);
                }
            }

            // Simpan gambar dan PDF
            $buktiGambar = $request->file('bukti_gambar');
            $scanPdf = $request->file('scan_pdf');

            if ($buktiGambar->isValid() && $scanPdf->isValid()) {
                $buktiGambarName = 'gambar_SP' . $tingkat . '_' . $namaNasabah . '.' . $buktiGambar->getClientOriginalExtension();
                $scanPdfName = 'pdf_SP' . $tingkat . '_' . $namaNasabah . '.' . $scanPdf->getClientOriginalExtension();

                // Cek apakah sudah ada entri dengan nama gambar atau PDF yang sama
                $existingSuratPeringatan = SuratPeringatan::where('bukti_gambar', 'like', '%/' . $buktiGambarName)
                    ->first();

                if ($existingSuratPeringatan) {
                    Log::info('Data sudah ada untuk gambar atau PDF: ' . $buktiGambarName . ', ' . $scanPdfName);
                    return response()->json(['error' => 'Data sudah ada untuk Nasabah SP ini.'], 422);
                }

                $buktiGambarPath = $buktiGambar->storeAs('private/surat_peringatan', $buktiGambarName);
                $scanPdfPath = $scanPdf->storeAs('private/surat_peringatan', $scanPdfName);

                Log::info('File gambar berhasil disimpan: ' . $buktiGambarName);
                Log::info('File PDF berhasil disimpan: ' . $scanPdfName);
            } else {
                Log::error('File gambar atau PDF tidak valid');
                throw new \Exception('File gambar atau PDF tidak valid');
            }

            // Buat record surat peringatan
            $suratPeringatan = SuratPeringatan::create([
                'no' => $validated['no'],
                'tingkat' => $validated['tingkat'],
                'tanggal' => $validated['tanggal'],
                'bukti_gambar' => $buktiGambarPath,
                'scan_pdf' => $scanPdfPath,
                'id_account_officer' => $validated['id_account_officer'],
            ]);

            Log::info('Surat peringatan berhasil dibuat: ' . json_encode($suratPeringatan));

            return response()->json($suratPeringatan, 201);
        } catch (ValidationException $e) {
            // Log pesan validasi
            Log::error('Validasi gagal: ' . $e->getMessage());

            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log pesan error umum
            Log::error('Terjadi kesalahan saat menyimpan surat peringatan: ' . $e->getMessage());

            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan surat peringatan.'], 500);
        }
    }

    public function serveImage($filename)
    {
        $path = storage_path('app/private/surat_peringatan/' . $filename);
        if (file_exists($path)) {
            Log::info("Serving image from path: " . $path);
            return response()->file($path);
        } else {
            Log::error("Image not found at path: " . $path);
            return response()->json(['error' => 'Image not found'], 404);
        }
    }
    
    public function servePdf($filename)
    {
        $path = storage_path('app/private/surat_peringatan/' . $filename);
        if (file_exists($path)) {
            Log::info("Serving PDF from path: " . $path);
            return response()->file($path);
        } else {
            Log::error("PDF not found at path: " . $path);
            return response()->json(['error' => 'PDF not found'], 404);
        }
    }
}
