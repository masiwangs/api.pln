<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Prk extends Model
{
    protected $fillable = ['nama_project', 'no_prk', 'project_id', 'basket', 'created_by'];

    public function materials() {
        return $this->hasMany(PrkMaterial::class);
    }

    public function jasas() {
        return $this->hasMany(PrkJasa::class);
    }
}
