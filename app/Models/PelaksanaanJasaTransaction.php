<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelaksanaanJasaTransaction extends Model
{
    protected $fillable = [
        'nama_jasa', 
        'harga', 
        'kontrak_id',
    ];
}
