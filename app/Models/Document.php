<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    // On force le nom singulier de la table
    protected $table = 'document';

    // On précise la clé primaire
    protected $primaryKey = 'id_document';

    // On désactive les timestamps (created_at, updated_at) si tu ne les as pas dans ta table
    public $timestamps = false;

    protected $guarded = [];

    public function physique()
    {
        return $this->hasOne(TiersPhysique::class, 'id_tiers', 'id_tiers');
    }

    // Relation Entreprise
    public function morale()
    {
        return $this->hasOne(TiersMorale::class, 'id_tiers', 'id_tiers');
    }

    // NOUVEAU : Relation vers les comptes bancaires
    public function comptesBancaires()
    {
        return $this->hasMany(CompteBancaire::class, 'id_tiers', 'id_tiers');
    }

    // NOUVEAU : Relation directe vers les documents du tiers
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_tiers', 'id_tiers');
    }

    // Relation vers l'historique des requêtes (Nomme-le bien selon ton modèle d'actions)
    public function actions()
    {
        return $this->hasMany(Action::class, 'id_tiers', 'id_tiers');
    }
}