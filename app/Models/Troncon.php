<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Troncon extends Model
{
    use HasFactory;

    protected $table = 'troncon';
    protected $primaryKey = 'id_troncon';
    protected $guarded = [];

    // Le tronçon appartient à un réseau global
    public function reseau()
    {
        return $this->belongsTo(Reseau::class, 'id_reseau', 'id_reseau');
    }
    public function voie()
    {
        return $this->belongsTo(Voie::class, 'id_voie', 'id_voie');
    }

    public function ouvrageLie()
    {
        return $this->belongsTo(Ouvrage::class, 'id_ouvrage_lie', 'id_ouvrage');
    }

    public function equipements()
    {
        return $this->hasMany(Equipement::class, 'id_troncon', 'id_troncon');
    }
    //interventions
    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'id_troncon', 'id_troncon');
    }
    /**
     * Obtenir toutes les pièces jointes et documents liés à ce tronçon de voie
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_troncon', 'id_troncon');
    }
}