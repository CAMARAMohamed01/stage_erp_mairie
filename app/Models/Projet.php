<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $table = 'projet';
    protected $primaryKey = 'id_projet';
    protected $guarded = [];

    // --- Les relations Many-to-Many (Plusieurs-à-Plusieurs) ---

    // Un projet peut concerner plusieurs bâtiments (via la table pivot projet_batiment)
    public function batiments()
    {
        return $this->belongsToMany(Batiment::class, 'projet_batiment', 'id_projet', 'id_batiment');
    }

    // Un projet peut concerner plusieurs lieux publics
    public function lieuxPublics()
    {
        return $this->belongsToMany(LieuPublic::class, 'projet_lieu', 'id_projet', 'id_lieu');
    }

    // Un projet peut concerner plusieurs locaux
    public function locaux()
    {
        return $this->belongsToMany(Local::class, 'projet_local', 'id_projet', 'id_local');
    }
}