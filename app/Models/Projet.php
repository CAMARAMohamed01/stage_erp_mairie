<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $table = "projet";
    protected $primaryKey = 'id_projet';
    public $timestamps = false;
    protected $fillable = [
        'nom_projet',
        'budget_global_alloue',
        'annee_mandat',
        'avis',
        'id_axe',
        'id_user',
        'type_projet' // Le nouveau champ que nous avons ajouté
    ];
    // Un projet peut recevoir plusieurs arbitrages de commission
    public function decisions()
    {
        return $this->hasMany(DecisionCommission::class, 'id_projet', 'id_projet');
    }
    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'id_projet');
    }
    public function chefProjet()
    {
        return $this->belongsTo(Utilisateur::class, 'id_user');
    }
    // Un projet peut concerner plusieurs bâtiments (via la table pivot projet_batiment)
    // Bâtiments (N:N)
    public function batiments()
    {
        return $this->belongsToMany(Batiment::class, 'projet_batiment', 'id_projet', 'id_batiment', 'id_projet', 'id_batiment');
    }

    // Lieux Publics (N:N)
    public function lieuxPublics()
    {
        return $this->belongsToMany(LieuPublic::class, 'projet_lieu', 'id_projet', 'id_lieu', 'id_projet', 'id_lieu');
    }

    // Voies (N:N)
    public function voies()
    {
        return $this->belongsToMany(Voie::class, 'projet_voie', 'id_projet', 'id_voie', 'id_projet', 'id_voie');
    }

    // Lieux-dits / Quartiers (N:N)
    public function lieuxDits()
    {
        return $this->belongsToMany(LieuDit::class, 'projet_quartier', 'id_projet', 'id_lieu_dit', 'id_projet', 'id_lieu_dit');
    }

    // Un projet peut concerner plusieurs locaux
    public function locaux()
    {
        return $this->belongsToMany(Local::class, 'projet_local', 'id_projet', 'id_local');
    }
}