<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Key;


class Jabatan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_jabatan';

    public function users()
    {
        return $this->hasMany(User::class, 'jabatan_id', 'id_jabatan');
    }

    public function keys()
    {
        return $this->hasMany(Key::class, 'jabatan', 'id_jabatan'); // Sesuaikan nama kolom foreign key jika berbeda
    }
    

}
