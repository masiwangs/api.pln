<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelaksanaanMaterialTransaction extends Model
{
    protected $fillable = [
        'kode_normalisasi', 
        'nama_material', 
        'harga',
        'jumlah',
        'satuan',
        'transaction',
        'tug9',
        'kontrak_id',
    ];
}
