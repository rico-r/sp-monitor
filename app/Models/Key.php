<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
