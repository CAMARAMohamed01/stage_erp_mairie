<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImmobilisationInventaire extends Model
{
    protected $table = 'immobilisation_inventaire_';
    protected $primaryKey = 'id_immo';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'date_acquisition' => 'date',
        'date_sortie' => 'date',
        'est_amortissable' => 'boolean',
        'valeur_achat' => 'decimal:2',
        'valeur_revente' => 'decimal:2',
    ];

    // Relations Comptables
    public function ligneAchat()
    {
        return $this->belongsTo(LigneFinanciereFacture::class, 'id_ligne_achat', 'id_ligne');
    }

    public function ligneVente()
    {
        return $this->belongsTo(LigneFinanciereFacture::class, 'id_ligne_vente', 'id_ligne');
    }

    // Relations Patrimoniales (Biens adossés à cette immobilisation)
    public function batiments()
    {
        return $this->hasMany(Batiment::class, 'id_immo', 'id_immo');
    }

    public function parcelles()
    {
        return $this->hasMany(Parcelle::class, 'id_immo', 'id_immo');
    }

    public function lieuxPublics()
    {
        return $this->hasMany(LieuPublic::class, 'id_immo', 'id_immo');
    }

    public function equipements()
    {
        return $this->hasMany(Equipement::class, 'id_immo', 'id_immo');
    }
    public function articleCompta()
    {
        return $this->belongsTo(ArticleCompta::class, 'id_article', 'id_article');
    }
}