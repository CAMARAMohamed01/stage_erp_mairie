<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $table = 'local_'; // Le fameux underscore !
    protected $primaryKey = 'id_local';
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
}