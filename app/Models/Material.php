<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'kode_normalisasi', 
        'nama_material', 
        'deskripsi',
        'satuan',
        'harga',
    ];
}
