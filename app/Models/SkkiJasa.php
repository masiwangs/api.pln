<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkkiJasa extends Model
{
    protected $fillable = [
        'nama_jasa', 
        'harga', 
        'skki_id'
    ];
}
