<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voie extends Model
{
    protected $table = 'voie';
    protected $primaryKey = 'id_voie';
    public $timestamps = false;
    protected $guarded = [];

    // Les tronçons qui composent cette voie
    public function troncons()
    {
        return $this->hasMany(Troncon::class, 'id_voie', 'id_voie')
            ->orderBy('pk_debut'); // On trie par Point Kilométrique
    }

    // Les ouvrages d'art liés à cette voie (ponts, murs)
    public function ouvrages()
    {
        return $this->hasMany(Ouvrage::class, 'id_voie', 'id_voie');
    }

}