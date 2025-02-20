<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Nip;
use App\Models\Jabatan;
use App\Models\Wilayah;
use App\Models\PegawaiAdminKas;
use App\Models\PegawaiSupervisor;
use Laravel\Sanctum\HasApiTokens;
use App\Models\PegawaiKepalaCabang;
use App\Events\UserRegisteredMobile;
use App\Models\PegawaiAccountOffice;
use Illuminate\Notifications\Notifiable;

class Nasabah extends Model
{
    use HasFactory;
    protected $fillable = [
        'no',
        'nama',
        'pokok',
        'bunga',
        'denda',
        'total',
        'account_officer',
        'keterangan',
        'ttd',
        'kembali',
        'id_cabang',
        'id_wilayah',
        'id_account_officer',
        'id_admin_kas',
    ];
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'id_wilayah');
    }
    public function adminkas()
    {
        return $this->belongsTo(PegawaiAdminKas::class, 'id_admin_kas');
    }
    public function accountofficer()
    {
        return $this->belongsTo(PegawaiAccountOffice::class, 'id_account_officer');
    }
    public function suratPeringatan()
    {
        return $this->hasMany(SuratPeringatan::class, 'no' ,'no');
    }

    // Relasi-relasi lain yang mungkin dimiliki oleh Nasabah
    protected $primaryKey = 'no';
}
