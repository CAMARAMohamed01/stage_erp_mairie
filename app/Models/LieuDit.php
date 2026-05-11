<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LieuDit extends Model
{
    use HasFactory;

    protected $table = 'lieu_dit';
    protected $primaryKey = 'id_lieu_dit';
    protected $guarded = [];

    // Un lieu-dit regroupe plusieurs adresses et plusieurs parcelles
    public function adresses()
    {
        return $this->hasMany(Adresse::class, 'id_lieu_dit', 'id_lieu_dit');
    }

    public function parcelles()
    {
        return $this->hasMany(Parcelle::class, 'id_lieu_dit', 'id_lieu_dit');
    }
}