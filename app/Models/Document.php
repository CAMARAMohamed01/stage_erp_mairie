<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'document';

    // ⚠️ CRUCIAL : Indiquer le nom de la clé primaire
    protected $primaryKey = 'id_document';

    public $timestamps = false;

    protected $fillable = [
        'nom_fichier',
        'type_doc',
        'chemin_stockage',
        'taille_ko',
        'date_upload',
        'id_compte',
        'id_tiers'
    ];

    public function physique()
    {
        return $this->hasOne(TiersPhysique::class, 'id_tiers', 'id_tiers');
    }

    // Relation Entreprise
    public function morale()
    {
        return $this->hasOne(TiersMorale::class, 'id_tiers', 'id_tiers');
    }

    public function compteBancaire()
    {
        return $this->belongsTo(CompteBancaire::class, 'id_compte', 'id_compte');
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