<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Jabatan;


class Key extends Model
{
    use HasFactory;
    protected $primaryKey = 'key'; // Atur primary key jika perlu

    protected $fillable = [
        'key',
        'jabatan',
        'key_status'
       
      

    ];
    public function users()
    {
        return $this->hasMany(User::class, 'key', 'key');
    }

    public function jabatannama()
{
    return $this->belongsTo(Jabatan::class, 'jabatan'); // Ganti 'jabatan_id' jika nama foreign key berbeda
}
}
