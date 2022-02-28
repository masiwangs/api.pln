<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['tahun', 'is_active', 'created_by'];

    public function prks() {
        return $this->hasMany(Prk::class);
    }
}
