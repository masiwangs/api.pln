<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrkMaterial extends Model
{
    protected $fillable = [
        'kode_normalisasi', 
        'nama_material', 
        'jumlah', 
        'stok',
        'harga', 
        'satuan', 
        'prk_id'
    ];
}
