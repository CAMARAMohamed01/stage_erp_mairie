<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    use HasFactory;

    // Configuration de la table
    protected $table = 'contrat';
    protected $primaryKey = 'id_contrat';
    public $timestamps = false; // Désactivé si tu n'as pas created_at/updated_at dans ton SQL
    protected $guarded = [];

    // Les dates à "caster" automatiquement via Carbon pour faciliter l'affichage
    protected $casts = [
        'date_signature_contrat' => 'date',
        'date_debut_contrat' => 'date',
        'date_fin_contrat' => 'date',
        'date_echeance' => 'date',
        'revision_prix_prevue' => 'boolean'
    ];

    /**
     * RELATION 1 : Le contrat appartient à un Tiers (prestataire)
     */
    public function tiers()
    {
        return $this->belongsTo(Tiers::class, 'id_tiers');
    }

    /**
     * RELATION 2 : Un contrat génère plusieurs dossiers financiers (factures, devis)
     */
    public function dossiersFinanciers()
    {
        return $this->hasMany(DossierFinancier::class, 'id_contrat');
    }

    /**
     * RELATION 3 : Les opérations comptables liées (Table pivot operation_contrat)
     */
    public function operationsComptables()
    {
        return $this->belongsToMany(
            OperationComptable::class,
            'operation_contrat',
            'id_contrat',
            'id_operation'
        );
    }

    // Relation Many-to-Many avec les équipements couverts
    public function equipementsCouverts()
    {
        return $this->belongsToMany(
            Equipement::class,
            'contrat_equipement',
            'id_contrat',
            'id_equipement'
        );
    }

    public function batimentsCouverts()
    {
        return $this->belongsToMany(\App\Models\Batiment::class, 'contrat_batiment', 'id_contrat', 'id_batiment');
    }

    public function locauxCouverts()
    {
        return $this->belongsToMany(\App\Models\Local::class, 'contrat_local', 'id_contrat', 'id_local');
    }

    public function lieuxCouverts()
    {
        return $this->belongsToMany(\App\Models\LieuPublic::class, 'contrat_lieu', 'id_contrat', 'id_lieu');
    }
}