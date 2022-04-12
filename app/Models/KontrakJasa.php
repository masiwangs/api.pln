<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class KontrakJasa extends Model
{
    protected $fillable = [
        'nama_jasa',
        'harga',
        'kontrak_id'
    ];
}
