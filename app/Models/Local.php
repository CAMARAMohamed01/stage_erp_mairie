<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $table = 'local_';
    protected $primaryKey = 'id_local';
    public $timestamps = false;
    protected $guarded = [];

    // Relation inverse : Connaître tous les projets qui ont impacté ce local
    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'projet_local', 'id_local', 'id_projet');
    }
    // Relation avec le bâtiment
    public function batiment()
    {
        return $this->belongsTo(\App\Models\Batiment::class, 'id_batiment', 'id_batiment');
    }
    public function contratsAdministratifs()
    {
        return $this->belongsToMany(\App\Models\Contrat::class, 'contrat_local', 'id_local', 'id_contrat');
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_local', 'id_local');
    }

    // Un local peut avoir plusieurs équipements
    public function equipements()
    {
        return $this->hasMany(Equipement::class, 'id_local', 'id_local');
    }

    // Un local peut avoir plusieurs compteurs (eau, électricité...)
    public function compteurs()
    {
        return $this->hasMany(Compteur::class, 'id_local', 'id_local');
    }
}