<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanMaterial extends Model
{
    protected $fillable = [
        'kode_normalisasi',
        'nama_material',
        'jumlah',
        'harga',
        'satuan',
        'skki_id'
    ];
}
