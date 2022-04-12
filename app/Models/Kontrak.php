<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    protected $fillable = [
        'nomor_kontrak',
        'tanggal_kontrak',
        'tanggal_awal',
        'tanggal_akhir',
        'pelaksana',
        'direksi_pelaksana',
        'pengadaan_id',
        'is_amandemen',
        'versi_amandemen',
        'amandemen_id',
        'basket_id'
    ];

    public function materials() {
        return $this->hasMany(KontrakMaterial::class);
    }

    public function jasas() {
        return $this->hasMany(KontrakJasa::class);
    }
}
