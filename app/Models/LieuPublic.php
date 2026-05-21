<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LieuPublic extends Model
{
    use HasFactory;

    protected $table = 'lieux_publics';
    protected $primaryKey = 'id_lieu';
    protected $guarded = [];

    // Relation inverse
    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'projet_lieu', 'id_lieu', 'id_projet');
    }
    public function contratsAdministratifs()
    {
        return $this->belongsToMany(\App\Models\Contrat::class, 'contrat_lieu', 'id_lieu', 'id_contrat');
    }

    // Les compteurs associés à ce lieu public (via les locaux de ce lieu)
    public function compteurs()
    {
        return $this->hasManyThrough(
            Compteur::class, // Modèle final qu'on veut
            Local::class,    // Modèle intermédiaire
            'id_lieu',       // Clé étrangère sur la table intermédiaire (local_)
            'id_local',      // Clé étrangère sur la table finale (compteur)
            'id_lieu',       // Clé locale sur la table de départ (lieux_publics)
            'id_local'       // Clé locale sur la table intermédiaire (local_)
        );
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_lieu', 'id_lieu');
    }
}