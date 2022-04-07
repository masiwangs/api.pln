<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Skki extends Model
{
    protected $fillable = [
        'nomor_skki', 
        'nomor_prk_skki', 
        'nomor_wbs_jasa',
        'nomor_wbs_material',
        'prks', 
        'basket',
    ];

    public function materials() {
        return $this->hasMany(SkkiMaterial::class);
    }

    public function jasas() {
        return $this->hasMany(SkkiJasa::class);
    }
}
