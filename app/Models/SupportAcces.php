<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportAcces extends Model
{
    protected $table = 'support_acces';
    protected $primaryKey = 'id_support';
    public $timestamps = false;

    protected $fillable = [
        'numero_serie',
        'type_support',
        'est_actif',
        'observations'
    ];

    protected $casts = [
        'est_actif' => 'boolean',
    ];

    // Relation pour voir l'historique et les agents affectés à cette clé
    // On récupère les colonnes de la table pivot 'affectation'
    public function utilisateurs()
    {
        return $this->belongsToMany(Utilisateur::class, 'affectation', 'id_support', 'id_user')
            ->withPivot('date_remise', 'date_restitution', 'attestation_signee', 'commentaire');
    }

    // Raccourci pour récupérer l'agent qui possède ACTUELLEMENT la clé
    // (L'affectation où la date de restitution est nulle)
    public function affectationActuelle()
    {
        return $this->utilisateurs()->wherePivotNull('date_restitution')->first();
    }
}