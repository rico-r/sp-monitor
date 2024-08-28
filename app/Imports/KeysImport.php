<?php
namespace App\Imports;

use App\Models\Key;
use Maatwebsite\Excel\Concerns\ToModel;

class KeysImport implements ToModel
{
    public function model(array $row)
    {
        return new Key([
            'key' => $row[0], // Kolom A (key)
            'jabatan' => $row[1], // Kolom B (jabatan)
        ]);
    }
}