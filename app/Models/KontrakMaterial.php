<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class KontrakMaterial extends Model
{
    protected $fillable = [
        'kode_normalisasi',
        'nama_material',
        'harga',
        'jumlah',
        'satuan',
        'kontrak_id'
    ];
}
