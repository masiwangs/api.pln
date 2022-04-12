<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanJasa extends Model
{
    protected $fillable = [
        'nama_jasa', 
        'harga', 
        'skki_id'
    ];
}
