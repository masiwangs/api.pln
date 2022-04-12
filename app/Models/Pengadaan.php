<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Pengadaan extends Model
{
    protected $fillable = [
        'nomor_prk_skkis',
        'nodin',
        'tanggal_nodin',
        'no_pr',
        'nama_project',
        'status',
        'nomor_wbs_jasas',
        'nomor_wbs_materials'
    ];

    public function materials() {
        return $this->hasMany(PengadaanMaterial::class);
    }

    public function jasas() {
        return $this->hasMany(PengadaanJasa::class);
    }
}
