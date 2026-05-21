<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compteur extends Model
{
    protected $table = 'compteur';
    protected $primaryKey = 'id_compteur';
    public $timestamps = false;
    protected $guarded = [];

    // Le local où est physiquement le compteur
    public function local()
    {
        return $this->belongsTo(Local::class, 'id_local', 'id_local');
    }

    // Le contrat de fourniture (Eau, EDF, Total...)
    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat', 'id_contrat');
    }

    // Si c'est un sous-compteur, voici son compteur principal
    public function compteurPrincipal()
    {
        return $this->belongsTo(Compteur::class, 'id_compteur_principal', 'id_compteur');
    }

    // Si c'est un compteur principal, voici ses sous-compteurs
    public function sousCompteurs()
    {
        return $this->hasMany(Compteur::class, 'id_compteur_principal', 'id_compteur');
    }

    // Relation future pour les relevés de consommation
    public function releves()
    {
        return $this->hasMany(ReleveCompteur::class, 'id_compteur', 'id_compteur');
    }
    // Les documents (notices, photos, plans) liés au compteur
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_compteur', 'id_compteur');
    }

    // Les interventions techniques réalisées sur ce compteur
    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'id_compteur', 'id_compteur');
    }
}