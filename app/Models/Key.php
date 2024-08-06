<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    use HasFactory;
    protected $fillable = [
        'key',
       
      

    ];
    public function users()
    {
        return $this->hasMany(User::class, 'key', 'key');
    }
}
